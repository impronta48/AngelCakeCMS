<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Log\Log;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

  public function beforeFilter(\Cake\Event\EventInterface $event)
  {
    parent::beforeFilter($event);
    $this->loadComponent('Authentication.Authentication');
    $this->Authentication->allowUnauthenticated(['login','add']);
  }

  public function login()
  {
    $result = $this->Authentication->getResult();
    // If the user is logged in send them away.
    if ($result->isValid()) {
      $target = $this->Authentication->getLoginRedirect() ?? '/admin';
      return $this->redirect($target);
    }
    if ($this->request->is('post') && !$result->isValid()) {
      $this->Flash->error('Invalid username or password');
    }
  }

  public function logout()
  {
    $this->Authentication->logout();
    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
  }


  public function add()
  {
    $user = $this->Users->newEmptyEntity();
    if ($this->request->is('post')) {
      $user = $this->Users->patchEntity($user, $this->request->getData());
      if ($this->Users->save($user)) {
        $this->Flash->success(__('The user has been saved.'));
        return $this->redirect(['action' => 'add']);
      }
      $this->Flash->error(__('Unable to add the user.'));
    }
    $this->set('user', $user);
  }

  public function jwtLogin(){
    
    $identity = $this->request->getAttribute('identity');

    $userId = $identity->getIdentifier(); // usually the primary key
   

    $users = $this->getTableLocator()->get('Users');
    $identity = $users->find()->where(['id'=>$userId])->first();
    $response = $this->_returnHttpOnlyCookies($identity, $this->response);

    if ($identity->get('group_id') == 1)
      return $this->redirect('/admin');
      
    return $this->redirect('/viaggi/index');

    

  }

   private function _returnHttpOnlyCookies($entity): Response
    {
        $identity = $entity->get('id');
        $user = new User();
        $token = $user->getToken($identity);


        // Save refresh token to database
        $userRecord = $this->Users->get($identity);
        if ($userRecord == null) {
            throw new NotFoundException('Utente non trovato');
        }

        // Se il token è scaduto ne genero uno nuovo e lo salvo,
        // altrimenti riuso il refresh token esistente
        $refreshToken = null;
        $expired = true;
        if (!empty($userRecord->refresh_token_expires)) {
            try {
                if ($userRecord->refresh_token_expires instanceof \DateTimeInterface) {
                    $expired = $userRecord->refresh_token_expires->getTimestamp() < time();
                } else {
                    $expiresDt = new \DateTime((string)$userRecord->refresh_token_expires);
                    $expired = $expiresDt->getTimestamp() < time();
                }
            } catch (\Exception $e) {
                // se non riesco a parsare la data considerala scaduta
                $expired = true;
            }
        }

        if ($expired || empty($userRecord->refresh_token)) {
            // Generate and save a new refresh token
            $refreshToken = $user->getRefreshToken($identity);
            $userRecord->refresh_token = $refreshToken;
            $userRecord->refresh_token_expires = date('Y-m-d H:i:s', time() + User::REFRESH_TOKEN_MONTH_LIVE);
        } else {
            // Reuse existing refresh token
            $refreshToken = $userRecord->refresh_token;
        }


        $this->Users->save($userRecord);

        $data = [
            'access_token' => $token,
            'refresh_token' => $refreshToken
        ];

        // Imposta cookie HttpOnly + Secure per access token
        $accessCookie = new Cookie(
            'jwt_token',
            $token,
            new \DateTime('+60 minutes'), // 60 minuti
            '/',
            ".cribyoo.it", // dominio (null = automatico)
            true, // secure
            true, // httpOnly
            'None' // SameSite
        );

        // Imposta cookie HttpOnly + Secure per refresh token (30 giorni)
        $refreshCookie = new Cookie(
            'jwt_refresh_token',
            $refreshToken,
            new \DateTime('+30 days'), // 30 giorni
            '/',
            ".cribyoo.it", // dominio (null = automatico)
            true, // secure
            true, // httpOnly
            'None' // SameSite
        );

        $userCookie = new Cookie(
            'user',
            json_encode($entity),
            new \DateTime('+30 days'), // 30 giorni
            '/',
            ".cribyoo.it", // dominio (null = automatico)
            true, // secure
            true, // httpOnly
            'None' // SameSite
        );

        // Aggiungi i cookie alla response
        $this->response = $this->response
            ->withStringBody(json_encode(['success' => true, 'user' => $entity]))
            ->withType('application/json')
            ->withCookie($accessCookie)
            ->withCookie($refreshCookie)
            ->withCookie($userCookie);

        $message = sprintf(
            'User %s (ID: %d) with role %s logged',
            $entity->username,
            $entity->id,
            $entity->group_id,
        );

        Log::write('info', $message, ['scope' => ['login']]);

        return $this->response;
    }


}
