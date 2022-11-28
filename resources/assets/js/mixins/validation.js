export default {
	props: {
		initialErrors: {
			required: false,
			type: Object,
			default: function() { return {}; },
		},
	},

	data() {
		return {
			errors: this.clone(this.initialErrors),
		};
	},

	watch: {
		initialErrors() {
			this.errors = this.clone(this.initialErrors);
		},
	},

	methods: {
		clearError(field) {
			if (undefined == field) {
				this.$set(this.$data, 'errors', {});
				return;
			}

			this.errors[ field ] = null;
			this.$delete(this.errors, field);
		},

		setError(field, err) {
			this.$set(this.errors, field, err);
		},

		hasError(field) {
			if (undefined == field) {
				return Object.keys(this.errors).length > 0;
			}

			if ( ! field instanceof Array) {
				field = [ field ];
			}

			return !! Object.keys(this.errors).filter( errorField => field.indexOf(errorField) >= 0).length;
		},

		error(field) {
			if (undefined == field) {
				return this.errors;
			}

			return this.errors[ field ] || null;
		},

		clone(obj) {
			return JSON.parse(JSON.stringify(obj));
		},
	}
}