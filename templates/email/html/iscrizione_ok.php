<?php

use Cake\Utility\Text;
?>
Caro <?= $participant->name ?>,<br>
ti sei iscritto con successo a: <?= $event->title ?> .<br>

<h2>Informazioni</h2>
<?= $event->description ?>

<p>
  <b>Luogo</b>: <?= $event->place ?><br>
  <b>Orario inizio</b>: <?= $event->start_time->nice() ?><br>
  <b>Orario fine</b>: <?= $event->end_time->nice() ?><br>
</p>

Se hai dubbi o dovessi modificare la tua iscrizione,<br>
per favore scrivi una mail a <br>

<?php $e = $event->organizer_email;
$m = key($e) ?>
<?= $this->Html->link(current($e), "mailto:$m") ?><br>
<br>
Saluti,<br>
Lo Staff<br>