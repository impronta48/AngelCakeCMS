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
    return {
        loading: false,
        form: { tags: [] },
        selectOptionsTags: (() => {
            try {
                const raw = document.getElementById('app').dataset.tags;
                console.log('tags raw:', raw);
                return JSON.parse(raw || '[]');
            } catch(e) {
                console.error('JSON parse error:', e);
                return [];
            }
        })()
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