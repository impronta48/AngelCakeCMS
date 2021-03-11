<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Exception;

class ContactsController extends AppController
{
  //Necessario per gestire la risposta in json della view
  public function initialize(): void
  {
    parent::initialize();
    $this->Authentication->allowUnauthenticated(['index']);
  }

  public function index($destination)
  {
    $this->autoRender = false;
    if ($this->request->is('post')) {
      $d = $this->request->getData();

      if (!filter_var($destination, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email del sito non valida");
      }

      if (!filter_var($d['_replyto'], FILTER_VALIDATE_EMAIL) && !empty($d['_replyto'])) {
        return $this->Flash->error('Email destinatario non valida.');
        //throw new Exception("Email destinatario non valida");
      }

      $mailer = new Mailer('default');
      $sender = Configure::read('MailAdmin');
      if (empty($sender)) {
        $sender = ['info@impronta48.it' => 'iMpronta - WebFORM'];
      }
      $msg = "Hai ricevuto un messaggio dal sito: " . env("SERVER_NAME") . "\n\r";
      foreach ($d as $k => $m) {
        $msg .= "$k: $m \n\r";
      }
      $mailer->setFrom($sender)
        ->setReplyTo($d['_replyto'])
        ->setTo($destination)
        ->setSubject('Messaggio dal Web')
        ->deliver($msg);

      $this->Flash->success('Grazie, ti risponderemo al più presto.');
      $this->redirect($this->referer());
    }
  }
}
