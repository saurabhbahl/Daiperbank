export default {
	props: {
		child: {
			required: true,
			type: Object,
		},

		isEditable: {
			required: false,
			type: Boolean,
			default: true,
		},

		processing: {
			required: false,
			type: Boolean,
			default: false,
		}
	},

	computed: {
		Child() {
			return this.child;
		},
	},

	methods: {
		onClick(Child) {
			this.$emit('click', Child);
		},
	},
};