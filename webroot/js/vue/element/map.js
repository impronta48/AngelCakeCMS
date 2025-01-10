Vue.component('mappa-percorsi', {
    props: {
        poi_id: Number,
        percorso_id: Number,
        edit: Boolean,
    },
    components: {
        'l-map': window.Vue2Leaflet.LMap,
        'l-tile-layer': window.Vue2Leaflet.LTileLayer,
        'l-marker': window.Vue2Leaflet.LMarker,
        'l-popup': window.Vue2Leaflet.LPopup,
    },
    template: `
            <div style="display: block; position: relative; height: 100%;">
              
                <div v-if="edit" style="display:block; position:relative;">
                    <b-row align-v="center">
		            	    <b-col cols="12"><label for="localita">Località</label><b-form-input name="localita" id="localita" v-model="form.localita" placeholder="" label="Località:"></b-form-input></b-col>
                    </b-row>
		                <b-row align-v="center">
                      <b-col cols="6"><label for="indirizzo">Indirizzo</label><b-form-input name="indirizzo" id="indirizzo" v-model="form.indirizzo" placeholder="" label="Indirizzo:"></b-form-input></b-col>
                      <b-col cols="2"><label for="lat">Latitudine</label><b-form-input name="lat" id="lat" v-model="form.lat" placeholder="" label="Lat"></b-form-input></b-col>
                      <b-col cols="2"><label for="lon">Longitudine</label><b-form-input name="lon" id="lon" v-model="form.lon" placeholder="" label="Lon"></b-form-input></b-col>
                      <b-col cols="2">
                        <b-button block @click="geoCode" variant="outline-primary" size="sm">Cerca Indirizzo</b-button>
                        <b-button block @click="reverseGeoCode" variant="outline-secondary" size="sm">Ottieni Indirizzo</b-button>
                      </b-col>
		                </b-row>
                </div>
                
                <l-map
                  :zoom="zoom"
                  :center="center"
                  :option="mapOptions"
                  ref="map"      
                  @click="moveMarker"
                  style="display: block; position: relative; height: 80%;">                
                        <l-marker v-if="edit" :lat-lng="[this.form.lat, this.form.lon]"></l-marker>
                        <l-marker v-for="p in pois" :key="p.id" :lat-lng="[p.lat, p.lon]" :icon="getIcon(p.categorie[0].marker)">
                          <l-popup>
                            <b-card :title="p.title" :img-src="formatImage(p.copertina,500,200,'contain')" no-body>
                              <b-card-body><a :href="p.url"><h4>{{ p.title }}</h4></a></b-card-body>
                            </b-card>
                          </l-popup>
                        </l-marker>
                <l-tile-layer :url="url" :attribution="attribution" :options="layerOptions" />
                </l-map>
                
            </div>`,
    data() {
        return {
            loaded: false,
            message: '',
            zoom: 10,
            url: "https://tiles.bikesquare.eu/tiles/tile/{z}/{x}/{y}.png",
            // url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            currentZoom: 11.5,
            currentCenter: L.latLng(45.070339, 7.686864),
            percorso: {},
            pois: [],
            mapOptions: {
                zoomSnap: 0.5,
            },
            layerOptions: {
                // tileSize: 512,
                // zoomOffset: -1,
            },
            center: L.latLng(44.6031628, 7.9283508),
            options: {},
            form: {
                lon: 0,
                lat: 0,
                indirizzo: "Località Ciocchini, 18",
                localita: "Novello",
            },
        }
    },
    async created() {
        let response;
        if (this.poi_id != null) {
            response = await axios.get(`/poi.json?id=${this.poi_id}`);
            this.center = L.latLng(response.data[0].lat, response.data[0].lon);
            this.centerUpdate();
            this.pois.push(response.data[0]);
            if (this.edit) {
                this.form.localita = this.pois[0].localita;
                this.form.indirizzo = this.pois[0].indirizzo;
                this.form.lat = this.pois[0].lat;
                this.form.lon = this.pois[0].lon;
            }
        }
        if (this.percorso_id != null) {
            response = await axios.get(`/percorsi.json?id=${this.percorso_id}`);
            this.percorso = response.data[0];
            if (this.percorso.centroid_lat != 0 && this.percorso.centroid_lon != 0) {
                this.center = L.latLng(this.percorso.centroid_lat, this.percorso.centroid_lon);
                this.centerUpdate();
            }
            this.addPathOverlay();
            response = await axios.get(`/poi.json?percorso=${this.percorso_id}`);
            this.pois = response.data;
            if (this.edit) {
                this.form.localita = this.percorso.comune;
                this.form.indirizzo = 'N/A';
                this.form.lat = this.percorso.centroid_lat;
                this.form.lon = this.percorso.centroid_lon;
            }
        }
        this.loaded = true;
    },
    methods: {
        addPathOverlay() {
            if (this.percorso.kml != undefined && this.percorso.kml.length > 0) { // Controlla che ci sia veramente un path
                var map = this.$refs.map.mapObject;
                var layer = omnivore.kml(this.percorso.kml)
                    .on('ready', function() { // Il layer non e` pronto subito: se non aspetti per fare getBounds non ti da bounds validi!
                        map.fitBounds(layer.getBounds());
                    })
                    .addTo(map);
            }
        },
        zoomUpdate(zoom) {
            this.currentZoom = zoom;
        },
        centerUpdate(center) {
            this.currentCenter = center;
        },
        moveMarker(event) {
            this.form.lat = event.latlng.lat;
            this.form.lon = event.latlng.lng;
        },
        formatAddress(feature) {
            return `${feature.city}, ${feature.street} ${feature.housenumber}, ${feature.postcode} ${feature.country}`;
        },
        getIcon(marker) {
            return new L.icon({
                iconUrl: "/cyclomap/img/" + marker,
                iconSize: [40, 55],
                iconAnchor: [20, 55],
                popupAnchor: [0, -35],
                // shadowUrl: 'my-icon-shadow.png',
                // shadowSize: [68, 95],
                // shadowAnchor: [22, 94]
            });
        },
        formatImage(url, w = 500, h = 500, fit = "crop") {
            return `/images${url}?w=${w}&h=${h}&fit=${fit}`
        },
        async reverseGeoCode() {
            // Cerco indirizzo
            let response = await axios.get(`https://photon.komoot.io/reverse?lon=${this.form.lon}&lat=${this.form.lat}`);
            this.form.indirizzo = this.formatAddress(response.data.features[0].properties);
        },
        async geoCode() {
            try {
                res = await this.geoCoderService({
                    address: this.form.indirizzo,
                    city: this.form.localita,
                    province: "",
                });
                if (res.lng != 0 && res.lat != 0) {
                    this.form.lon = res.lng;
                    this.form.lat = res.lat;
                    // this.form.indirizzo = res.addr;
                    // this.form.localita = res.loc;
                    this.$refs.map.mapObject.flyTo([this.form.lat, this.form.lon], this.zoom);
                    this.message = null;
                }
            } catch (error) {
                this.message = error.message;
            }
        },
        async geoCoderService(address) {
            let a = ""; //Address
            let c = ""; //City
            let p = ""; //Province
            //Default: Centro su Torino
            // let lat = 45.05011899322459;
            // let lon = 7.669830322265626;
            let lat = 180;
            let lon = 180;
            let res = new L.latLng();

            if (typeof address == "string") {
                a = address;
            } else {
                a = address.address;
                c = address.city;
                p = address.province;
            }

            //Se hai specificato una città focalizzo la ricerca attorno alla città
            if (c != "") {
                let response = await axios.get(`https://photon.komoot.io/api/?q=${c}, ${p}&limit=1`);
                if (response.data.features.length > 0) {
                    lon = response.data.features[0].geometry.coordinates[0];
                    lat = response.data.features[0].geometry.coordinates[1];
                }
            }

            res = await this.findAddress(`${a}, ${c} ${p}`, lat, lon);
            return res;
        },
        async findAddress(a, lat, lon) {
            //Cerco l'indirizzo
            let response;
            if (lat < 90 && lon < 90) {
                response = await axios.get(`https://photon.komoot.io/api/?q=${a}&lat=${lat}&lon=${lon}&limit=1`);
            } else {
                response = await axios.get(`https://photon.komoot.io/api/?q=${a}&limit=1`);
            }

            if (response.data.features.length > 0) {
                lon = response.data.features[0].geometry.coordinates[0];
                lat = response.data.features[0].geometry.coordinates[1];
                return {
                    lat: lat,
                    lng: lon,
                    addr: this.formatAddress(response.data.features[0].properties),
                    loc: response.data.features[0].properties.county,
                };
            } else {
                throw new Error(`Impossibile trovare l'indirizzo ${a}`);
            }
        },
    },
});