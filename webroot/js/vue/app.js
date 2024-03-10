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
    },
	mounted() {
		if (document.querySelector('.editor')) {
			var editors = document.querySelectorAll(".editor");
			editors.forEach(function(edt) {
				CKEDITOR.replace(edt, {
					customConfig: '/js/ckeditor.config.js'
				})
			});
		}
	},
});

