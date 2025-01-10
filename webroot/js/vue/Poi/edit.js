var app = new Vue({
    el: '#app',
    components: {
        'v-select': VueSelect.VueSelect,
    },
    data() {
        return {
			form: {
				percorsi: $prev_percorsi,
				destination_id: $prev_dest,
			},
			selectOptions: [],
		}
    },
	async created() {
		this.getPercorsi();
	},
	methods: {
		async getPercorsi() {
			let response = await axios.get(`/admin/percorsi/options.json?destination_id=${this.form.destination_id}`);
			this.selectOptions = response.data;
		}
	}
});