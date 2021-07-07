var app = new Vue({
    el: '#app',
    data() {
        return {
            form: {},
            showMobile: false,
        }
    },
    methods: {
        showMobileMenu() {
            this.showMobile = !this.showMobile;
        },
    }
});