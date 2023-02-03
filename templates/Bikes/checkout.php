<?php

use Cake\Core\Configure;
use Cake\I18n\I18n;

$this->assign('title', 'Checkout');
$this->assign('vue', 'Bikes/checkout'); // Needed because this page is also rendered by `add`
?>

<?= $this->Html->css('https://unpkg.com/vue-select@latest/dist/vue-select.css', ['block' => true]); ?>
<?= $this->Html->script('node_modules/axios/dist/axios.min.js', ['block' => true]) ?>

<div class="page-header page-header-article parallax light larger10x larger-desc">
    <div class="container" data-0="opacity:1;" data-top="opacity:1;">
        <div class="row">
            <div class="col-md-8">
                <h1 class="titolo-percorso mt-5">
                    Prenota la prova
                </h1>
            </div><!-- End .col-md-6 -->
            <!-- End .col-md-6 -->
        </div><!-- End .row -->
    </div><!-- End .container -->
</div><!-- End .page-header -->


<div class="container">
    <b-row class="mb-3">
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

                    <b-list-group v-else flush>
                        <b-list-group-item v-for="(bike, i) in carrello" class="d-flex justify-content-between">
                            <span>
                                <b-icon icon="dash-circle-fill" variant="primary" scale="0.8" @click="removeFromCart(i)"></b-icon> {{bike.number}}
                                <b-icon icon="plus-circle-fill" variant="primary" scale="0.8" @click="addBikeToCart(bike)"></b-icon>
                            </span>
                            <span>
                                {{bike.Bici.name.length <= 12 ? `${bike.Bici.name}` : `${bike.Bici.name.slice(0,12)}...`}}
                                <b-badge :variant="bike.usata ? 'primary' : 'success'">{{bike.usata ? 'Usata' : 'Nuova'}}</b-badge>
                            </span>
                            <span>{{costoUnitarioBike(bike)}} € </span>
                        </b-list-group-item>
                    </b-list-group>
                </div>
                <div class="card-footer">
                    <span v-if="carrello.length > 0" style="font-size: 1em; font-weight: 300" class="mb-2 d-flex justify-content-center mr-4">Prezzo bici: {{total.toFixed(2)}} € che potrai pagare in 10 rate</span>
                    <br>
                    <span v-if="carrello.length > 0" style="font-size: 1.5em; font-weight: 900" class="mb-2 d-flex justify-content-center mr-4">Totale da pagare ora: 35€</span>

                    
                    <p style="font-size: .8em; font-weight: 300" class="mb-2">Se la bici ti piace la puoi portare a casa (e ti rimborsiamo i 35€).
                        Se decidi di non comprarla avrai passato una bella giornata in bici.</p>
                </div>
            </div>
        </b-col>


        <b-col cols="0" md="1">
            <div style="border-left: 1px dashed grey;" class="h-100 mb-2"></div>
        </b-col>
        <b-col cols="12" md="7">
            <div class="accordion" role="tablist">
                <b-card no-body class="mb-1">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                        <b-button block v-b-toggle.data variant="primary">1. Scegli la data in cui fare la prova
                            <b-iconstack font-scale="1" v-if="dateValidation">
                                <b-icon stacked icon="circle-fill" variant="success"></b-icon>
                                <b-icon stacked icon="circle" variant="white"></b-icon>
                                <b-icon stacked icon="check" variant="white"></b-icon>
                            </b-iconstack>
                        </b-button>
                    </b-card-header>
                    <b-collapse id="data" accordion="payment-method" role="tabpanel">
                        <div class="mb-2 mt-4 h-100">
                            <b-row class="ml-2 mt-2 mr-2">
                                <b-col cols="12" md="6">
                                    <b-label>Scegli la data in cui vuoi fare la prova</b-label>
                                    <b-input type="date" v-model="dataProva"></b-input>
                                </b-col>
                            </b-row>
                        </div>
                        <b-row>
                            <b-button class="ml-4 mr-4" block variant="light text-dark" v-b-toggle.dati-utente>Avanti</b-button>
                        </b-row>
                    </b-collapse>
                </b-card>
                <b-card no-body class="mb-1">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                        <b-button block v-b-toggle.dati-utente variant="primary">2. Inserisci i tuoi dati
                            <b-iconstack font-scale="1" v-if="formValidation">
                                <b-icon stacked icon="circle-fill" variant="success"></b-icon>
                                <b-icon stacked icon="circle" variant="white"></b-icon>
                                <b-icon stacked icon="check" variant="white"></b-icon>
                            </b-iconstack>
                        </b-button>
                    </b-card-header>
                    <b-collapse id="dati-utente" accordion="payment-method" role="tabpanel">
                        <div class="mb-2 mt-4 h-100">
                            <b-row class="ml-2 mt-2 mr-2">
                                <b-col cols="12" md="6">
                                    <div class="form-group">
                                        <input v-model="form['Nome']" type="text" class="form-control" name="name" id="name" placeholder="Nome*" required>
                                    </div>
                                </b-col>
                                <b-col cols="12" md="6">
                                    <div class="form-group">

                                        <input v-model="form['Cognome']" type="text" class="form-control" name="surname" id="surname" placeholder="Cognome*" required>
                                    </div>
                                </b-col>
                            </b-row>
                            <b-row class="ml-2 mr-2">
                                <b-col cols="12" md="6">
                                    <div class="form-group">

                                        <input v-model="form['EMail']" type="email" class="form-control" name="email" id="email" placeholder="Email*" required>
                                    </div>
                                </b-col>
                                <b-col cols="12" md="6">
                                    <div class="form-group">
                                        <input v-model="form['coupon']" type="text" class="form-control" name="coupon" placeholder="Codice coupon" id="coupon">

                                    </div>
                                </b-col>
                            </b-row>
                            <b-row class="mb-2 ml-2 mr-2">
                                <b-col cols="12" md="6">
                                    <div class="form-group">
                                        <input v-model="form['Cellulare']" type="tel" class="form-control" name="mobile" id="mobile" placeholder="Cellulare*" required>
                                    </div>
                                </b-col>
                            </b-row>
                            <div v-if="paymentMethod == 'sella'">
                                <hr class="mr-2 ml-2" style="border-top: 1px dashed grey">
                                <p class="mr-2 ml-2 text-center" style="font-size: 1.3em; font-weight: 900">Dati aggiuntivi Banca Sella</p>
                                <b-row class="mb-2 ml-2 mr-2">
                                    <b-col cols="12" md="6">
                                        <div class="form-group">
                                            <input type="text" v-model="taxCode" class="form-control mt-2" name="taxCode" id="taxCode" placeholder="Codice Fiscale*" required>
                                        </div>
                                    </b-col>
                                </b-row>
                            </div>
                            <b-row>
                                <b-button class="ml-4 mr-4" block variant="light text-dark" v-b-toggle.riepilogo>Avanti</b-button>
                            </b-row>

                        </div>

                    </b-collapse>
                </b-card>
                <b-card no-body class="mb-1">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                        <b-button block v-b-toggle.riepilogo variant="primary">3. Riepilogo</b-button>
                    </b-card-header>
                    <b-collapse id="riepilogo" accordion="payment-method" role="tabpanel">
                        <div class="mb-2 h-100">
                            <b-row class="ml-2 mr-2 mt-4">
                                <b-col>
                                    <strong style="font-size: 1.3em">Metodo di pagamento prova</strong> <br>CARTA DI CREDITO<br>
                                    <strong style="font-size: 1.3em">Metodo di pagamento bici</strong> <br>Bonifico, POS o 10 Rate con APPAGO* <span class="small text-muted">(* prossima attivazione)</span><br>
                                </b-col>
                            </b-row>
                            <hr class="ml-2 mr-2">
                            <b-row class="ml-2 mr-2">
                                <b-col>
                                    <strong style="font-size: 1.3em">Dati cliente</strong> <br>
                                    <strong>Nome:</strong> {{form.Nome}} <br>
                                    <strong>Cognome:</strong> {{form.Cognome}} <br>
                                    <strong>Email:</strong> {{form.EMail}} <br>
                                    <strong>Cellulare:</strong> {{form.Cellulare}} <br>
                                    <strong v-if="form.coupon">Coupon:</strong> {{form.coupon}} <br>
                                </b-col>
                            </b-row>
                            <b-row>
                                <b-button class="ml-4 mr-4 mt-3" block variant="light text-dark" v-b-toggle.riepilogo>Avanti</b-button>
                            </b-row>

                        </div>

                    </b-collapse>
                </b-card>
                <b-card no-body class="mb-1">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                        <b-button block variant="primary" :disabled="!canGoToPayment" @click="goToPayment()">4. Vai al pagamento</b-button>
                    </b-card-header>
                </b-card>
            </div>
        </b-col>
    </b-row>
    <b-modal id="modal-failed" hide-footer title="Qualcosa è andato storto!">
        <div class="d-block text-center">
            <h3>Errore: {{errorMessage}}</h3>
            <p>Riprova o contattaci!</p>
        </div>
        <b-button class="mt-3" variant="info" block v-b-modal.modal-recurring @click="$bvModal.hide('modal-failed')">Ok</b-button>

    </b-modal>
</div>