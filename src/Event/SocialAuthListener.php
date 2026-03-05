<?php
namespace App\Event;

use ADmad\SocialAuth\Middleware\SocialAuthMiddleware;
use Authentication\Authenticator\CookieAuthenticator;
use Authentication\PasswordHasher\PasswordHasherTrait;
use Cake\Core\Configure;
use App\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Http\Response;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Google\Cloud\Core\Exception\NotFoundException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class SocialAuthListener implements EventListenerInterface
{
    use LocatorAwareTrait;
    use PasswordHasherTrait;

    public function implementedEvents(): array
    {
        return [
            //SocialAuthMiddleware::EVENT_AFTER_IDENTIFY => 'afterIdentify',
            SocialAuthMiddleware::EVENT_BEFORE_REDIRECT => 'beforeRedirect',
            // Uncomment below if you want to use the event listener to return
            // an entity for a new user instead of directly using `createUser()` table method.
            // SocialAuthMiddleware::EVENT_CREATE_USER => 'createUser',
        ];
    }

    public function afterIdentify(EventInterface $event, EntityInterface $user)
    {   
        // Create a custom response
        $response = new Response();
        

        // Stop further event propagation
        $event->stopPropagation();

        // Set the response on the controller
        //$controller = $event->getSubject();
        //$controller->setResponse($response);

                  
        $users = $this->getTableLocator()->get('Users');
        $identity = $users->find()->where(['id'=>$user->id])->first();
        $response = $this->_returnHttpOnlyCookies($identity, $response);

        return $response;
    }

     public function beforeRedirect(EventInterface $event, $url, string $status, ServerRequest $request): string
    {
        // Set flash message
        switch ($status) {
            case SocialAuthMiddleware::AUTH_STATUS_SUCCESS:
                break; 

            // Auth through provider failed. Details will be logged in
            // `error.log` if `logErrors` option is set to `true`.
            case SocialAuthMiddleware::AUTH_STATUS_PROVIDER_FAILURE:

            // Table finder failed to return user record. An e.g. of this is a
            // user has been authenticated through provider but your finder has
            // a condition to not return an inactivated user.
            case SocialAuthMiddleware::AUTH_STATUS_FINDER_FAILURE:
                $request->getFlash()->error('Authentication failed.');
                break;

            case SocialAuthMiddleware::AUTH_STATUS_IDENTITY_MISMATCH:
                $request->getFlash()->error('The social profile is already linked to another user.');
                break;
        }

        return "/users/jwtLogin";
                

        // You can return a modified redirect URL if needed.
    }


    /**
     * Creates a full cookie token serialized as a JSON sting.
     *
     * Cookie token consists of a username and hashed username + password hash.
     *
     * @param array|\ArrayAccess $identity Identity data.
     * @return string
     */
    protected function _createToken($identity): string
    {
        $plain = $this->_createPlainToken($identity);
        $hash = $this->getPasswordHasher()->hash($plain);

        return json_encode([$identity->username, $hash]);
    }

    /**
     * Creates a cookie instance with configured defaults.
     *
     * @param mixed $value Cookie value.
     * @return \Cake\Http\Cookie\CookieInterface
     */
    protected function _createCookie($name, $value): CookieInterface
    {        
       $options = [
            'expires' => '2 weeks',
            'http' => true,
            'secure' => false,
       ];
       
        $cookie = Cookie::create(
            $name,
            $value,
            $options
        );

        return $cookie;
    }

    /**
     * Creates a plain part of a cookie token.
     *
     * Returns concatenated username, password hash, and HMAC signature.
     *
     * @param array|\ArrayAccess $identity Identity data.
     * @return string
     */
    protected function _createPlainToken($identity): string
    {
        $usernameField = 'username';
        $passwordField = 'password';

        $salt = Security::getSalt();
        $value = $identity[$usernameField] . $identity[$passwordField];

        if ($salt === false) {
            return $value;
        }
        if (!is_string($salt) || $salt === '') {
            throw new InvalidArgumentException('Salt must be a non-empty string.');
        }

        $hmac = hash_hmac('sha1', $value, $salt);
        // Instead of appending the plain salt, we create a hash. This limits the chance of the salt being leaked.

        return $value . $hmac;
    }


    /** dopo un login di successo restituisce i cookies httponly con i token e il refresh */
    private function _returnHttpOnlyCookies($entity, Response $response): Response
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $identity = $entity->get('id');
        $user = new User();
        $token = $user->getToken($identity);


        // Save refresh token to database
        $userRecord = $Users->get($identity);
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


        $Users->save($userRecord);

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
            null, // dominio (null = automatico)
            true, // secure
            true, // httpOnly
            'Lax' // SameSite
        );

        // Imposta cookie HttpOnly + Secure per refresh token (30 giorni)
        $refreshCookie = new Cookie(
            'jwt_refresh_token',
            $refreshToken,
            new \DateTime('+30 days'), // 30 giorni
            '/',
            null, // dominio (null = automatico)
            true, // secure
            true, // httpOnly
            'Lax' // SameSite
        );

        $userCookie = new Cookie(
            'user',
            json_encode($entity),
            new \DateTime('+30 days'), // 30 giorni
            '/',
            null, // dominio (null = automatico)
            false, // secure
            false, // httpOnly
            'Lax' // SameSite
        );

        // Aggiungi i cookie alla response
        $response = $response
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

        //Log::write('info', $message, ['scope' => ['login']]);

        return $response;
        //$this->redirect("http://localhost:3000"); 
    }




}
