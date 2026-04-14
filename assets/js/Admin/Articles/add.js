import FileUploader from '../../components/FileUploader.vue';

var app = new Vue({
    el: '#app',
    components: {        
		'file-uploader': FileUploader,
    },
    data() {
        return {
			loading: false,
			saveAutoTrans: false,
		}
    },
    async mounted() {
		// Creo un ckeditor per tutti i controlli che hanno la class="editor"
		if (document.querySelector('.editor')) {
			var editors = document.querySelectorAll(".editor");
			editors.forEach(function(edt) {
				CKEDITOR.replace(edt, {
					customConfig: '/js/ckeditor.config.js'
				})
			});
		}},
    methods: {
		async saveAndAutomaticallyTranslate(event) {
			let response = await this.$bvModal.msgBoxConfirm("L'operazione comporterà la sovrascrittura di traduzioni già presenti. Sei sicuro di voler procedere?")
			let clickedButton = event.target.name
			if (response && event.target.name == 'save-auto') {
				this.saveAutoTrans = true
				await this.$nextTick()
				this.$refs.form.submit()				
			}
		}
	}
});