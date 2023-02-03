                <b-card no-body class="mb-1">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                        <b-button block v-b-toggle.metodo-pagamento variant="primary">1. Scegli come vuoi pagare: pagamento a rate o unica rata</span>
                            <b-iconstack font-scale="1" v-if="paymentMethod != null">
                                <b-icon stacked icon="circle-fill" variant="success"></b-icon>
                                <b-icon stacked icon="circle" variant="white"></b-icon>
                                <b-icon stacked icon="check" variant="white"></b-icon>
                            </b-iconstack>
                        </b-button>
                    </b-card-header>
                    <b-collapse id="metodo-pagamento" accordion="payment-method" role="tabpanel">
                        <b-row class="mt-2 mb-2 ml-2 mr-2">
                            <b-col cols="12" md="6" class="mb-2">
                                <b-card footer-class="footer-class" title="Pagamento a rate" img-src="/img/ricaricabile-pana.svg" img-alt="Carta di Credito Ricaricabile" img-top tag="article" 
                                    style="overflow: visible !important;
                                        
                                        border-radius: 25px !important; 
                                        box-shadow: 5px 10px 25px #888888 !important;
                                        margin: 0px 7px 15px 7px;
                                        background-size: cover;
                                        background-repeat: no-repeat;
                                        background-position: center center;" 
                                    class="mb-2 h-100">
                                    <b-card-text>
                                        <p>Paga le tue bici a rate grazie a Banca Sella!</p>
                                        <p>Scegli tu il numero di rate, sino a un massimo di 10.</p>
                                        <p style="color:red">Funzionalità presto disponibile!</p>
                                        
                                    </b-card-text>
                                    
                                    <template #footer>
                                        <b-row >
                                            <b-col >
                                                <b-button variant="primary" block v-b-toggle.dati-utente block @click="setPaymentMethod('sella')" disabled>Paga con<img class="ml-2" src="/img/Banca_Sella_Group_logo.svg" alt="BancaSella" style="height:25px; display: inline"/></b-button>
                                            </b-col>
                                            
                                        </b-row>
                                        
                                        
                                    </template>
                                </b-card>
                            </b-col>
                            <b-col cols="12" md="6" class="mb-2">
                            <b-card footer-class="footer-class" title="Pagamento in un'unica soluzione" img-src="/img/registra-pana.svg" img-alt="Carta di Credito Ricaricabile" img-top tag="article" 
                                    style="overflow: visible !important;
                                        
                                        border-radius: 25px !important; 
                                        box-shadow: 5px 10px 25px #888888 !important;
                                        margin: 0px 7px 15px 7px;
                                        background-size: cover;
                                        background-repeat: no-repeat;
                                        background-position: center center;" 
                                    class="mb-2 h-100">
                                    <b-card-text>
                                        <p>Paga in un'unica soluzione grazie a MangoPay!</p>
                                        <p>Scegli tu il metodo di pagamento.</p>
                                        <b-img class="mt-2" src="/img/powered-by-mangopay.png"></b-img>
                                    </b-card-text>
                                    
                                    <template #footer>
                                        <b-row >
                                            <b-col >
                                                <b-button variant="primary" v-b-toggle.dati-utente block @click="setPaymentMethod('mangopay')">Unica rata</b-button>
                                            </b-col>
                                        </b-row>
                                    </template>
                                </b-card>
                            </b-col>
                        </b-row>
                    </b-collapse> 
                </b-card>