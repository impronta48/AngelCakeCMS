<?= $this->Form->control('copertina_bkg_pos', [
  'label' => 'Posizione copertina (default: center)',
  'value' => $entity['copertina_bkg_pos'],
  'options' => [
    null => '--', 
    'top' => 'top', 
    'bottom' => 'bottom', 
    'left' => 'left', 
    'right' => 'right', 
    'center' => 'center'
  ]
]);?>