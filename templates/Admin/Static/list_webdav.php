<?= $this->Html->script('node_modules/axios/dist/axios.min.js', ['block' => true]) ?>
<?php
$this->assign('vue', 'Static/list-webdav');
?>

<div id="app2" class="container">
    <h1>Importa da Nextcloud</h1>
    <p>
        Questa pagina legge da server Nextcloud la cartella del sito e trasferisce i contenuti sul server, nella cartella static.
    </p>
    <p>
        <b>Attenzione</b>: i file vengono solo copiati e creati se sono più nuovi su Nextcloud, ma <b>non</b> vengono cancellati se sono stati cancellati su Nextcloud.
    </p>
    <hr>
    <b-button variant="primary" :disabled="spinning" @click="stop=false; runList()">
        <b-spinner small type="grow" v-if="spinning"></b-spinner>
        Importa da Nextcloud
    </b-button>

    <br>

    <div class="card mt-3">
        <div class="card-header">
            <h3>Avanzamento</h3>
        </div>
        <div class="card-body">
            <b-progress :value="progress" :max="max" show-progress animated></b-progress>
            <br>
            <p>{{ current_action }}</p>
        </div>
        <div class="card-footer">
            <b-button variant="danger" @click="doStop()" class="" v-if="spinning">Stop</b-button>
        </div>

    </div>