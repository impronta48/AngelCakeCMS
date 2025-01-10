<?php

declare(strict_types=1);

namespace App\Controller;

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

  public function initialize(): void
  {
    parent::initialize();
    $this->Authentication->allowUnauthenticated(['getList', 'subscribe']);
  }


  public function getList($allowedEvents = null)
  {
    $conditions = [];
    if (!empty($allowedEvents)) {
      $conditions['event_id'] = $allowedEvents;
    }
    $events = $this->Events->find('all', ['fields' => ['id', 'title'], 'limit' => 200, 'conditions' => $conditions]);
    $this->set('events', $events);

    //Mando la risposta in ajax
    if ($this->request->isAjax()) {
      $this->set('_serialize', 'events');
      $this->RequestHandler->renderAs($this, 'json');
    }
  }

  //Metodo per far iscrivere gli utenti ad un evento
  public function subscribe($id)
  {
    $event = $this->Events->findById($id)
      ->firstOrFail();

    $siti = $this->Events->Destinations->find('list', [
      'conditions' => ['published' => 1/*, 'chiuso' => 0*/],
      'order' => 'Name',
    ]);
    $this->set('siti', $siti);
    $this->set('event', $event);
  }
}
