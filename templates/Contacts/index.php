<?php

use Cake\Routing\Router;
?>
<?php $this->assign('title', 'Thank you'); ?>
<?php $this->assign('description', 'Grazie per averci contattato'); ?>

<div class="row">
    <div class="container">
        <h1>Grazie per averci contattato.</h1>
        <i>Ti risponderemo al più presto.</i>

        <?= $this->Html->link('Torna alla pagina precedente', Router::url($referer)); ?>
    </div>
</div>