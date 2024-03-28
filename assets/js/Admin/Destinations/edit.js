import FileUploader from '../../components/FileUploader.vue';
import MappaPercorsi from '../../../../plugins/Cyclomap/assets/js/components/MappaPercorsi.vue';

var app = new Vue({
    el: '#app',
    components: {        
		'file-uploader': FileUploader,
    'mappa-percorsi': MappaPercorsi,
    },
    data() {
        return {
			loading: false,
		}
    },
});