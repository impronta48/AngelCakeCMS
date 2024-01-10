<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
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
    //$this->autoRender = false;
    if ($this->request->is('post')) {
      $d = $this->request->getData();

      //honeypot
      if (isset($d['admin_email']) && $d['admin_email'] != '') {
        return;
      }

      //next
      $referer = $this->request->referer();
      if (isset($d['_next']) && $d['_next'] != '') {
        $referer = $d['_next'];
      } 
      if (empty($referer)) {
        $referer = Router::url('/', true);
      } 

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
        $sender = ['info@mobilitysquare.eu' => 'MobilitySquare - Messaggio dal Sito Web'];
      }
      $msg = "Hai ricevuto un messaggio dal sito: " . env("SERVER_NAME") . "\n\r";
      foreach ($d as $k => $m) {
        if (is_array($m)) {
          $msg .=json_encode($m) . "\n\r";
          
        }
       else {
            $msg .= "<b>$k</b>: $m \n\r";
        }       
      }

      $msg = nl2br($msg);
      $mailer->setFrom($sender)
        ->setEmailFormat('html')
        ->setReplyTo($d['_replyto'])
        ->setTo($destination)
        ->setSubject('Messaggio dal Web')
        ->deliver($msg);

      $this->Flash->success('Grazie, ti risponderemo al più presto.');
      $this->set(compact('referer'));
    }
  }
}
