var app = new Vue({
    el: '#app',
    data() {
        return {
            showMobile: false,
        }
    },
    methods: {
        showMobileMenu() {
            this.showMobile = !this.showMobile;
        },
    }
});
const Editor = toastui.Editor;
const editor = new Editor({
    el: document.querySelector('#editor'),
    height: '500px',
    initialEditType: 'markdown',
    previewStyle: 'vertical',
    initialValue: $initial,
});

editor.getHtml();