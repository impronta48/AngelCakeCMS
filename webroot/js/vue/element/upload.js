Vue.component('file-uploader', {
    components: {
        'vue-dropzone': window.vue2Dropzone,
    },
    template: `
        <div>
            <input v-if="temporary" type="hidden" :name="'upload_session_id['+field+']'" :value="id+'|'+field">
            <vue-dropzone ref="dropzoneInstance" :id="field" :options="dropzoneOptions"></vue-dropzone>
        </div>
    `,
    props: {
        field: String,
        files: Array,
        model: String,
        destination: String, // if temporary, this can be anything
        id: String, // could be session_id, a string
        fname: String,
        multiple: Boolean,
        temporary: Boolean,
        filetype: String,
        convert: Boolean,
    },
    async mounted() {
        for (f in this.files) {
            let img = this.files[f];
            this.$refs.dropzoneInstance.manuallyAddFile(img, img.thumbnail_url);
        }
    },
    data: function() {
        let _this = this;
        return {
            dropzoneOptions: {
                removedfile: async function(file) {
                    try {
                        await axios.get(_this.getDeleteUrl(file));
                        // I don't know why this part below works or what it does exactly. Why do we need _ref?
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    } catch (error) {
                        console.log(error);
                    }
                },
                maxFiles: (this.multiple ? undefined : 1),
                acceptedFiles: this.filetype != 'null' ? this.filetype : undefined,
                paramName: this.field, // The name that will be used to transfer the file
                uploadMultiple: this.multiple,
                // withCredentials: true,
                addRemoveLinks: true,
                url: `/attachments/upload/${this.model}/${this.destination}/${this.id}/${this.field}` + (this.temporary ? '/temp' : ''),
                resizeWidth: this.convert ? 5000 : undefined, // This should give jpegs of appropriate size
                resizeMimeType: this.convert ? 'image/jpeg' : undefined,
                maxFilesize: null, // PHP will handle in AttachmentsController
                thumbnailHeight: 300,
                thumbnailWidth: 150,
                thumbnailMethod: 'crop',
            }
        }
    },
    methods: {
        getDeleteUrl(file) {
            return `/attachments/remove/${this.model}/${this.destination}/${this.id}/${this.field}/${file.name}` + (this.temporary ? '/temp' : '')
        }
    }
});