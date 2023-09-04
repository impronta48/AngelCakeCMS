<div class="container">
    <h1>Importa da Nextcloud</h1>
    <p>
        Questa pagina legge da server Nextcloud la cartella del sito e trasferisce i contenuti sul server, nella cartella static.
    </p>
    <p>
        <b>Attenzione</b>: i file vengono solo copiati e creati se sono più nuovi su Nextcloud, ma <b>non</b> vengono cancellati se sono stati cancellati su Nextcloud.
    </p>
    <hr>
    <form action="" method="post">
        <input type="submit" name="submit" value="Importa da Nextcloud" />
    </form>

    <br>
    <?php if($risultato) : ?>
    <div class="card mt-3">
        <div class="card-header">
            <h3>Risultato operazione</h3>
        </div>
        <div class="card-body">
            <?= $this->Text->autoParagraph($risultato) ?>
        </div>
    </div>
    <?php endif; ?>
</div>