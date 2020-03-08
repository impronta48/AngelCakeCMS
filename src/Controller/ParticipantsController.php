<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;


/**
 * Participants Controller
 *
 * @property \App\Model\Table\ParticipantsTable $Participants
 *
 * @method \App\Model\Entity\Participant[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ParticipantsController extends AppController
{
    //Necessario per gestire la risposta in json della view
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        //$this->loadComponent('Security');
        $this->Auth->allow('add');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($event_id = null)
    {
        $conditions = [];
        $ext = $this->request->getAttribute('params')['_ext'];

        if (!empty($event_id))
        {
            $conditions['event_id'] = $event_id;
        }

        if ($ext == 'xls'){
            $participants = $this->Participants->find('all',['conditions'=>$conditions]);
        }
        else{
            $this->paginate = [
                'contain' => ['Events'],
                'conditions' => $conditions,
            ];
            $participants = $this->paginate($this->Participants);
        }

        $columns = $this->Participants->schema()->columns();
        $this->set(compact('participants','event_id','columns'));
    }

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
        $participant = $this->Participants->newEntity();
        if ($this->request->is('post')) {
            if (isset($this->request->getData()['referal']))
            {
                $referal = $this->request->getData()['referal'];
            }
            else
            {
                $referal = $this->referer();
            }

            $participant = $this->Participants->patchEntity($participant, $this->request->getData());

            //Se siete troppi ti rimando indietro
            if ($this->Participants->checkMaxPax($participant))
            {
                $message = __("L'evento è al completo ti chiediamo di scegliere un altro evento.");
                $responseData = ['message' => $message, 'success'=>false];
            }
            else {
                //Se il partecipante ci sta ancora, provo a salvare.
                if ($this->Participants->save($participant)) {
                $message = __("Ti abbiamo iscritto corretamente all'evento. Controlla la posta!");
                $responseData = ['message' => $message, 'success'=>true];
                //Mando una mail di notifica
                $this->sendNotification($participant);
                }else {
                    $message = __("Ci dev'essere qualche errore, per favore controlla i messaggi e riprova a salvare.");
                    $responseData = ['message' => $message, 'success'=>false];
                }
            }

            //Mando la risposta
            if ($this->request->isAjax())
            {
                // Specify which view vars JsonView should serialize.
                $this->set('responseData', $responseData);
                $this->set('_serialize', 'responseData');
                $this->response->getStatusCode(200);
                $this->RequestHandler->renderAs($this, 'json');
            }
            else
            {
                if ($responseData['success'])
                {
                    $this->Flash->success($message);
                }
                else
                {
                    $this->Flash->error($message);
                }
                return $this->redirect($referal);
            }
        }
        $events = $this->Participants->Events->find('list', ['limit' => 200]);
        $this->set(compact('participant', 'events'));
    }

    //Manda una mail di notifica al partecipante
    private function sendNotification($participant)
    {
         //Mi servono i dati dell'evento
        try{
            $event= $this->Participants->Events->get($participant->event_id);
        }
        catch (Exception $e) {
            $this->Flash->error('impossibile mandare una mail al destinatario');
            return;
        }

        //Imposto una mail dell'organizzatore di default
        if(empty($event->organizer_email))
        {
            $event->organizer_email='segreteria@yepp.it';
        }

        //Spedisco davvero la mail
        $email = new Email('default');
        $email->viewBuilder()->setTemplate('iscrizione_ok');
        $email->setFrom($event->organizer_email)
            ->setEmailFormat('html')
            ->setTo($participant->email)
            ->setCc($event->organizer_email)
            ->setSubject("Iscrizione a {$event->title}")
            ->setViewVars(['event'=>$event, 'participant'=>$participant])
            ->send();
    }

    /**
     * Edit method
     *
     * @param string|null $id Participant id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $participant = $this->Participants->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $participant = $this->Participants->patchEntity($participant, $this->request->getData());
            if ($this->Participants->save($participant)) {
                $this->Flash->success(__('The participant has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participant could not be saved. Please, try again.'));
        }
        $events = $this->Participants->Events->find('list', ['limit' => 200]);
        $destinations = TableRegistry::getTableLocator()->get('Destinations')->find('list', ['limit' => 200]);
        $this->set(compact('participant', 'events','destinations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Participant id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $participant = $this->Participants->get($id);
        if ($this->Participants->delete($participant)) {
            $this->Flash->success(__('The participant has been deleted.'));
        } else {
            $this->Flash->error(__('The participant could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
