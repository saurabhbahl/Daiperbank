export default {
	install(Vue, options) {
		Vue.mixin({
			data() {
				return {
					errors: null,
				};
			},

			methods: {
				hasError(field) {
					return this.errors && !(undefined == this.errors[ field ]);
				},

				error(field) {
					if (this.hasError(field)) {
						return this.errors[ field ][0];
					}

					return null;
				},

				clearErrors() {
					this.errors = null;
				},

				displayErrors(errors) {
					this.errors = errors;
				}
			}
		});
	}
}