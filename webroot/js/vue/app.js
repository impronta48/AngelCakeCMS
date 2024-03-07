var app = new Vue({
    el: '#app',
    data() {
        return {
            form: {},
            showMobile: false,
            loading: false,
        }
    },
    methods: {
        showMobileMenu() {
            this.showMobile = !this.showMobile;
        },
    }
});

