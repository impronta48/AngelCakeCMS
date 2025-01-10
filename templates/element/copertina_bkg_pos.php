<?= $this->Form->control('copertina_bkg_pos', [
  'label' => 'Posizione copertina',
  'value' => $entity['copertina_bkg_pos'],
  'options' => [
    null => 'crop', 
    'crop-top-left' => 'crop-top-left', 
    'crop-top' => 'crop-top', 
    'crop-top-right' => 'crop-top-right', 
    'crop-left' => 'crop-left', 
    'crop-center' => 'crop-center', 
    'crop-right' => 'crop-right', 
    'crop-bottom-left' => 'crop-bottom-left', 
    'crop-bottom' => 'crop-bottom',
    'crop-bottom-right' => 'crop-bottom-right'
  ]
]);?>