<?= $this->Html->css('admin.css',['inline'=>'block']) ?>
<div class="container">

<h2 style="text-align:center">Benvenuto nel sistema di gestione dei contenuti</h2>
<br>
    <div class="row">
        <div class=" col-md-2">
            <br>
            <a href="/articles/admin" class="titoloAdmin"><h4>Articoli</h4></a>
            <br>
            <a href="/articles/admin"><img src="/img/admin/articoli.png" class="img-responsive icona"></a>
        </div>
        <div class=" col-md-2">
            <br>
            <a href="/destinations/admin" class="titoloAdmin"><h4>Siti Locali</h4></a>
            <br>
            <a href="/destinations/admin" style="margin-left:50px;"><img src="/img/admin/categorie.png" class="img-responsive icona"></a>
        </div>
        <div class=" col-md-2">
            <br>
            <a href="/users/index" class="titoloAdmin"><h4>Utenti</h4></a>
            <br>
            <a href="/users/index" style="margin-left:50px;"><img src="/img/admin/users.png" class="img-responsive icona"></a>
        </div>
        <div class=" col-md-2">
            <br>
            <a href="/events" class="titoloAdmin"><h4>Eventi</h4></a>
            <br>
            <a href="/events" style="margin-left:50px;"><img src="/img/admin/eventi.png" class="img-responsive icona"></a>
        </div>
        <div class=" col-md-2">
            <br>
            <a href="/participants" class="titoloAdmin"><h4>Partecipanti</h4></a>
            <br>
            <a href="/participants" style="margin-left:50px;"><img src="/img/admin/partecipanti.png" class="img-responsive icona"></a>
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-md-22 text-center">
              <div class="divLogout">
                    <a type="button" class="btnLogout theme-solid-button btn btn-danger" href="/logout">LOGOUT</a>
              </div>
        </div>
    </div>
</div>