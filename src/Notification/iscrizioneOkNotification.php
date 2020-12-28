<?php

declare(strict_types=1);

namespace App\Notification;

use Cake\Core\Configure;
use Notifications\Notification\baseNotification;

class iscrizioneOkNotification extends baseNotification
{
  public function __construct($participant, $event)
  {
    //Imposto una mail dell'organizzatore di default
    if (empty($event->organizer_email)) {
      $event->organizer_email = Configure::read('MailAdmin');
    }

    parent::__construct();
    $this->to = $participant->email;
    $this->cc = $event->organizer_email;
    $this->from = $event->organizer_email;
    $this->subject = "Iscrizione a {$event->title}";
    $this->vars = ['event' => $event, 'participant' => $participant];
  }
}
