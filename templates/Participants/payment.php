<?php

use Cake\Core\Configure;

$cid = Configure::read('PayPal.ClientId');
$this->assign('title', "Pagamento $title per $name");
?>
<div class="our-history section-margin-top">
  <div class="container">

    <div class="row">
      <div class="col-md-7 col-xs-12">
        <div class="text">

          <h1><?= $title ?></h1>
          <p>
            <?= $name ?>,<br>
            Per concludere l'operazione dovresti <b>pagare <?= $amount ?> € con PayPal</b>. <br>
            Fai click qui sotto per procedere. PayPal ti permette di pagare con PayPal, Bonifico, Carta di Credito e Bancomat Maestro.
          </p>
          <div id="smart-button-container">
            <div style="text-align: center;">
              <div id="paypal-button-container"></div>
            </div>
          </div>
          <script src="https://www.paypal.com/sdk/js?client-id=<?= $cid ?>&currency=EUR" data-sdk-integration-source="button-factory"></script>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $baseurl = `<?= $this->Url->build(['controller' => 'participants', 'action' => 'thankyou', $pid],  ['fullBase' => true]) ?>`;
  $title = `<?= $title ?>`;
  $amount = <?= $amount ?>;
</script>

<?= $this->Html->script('paypal-button', ['block' => true]) ?>