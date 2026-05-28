import FileUploader from '../../components/FileUploader.vue';
import MappaPercorsi from '../../../../plugins/Cyclomap/assets/js/components/MappaPercorsi.vue';
import vSelect from 'vue-select';
import "vue-select/dist/vue-select.css";

function parseJsonScript(id, fallback = []) {
    const el = document.getElementById(id);
    if (!el || !el.textContent) return fallback;
    try {
        return JSON.parse(el.textContent);
    } catch (e) {
        console.error(`[edit.js] JSON non valido in #${id}`, e);
        return fallback;
    }
}

const appEl = document.getElementById('app');

if (appEl) {
    new Vue({
        el: '#app',
        components: {
            'file-uploader': FileUploader,
            'mappa-percorsi': MappaPercorsi,
            'v-select': vSelect,
        },
        data() {
            const tagsList = parseJsonScript('tags-data', []);
            const destTags = parseJsonScript('dest-tags-data', []);

            const initialTags = Array.isArray(destTags)
                ? destTags.map(t => t?.name || t?.label).filter(Boolean)
                : [];

            return {
                loading: false,
                form: { tags: initialTags },
                selectOptionsTags: Array.isArray(tagsList) ? tagsList : []
            };
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
}
