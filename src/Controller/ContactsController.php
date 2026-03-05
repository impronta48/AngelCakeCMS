<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use Exception;

class ContactsController extends AppController
{
  //Necessario per gestire la risposta in json della view

  public function initialize(): void
  {
    parent::initialize();
    $this->Authentication->allowUnauthenticated(['index','test']);
  }

  public function index($destination)
  {
    $this->autoRender = false;
    
    if ($this->request->is('post')) {
      $d = $this->request->getData();

      //honeypot
      if (isset($d['admin_email']) && $d['admin_email'] != '') {
        return $this->response
          ->withType('application/json')
          ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid request']));
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
        return $this->response
          ->withType('application/json')
          ->withStatus(400)
          ->withStringBody(json_encode(['success' => false, 'message' => 'Email del sito non valida']));
      }

      if (isset($d['_replyto']) && !filter_var($d['_replyto'], FILTER_VALIDATE_EMAIL) && !empty($d['_replyto'])) {
        return $this->response
          ->withType('application/json')
          ->withStatus(400)
          ->withStringBody(json_encode(['success' => false, 'message' => 'Email destinatario non valida']));
      }

      $mailer = new Mailer('default');
      $sender = Configure::read('MailAdmin');
      if (empty($sender)) {
        $sender = ['info@mobilitysquare.eu' => 'MobilitySquare - Messaggio dal Sito Web'];
      }
      $msg = "Hai ricevuto un messaggio dal sito: " . env("HTTP_REFERER") . "\n\r";
      foreach ($d as $k => $m) {
        if (is_array($m)) {
          $msg .= json_encode($m) . "\n\r";
        } else {
          $msg .= "<b>$k</b>: $m \n\r";
        }       
      }

      $msg = nl2br($msg);
      if (empty($msg)) {
        return $this->response
          ->withType('application/json')
          ->withStatus(400)
          ->withStringBody(json_encode(['success' => false, 'message' => 'Messaggio vuoto']));
      }

      try {
        $mailer->setFrom($sender)
          ->setEmailFormat('both');

        if (isset($d['_replyto']) && !empty($d['_replyto'])) {
          $mailer->setReplyTo($d['_replyto']);
        } 
        
        $mailer->setTo($destination)
          ->setSubject(isset($d['_subject']) ? $d['_subject'] : 'Messaggio dal Web')
          ->deliver($msg);

        return $this->response
          ->withType('application/json')
          ->withStringBody(json_encode([
            'success' => true, 
            'message' => 'Grazie, ti risponderemo al più presto.',
            'referer' => $referer
          ]));
      } catch (Exception $e) {
        return $this->response
          ->withType('application/json')
          ->withStatus(500)
          ->withStringBody(json_encode([
            'success' => false, 
            'message' => 'Errore durante l\'invio dell\'email: ' . $e->getMessage()
          ]));
      }
    }
    
    return $this->response
      ->withType('application/json')
      ->withStatus(405)
      ->withStringBody(json_encode(['success' => false, 'message' => 'Method not allowed']));
  }


}
