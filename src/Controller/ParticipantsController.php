<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Notification\iscrizioneOkNotification;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Notifications\Plugin;

/**
 * Participants Controller
 *
 * @property \App\Model\Table\ParticipantsTable $Participants
 *
 * @method \App\Model\Entity\Participant[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ParticipantsController extends AppController
{

  public $paginate = [
    'contain' => ['Events'],
  ];

  /**
   * View method
   *
   * @param string|null $id Participant id.
   * @return \Cake\Http\Response|void
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function view($id = null)
  {
    $participant = $this->Participants->get($id, [
      'contain' => ['Events']
    ]);

    $this->set('participant', $participant);
  }

  /**
   * Add method
   *
   * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
   */
  public function add()
  {
    $participant = $this->Participants->newEmptyEntity();
    if ($this->request->is('post')) {
      if (isset($this->request->getData()['referal'])) {
        $referal = $this->request->getData()['referal'];
      } else {
        $referal = $this->referer();
      }

      $participant = $this->Participants->patchEntity($participant, $this->request->getData());

      //Se siete troppi ti rimando indietro
      if ($this->Participants->checkMaxPax($participant)) {
        $message = __("L'evento è al completo ti chiediamo di scegliere un altro evento.");
        $responseData = ['message' => $message, 'success' => false];
      } else {
        //Se il partecipante ci sta ancora, provo a salvare.
        if ($this->Participants->save($participant)) {
          $message = __("Ti abbiamo iscritto corretamente all'evento. Controlla la posta!");
          $responseData = ['message' => $message, 'success' => true];

          //Mi servono i dati dell'evento
          try {
            $event = $this->Participants->Events->get($participant->event_id);
          } catch (\Cake\Core\Exception\Exception $e) {
            $this->Flash->error("impossibile recuperare i dati dell'evento per mandare una mail al destinatario");
            return;
          }

          //Mando una mail di notifica
          $n = new iscrizioneOkNotification($participant, $event);
          $n->toMail();
        } else {
          $message = __("Ci dev'essere qualche errore, per favore controlla i messaggi e riprova a salvare.");
          $responseData = ['message' => $message, 'success' => false];
        }
      }

      //Mando la risposta
      if ($this->request->isAjax()) {
        // Specify which view vars JsonView should serialize.
        $this->set('responseData', $responseData);
        $this->set('_serialize', 'responseData');
        $this->response->getStatusCode(200);
        $this->RequestHandler->renderAs($this, 'json');
      } else {
        if ($responseData['success']) {
          $this->Flash->success($message);
        } else {
          $this->Flash->error($message);
        }
        return $this->redirect($referal);
      }
    }
    $events = $this->Participants->Events->find('list', ['limit' => 200]);
    $this->set(compact('participant', 'events'));
  }
}
