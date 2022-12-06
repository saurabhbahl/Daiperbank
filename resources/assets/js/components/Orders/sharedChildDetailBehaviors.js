import validation from '../../mixins/validation';

export default {
	mixins: [ validation ],

	props: {
		initialChild: {
			required: true,
			type: Object,
		},

		isEditable: {
			required: false,
			type: Boolean,
			default: true,
		},

		initialEditing: {
			required: false,
			type: Boolean,
			default: false,
		},

		productCategories: {
			required: false,
			type: Array,
		},

		processing: {
			required: false,
			type: Boolean,
			default: false,
		}
	},

	data() {
		let tmpChild = this.clone(this.initialChild);
		tmpChild.product_id = tmpChild.item ? tmpChild.item.product_id : null;
		tmpChild.quantity = tmpChild.item? tmpChild.item.quantity : null;

		return {
			Child: this.initialChild,
			isEditing: this.initialEditing,
			editedChild: tmpChild,
			isValid: false,
		};

		return {
			Child: this.initialChild,
			isEditing: this.initialEditing,
			editedChild: tmpChild,
			isValid: false,
		};
	},

	methods: {
		cancel() {
			if (this.isEditing) {
				this.isEditing = false;
			} else {
				this.close();
			}
		},

		close() {
			this.$emit('close');
		},

		cancelClicked() {
			this.cancel();
		},

		onChildChanged(Child) {
			this.editedChild = Child;
			this.validate();
		},

		onDocumentKeyup(evt) {
			if (evt.keyCode == 27) {
				this.cancel();
				evt.preventDefault();
			}
		},

		validate() {
			this.clearError();

			if (!this.editedChild) {
				// the user hasn't made any changes yet..the component just loaded
				// how should we handle an 'error' here?
				this.isValid = false;
				return this.isValid;
			}
			debugger;
			if(this.editedChild.is_menstruator==1)
			{
				if ( ! this.editedChild.product_id
					|| ! this.getProductCategory(this.editedChild.product_id)) {
					this.setError('product_id', 'Please select a type.');
				}

				return this.isValid = ! this.hasError();	
			}
			else{
				let weight = parseInt(this.editedChild.weight);
				let productCategory = null;

				if ( ! this.editedChild.product_id
					|| ! this.getProductCategory(this.editedChild.product_id)) {
					this.setError('product_id', 'Please select a diaper type & size for this child.');
				}
				else if ( ! this.editedChild.quantity) {
					this.setError('quantity', 'Please specify a quantity for the selected diapers');
				}
				else {
					productCategory = this.getProductCategory(this.editedChild.product_id);
					if (productCategory.id == 2) { // this is the pullup category
						if ( ! weight ) {
							// weight is required if the child is in pull ups
							this.setError('weight', 'Please enter the child\'s weight.');
						}
					}
				}

				if (productCategory && productCategory.id == 2 && ( isNaN(Number(weight)) || weight == 0 || weight < 1)) {
					this.setError('weight', 'Please enter the child\'s weight.');
				}

				return this.isValid = ! this.hasError();
			}
	},

		getProductCategory(product_id) {
			return this.productCategories.filter( Category => {
				return Category.product.filter( Product => {
					return Product.id == product_id;
				}).length > 0;
			})[0] || null;
		}
	},

	mounted() {
		document.addEventListener('keyup', this.onDocumentKeyup);
		this.validate();
	},

	beforeDestroy() {
		document.removeEventListener('keyup', this.onDocumentKeyup);
	}
}