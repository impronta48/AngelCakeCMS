<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Destination;
use function Psy\debug;
use Cake\Routing\Router;


/**
 * Destinations Controller
 *
 * @property \App\Model\Table\DestinationsTable $Destinations
 *
 * @method \App\Model\Entity\Destination[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DestinationsController extends AppController
{
    public $paginate = [
        'limit' => 12,
    ];

    public function initialize(): void
    {
        parent::initialize();
        //$this->Authentication->allowUnauthenticated(['index','view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $destinations = $this->paginate($this->Destinations, [
            'contain' => ['Articles'],
            'conditions' => ['show_in_list' => TRUE],
            'order' => ['chiuso ASC', 'name'] ,
            'limit' => 100,
        ] );
        $this->set(compact('destinations'));
    }

    public function admin()
    {
        $destinations = $this->paginate($this->Destinations, [
            'contain' => ['Articles'],
        ] );


        $this->set(compact('destinations') );
    }

    /**
     * View method
     *
     * @param string|null $id Destination id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $query = $this->Destinations->find();
        $a = $this->request->getQuery('archive');

        $articles_q = $this->Destinations->Articles->find()
                        ->order( ['modified' => 'DESC']);

        if (empty($a)) {
            $articles_q->where(['Articles.archived' => false]);
        }
        else {
            $articles_q->where(['Articles.archived' => true]);
        }

        if (is_string($id))
        {
            try {
                $id = $this->Destinations->findBySlug($id)->firstOrFail()->id;

            } catch (\Cake\Datasource\Exception\RecordNotFoundException $ex) {
                $this->log(sprintf('Record not found in database (id = %d)!', $id), LogLevel::WARNING);
            }
        }


        $query->where(['id'=>$id]);                 //Filtro le destination
        $destination = $query->first();
        $this->set('destination', $destination);

        $articles_q->where(['destination_id'=>$id]); //Filtro gli articoli
        $articles_q->where(['published'=>true]); //Mostro solo quelli pubblicati
        $art = $this->paginate($articles_q);
        $this->set('articles', $art);

        $this->set('archived',$a);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $destination = $this->Destinations->newEmptyEntity();
        if ($this->request->is('post')) {
            $destination = $this->Destinations->patchEntity($destination, $this->request->getData());
            if ($this->Destinations->save($destination)) {
                $this->Flash->success(__('The destination has been saved.'));

                $this->redirect(Router::url( $this->referer(), true ) );
            }
            $this->Flash->error(__('The destination could not be saved. Please, try again.'));
        }
        $this->set(compact('destination'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Destination id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $destination = $this->Destinations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $destination = $this->Destinations->patchEntity($destination, $this->request->getData());
            if ($this->Destinations->save($destination)) {
                $this->Flash->success(__('The destination has been saved.'));

                return $this->redirect(['action' => 'admin']);
            }
            $this->Flash->error(__('The destination could not be saved. Please, try again.'));
        }
        $this->set(compact('destination'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Destination id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $destination = $this->Destinations->get($id);
        if ($this->Destinations->delete($destination)) {
            $this->Flash->success(__('The destination has been deleted.'));
        } else {
            $this->Flash->error(__('The destination could not be deleted. Please, try again.'));
        }

        $this->redirect(Router::url( $this->referer(), true ) );
    }
}
