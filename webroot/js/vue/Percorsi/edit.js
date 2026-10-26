var app = new Vue({
    el: '#app',
    components: {
        'v-select': VueSelect.VueSelect,
    },
    data() {
        return {
			form: {
				pois: $prev_pois,
				destination_id: $prev_dest,
				  percorsoPoiId: $prev_poi_id ?? null,
			},
			poiSelect: [],
		}
    },
	async created() {
		this.getPois();
	},
	methods: {
		async getPois() {
			let response = await axios.get(`/admin/poi/options.json?destination_id=${this.form.destination_id}`);
			this.poiSelect = response.data;
		}
	}, 
	watch: {
        // Quando poiSelect viene popolato, forza la selezione
        poiSelect(newList) {
            if (newList.length && $prev_poi_id) {
                this.form.percorsoPoiId = $prev_poi_id;
            }
        }
    }
});