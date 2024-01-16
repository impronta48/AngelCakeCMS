<?php $this->assign('title', 'Event ADD'); ?>

<div class="container">
  <?= $this->element('v-admin-navbar', ['event' => $event]); ?>

  <?= $this->Form->create($event); ?>
  <fieldset>
    <?php
    echo $this->Form->control('title', ['label' => 'Titolo (*)', 'readonly' => !empty($percorso_id)]);
    echo $this->Form->control('description', ['label' => 'Descrizione (*)', 'id' => 'editor', 'readonly' => !empty($percorso_id)]);
    echo $this->Form->control('max_pax');
    echo $this->Form->control('place', ['label' => 'Luogo (*)', 'readonly' => !empty($percorso_id)]);
    echo $this->Form->control('destination_id', ['label' => 'Destinazione (*)', 'options' => $destinations, 'disabled' => !empty($percorso_id)]);
    echo $this->Form->control('percorso_id', [
      'options' => $percorsi_evento, 
      'empty' => '--',
      'disabled' => !empty($percorso_id)
    ]);
    echo $this->Form->control('start_time');
    echo $this->Form->control('end_time');
    echo $this->Form->control('min_year');
    echo $this->Form->control('max_year');
    echo $this->Form->control('slug');
    echo $this->Form->control('user_id', ['options' => $users]);
    echo $this->Form->control('cost', ['label' => 'Costo (*)', 'readonly' => !empty($percorso_id)]);
    ?>
  </fieldset>
  <div>
    <small style="color:red">(*) Se viene selezionato un percorso il campo viene sovrascritto in automatico con il valore del percorso</small>
  </div>
  <div>
    <small>Se si inserisce un costo per l'evento, il partecipante sarà rimandato ad una pagina di paypal dove effettuerà il pagamento.</small>
  </div>
  <?= $this->Form->button(__("Add")); ?>
  <?= $this->Form->end() ?>

</div>