<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 *
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EventsController extends AppController
{
    //Necessario per gestire la risposta in json della view
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('getList');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $q = $this->request->query('q');
        $query = $this->Events->find()
			->contain(['Destinations']);
		//Se mi hai passato dei parametri in query filtro su quelli
		if (!empty($q)) {
            $query->where(['title LIKE' => "%$q%"]);
        }
        $events = $this->paginate($query);

        $this->set(compact('events','q'));
    }

    /**
     * View method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => ['Destinations', 'Users', 'Participants']
        ]);

        $this->set('event', $event);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $event = $this->Events->newEntity();
        if ($this->request->is('post')) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $destinations = $this->Events->Destinations->find('list', ['limit' => 200]);
        $users = $this->Events->Users->find('list', ['limit' => 200]);
        $this->set(compact('event', 'destinations', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $destinations = $this->Events->Destinations->find('list', ['limit' => 200]);
        $users = $this->Events->Users->find('list', ['limit' => 200]);
        $this->set(compact('event', 'destinations', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $event = $this->Events->get($id);
        if ($this->Events->delete($event)) {
            $this->Flash->success(__('The event has been deleted.'));
        } else {
            $this->Flash->error(__('The event could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function getList($allowedEvents = null)
    {
        $conditions = [];
        if (!empty($allowedEvents))
        {
            $conditions['event_id'] = $allowedEvents;
        }
        $events = $this->Events->find('all', ['fields'=>['id','title'],'limit' => 200, 'conditions'=>$conditions]);
        $this->set('events', $events);

        //Mando la risposta in ajax
        if ($this->request->isAjax())
        {
            $this->set('_serialize', 'events');
            $this->RequestHandler->renderAs($this, 'json');
        }
    }

    //Metodo per far iscrivere gli utenti ad un evento
    public function subscribe($slug)
    {
        $event = $this->Events->findBySlug($slug)
                ->firstOrFail();

        $siti = $this->Events->Destinations->find('list',[
                'conditions' => ['show_in_list' => 1, 'chiuso'=>0],
                'order' => 'Name'
        ]);
        $this->set('siti', $siti);
        $this->set('event', $event);
    }
}
