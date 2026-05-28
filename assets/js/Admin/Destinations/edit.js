import FileUploader from '../../components/FileUploader.vue';
import MappaPercorsi from '../../../../plugins/Cyclomap/assets/js/components/MappaPercorsi.vue';
import vSelect from 'vue-select';
import "vue-select/dist/vue-select.css";

var app = new Vue({
    el: '#app',
    components: {        
        'file-uploader': FileUploader,
        'mappa-percorsi': MappaPercorsi,
        'v-select': vSelect,
    },
    data() {

   
        let appEl = document.getElementById('app');
  

        let tagsList =  JSON.parse(document.getElementById('tags-data').textContent);
        let destTags =  JSON.parse(document.getElementById('dest-tags-data').textContent);
        
      

        let initialTags = [];
        if (destTags && destTags.length > 0) {
            initialTags = destTags.map(t => t.name || t.label);
        } 


        return {
            loading: false,
            form: { tags: initialTags },
            selectOptionsTags: tagsList
        }
    },
    mounted() {
        this.$nextTick(() => {
            const tagList = window.jQuery ? window.jQuery('#tag-list') : null;
            if (tagList && typeof tagList.select2 === 'function') {
                if (tagList.hasClass('select2-hidden-accessible')) {
                    tagList.select2('destroy');
                }
                tagList.select2({
                    placeholder: 'Seleziona tag...',
                    allowClear: true,
                    width: '100%',
                    theme: 'bootstrap4'
                });
            }
        });
    },
});
