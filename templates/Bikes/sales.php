
<?php

use Cake\Core\Configure;
use Cake\I18n\I18n;

$this->assign('title', 'Bici in vendita');
$this->assign('vue', 'Bikes/sales'); // Needed because this page is also rendered by `add`
?>

<?= $this->Html->css('https://unpkg.com/vue-select@latest/dist/vue-select.css', ['block' => true]); ?>
<?= $this->Html->script('node_modules/axios/dist/axios.min.js', ['block' => true]) ?>

<div class="page-header page-header-article parallax light larger10x larger-desc" >
  <div class="container" data-0="opacity:1;" data-top="opacity:1;">
    <div class="row">
      <div class="col-md-8">
        <h1 class="titolo-percorso mt-5">
         Bici in vendita
        </h1>
      </div><!-- End .col-md-6 -->
      <!-- End .col-md-6 -->
    </div><!-- End .row -->
  </div><!-- End .container -->
</div><!-- End .page-header -->


<div class="container">
      
    <br>
    <div class="card">
          <h5 class="card-header">Come funziona <i class="fa fa-info" aria-hidden="true"></i>
</h5>
            <div class="card-body">
             <p class="card-text">
             1) Scegli la bici che ti piace.<br>
             2) Aggiungila al carrello. <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
Stai acquistando la prova su strada nelle Langhe per valutare l'acquisto della bici che hai scelto. <br><strong>Non stai per pagare l'importo visualizzato nel totale</strong>. <br>
             </p>
              </div>
          </div>

    <div class="card-deck mt-5 mb-3">
        <b-col cols="12" md="8">
            <b-tabs>
                <b-tab title="Usate">
                    <b-row>
                        <div class="col-md-5 mt-4 mb-2"  v-for="bike in bikesUsate">
                            <div class="card h-100" style="border-radius: 25px !important; box-shadow: 5px 10px 25px #888888 !important;">
                                <b-img-lazy 
                                    class="card-img-top card-img-left" 
                                    :src="biciImg(bike.Tipobici.picture)" 
                                    :alt="`BikeSquare ${bike.Bici.name}`" 
                                    blank-src="/images/bikesquare/img/header.jpg?w=309&h=200&fit=crop&fm=webp&q=5"
                                    style="object-fit: contain; max-width: 250px;max-height: 250px;width: auto;height: auto;margin:auto; padding:10px"
                                    >
                                </b-img-lazy>
                                <div class="card-body" style="text-align:center;">
                                    <h5 class="card-title" style="font-weight: 900;">{{bike.Bici.name}}
                                        
                                    </h5>
                                    
                                    <div v-if="bike.Bici.sconto && bike.Bici.sconto > 0">
                                        <p>
                                            <del class="text-danger">{{(parseFloat(bike.Bici.prezzo_vendita)).toFixed(2)}} €</del>
                                            <span>{{(parseFloat(bike.Bici.prezzo_vendita) - parseFloat(bike.Bici.sconto)).toFixed(2)}} €</span>
                                        </p>
                                        <p><b-badge variant="success">Risparmi {{bike.Bici.sconto}}€ ({{calcolaScontoPercentuale(bike.Bici.prezzo_vendita, bike.Bici.sconto)}}%)</b-badge></p>
                                    </div>
                                    
                                    <p v-else>{{(parseFloat(bike.Bici.prezzo_vendita)).toFixed(2)}} €</p>
                                    
                                    <p style="text-align: center;"><b-badge variant="primary">Usata</b-badge></p>
                                    <p class="text-center text-muted font-weight-light mt-2" style="font-size: 0.8em">{{fraseNoleggiatore(bike)}}</p>
                                    <b-button class="mt-2" size="sm" variant="warning" @click="goToDetails(bike)">Scopri di più</b-button>
                                </div>
                                <div class="card-footer">
                                    
                                    <b-button block variant="primary" @click="addBikeToCart(bike, true)">Aggiungi al carrello</b-button>
                                </div>
                            </div>
                        </div>
                    </b-row>
                </b-tab>
               <!-- <b-tab title="Nuove">
                    <b-row>
                        <div class="col-md-5 mt-4 mb-2"  v-for="bike in bikesNew">
                            <div class="card h-100" style="border-radius: 25px !important; box-shadow: 5px 10px 25px #888888 !important;">
                                <b-img-lazy 
                                    class="card-img-top" 
                                    :src="biciImg(bike.Tipobici.picture)" 
                                    :alt="`BikeSquare ${bike.Bici.name}`" 
                                    blank-src="/images/bikesquare/img/header.jpg?w=309&h=200&fit=crop&fm=webp&q=5"
                                    style="object-fit: contain; max-width: 250px;max-height: 250px;width: auto;height: auto;margin:auto; padding:10px">
                                </b-img-lazy>
                                <div class="card-body" style="text-align:center;">
                                    <h5 class="card-title" style="font-weight: 900;">{{bike.Bici.name}}
                                        
                                    </h5>
                                    <p v-if="bike.Bici.sconto && bike.Bici.sconto > 0">
                                        <del class="text-danger">{{(parseFloat(bike.Bici.prezzo_vendita)).toFixed(2)}} €</del>
                                        <span>{{(parseFloat(bike.Bici.prezzo_vendita) - parseFloat(bike.Bici.sconto)).toFixed(2)}} €</span>
                                    </p>
                                    <p v-else>{{(parseFloat(bike.Bici.prezzo_vendita)).toFixed(2)}} €</p>
                                    <p style="text-align: center;"><b-badge variant="success">Nuova</b-badge></p>
                                    <p class="text-center text-muted font-weight-light mt-2" style="font-size: 0.8em">{{fraseNoleggiatore(bike)}}</p>
                                    <b-button class="mt-2" size="sm" variant="warning" @click="goToDetails(bike)">Scopri di più</b-button>
                                </div>
                                <div class="card-footer">
                                    <b-button block variant="primary" @click="addBikeToCart(bike, false)">Aggiungi al carrello</b-button>
                                </div>
                            </div>
                        </div>
                    </b-row>
                </b-tab>-->
            </b-tabs>
            
        </b-col>
        
            
        <b-col cols="12" md="4" class="mt-3">
            <div class="card" style="border-radius: 25px !important; box-shadow: 5px 10px 25px #FEE396 !important; border-color: #FFB233">
                <h4 style="text-align:center; padding:5px; margin-top:10px">Carrello
                    
                    <b-icon icon="cart-4" style="position:relative"></b-icon>
                    <span v-if="carrello.length > 0" style="height: 26px;
                        width: 26px;
                        background-color: red;
                        border-radius: 50%;position:absolute; margin-left: -10px">
                        <small style="color:white; position:absolute;margin-left: -4px; margin-top:4px; font-size: 0.5em">{{nBikesCarrello}}</small>
                    </span>
                    
                </h4>

                <h5 style="text-align:center; padding:5px; margin-top:5px">Prenota la prova della bici</h5>
               
                <div class="card-body">
                    <p v-if="carrello.length == 0">Il tuo carrello è vuoto</p>
                    <p v-if="carrello.length > 1">Il tuo carrello contiene il noleggio di prova delle bici qui sotto:</p>
                    
                    <b-list-group v-else flush >
                        <b-list-group-item v-for="(bike, i) in carrello" class="d-flex justify-content-between">
                            <span><b-icon icon="dash-circle-fill" variant="primary" scale="0.8" @click="removeFromCart(i)"></b-icon> {{bike.number}} <b-icon icon="plus-circle-fill" variant="primary" scale="0.8" @click="addBikeToCart(bike)"></b-icon></span>
                            <span >
                                {{bike.Bici.name.length <= 12 ? `${bike.Bici.name}` : `${bike.Bici.name.slice(0,12)}...`}} <b-badge :variant="bike.usata ? 'primary' : 'success'">{{bike.usata ? 'Usata' : 'Nuova'}}</b-badge></span>
                            <span >{{costoUnitarioBike(bike)}} € </span>
                        </b-list-group-item>
                    </b-list-group>
                </div>
                <div class="card-footer">
                    <span v-if="carrello.length > 0" style="font-size: 1em; font-weight: 300" class="mb-2 d-flex justify-content-center mr-4">Prezzo bici: {{total.toFixed(2)}} € che potrai pagare in 10 rate</span>
                    <br>
                    <span v-if="carrello.length > 0" style="font-size: 1.5em; font-weight: 900" class="mb-2 d-flex justify-content-center mr-4">Totale da pagare ora: 35€</span>

                    <b-button block variant="primary" :disabled="carrello.length == 0" @click="goToCheckout">Prenota la prova</b-button>
                    <br>
                    <p style="font-size: .8em; font-weight: 300" class="mb-2">Se la bici ti piace la puoi portare a casa (e ti rimborsiamo i 35€).
Se decidi di non comprarla avrai passato una bella giornata in bici.</p>
                </div>
            </div>
        </b-col>
        
    </div>
</div>