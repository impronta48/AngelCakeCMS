import FileUploader from '../../components/FileUploader.vue';
import MappaPercorsi from '../../../../plugins/Cyclomap/assets/js/components/MappaPercorsi.vue';

var app = new Vue({
    el: '#app',
    components: {        
		'file-uploader': FileUploader,
    'mappa-percorsi': MappaPercorsi,
    },
    data() {
      const tagsData = document.getElementById('tags-data');
      const selectOptionsTags = tagsData ? JSON.parse(tagsData.textContent) : [];
        
      const destTagsData = document.getElementById('dest-tags-data');
      const destTags = destTagsData ? JSON.parse(destTagsData.textContent) : [];
        
      return {
  			loading: false,
        selectOptionsTags: selectOptionsTags,
        form: {
          tags: destTags.map(t => t.name || t.label)
        }
  		}
    },
});