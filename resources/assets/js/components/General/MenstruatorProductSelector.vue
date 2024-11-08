<template>
	<table class="table table-condensed table-striped ma0">
		<thead>
			<tr>
				<th class="w-33">Type
					<span class="required">*</span>
				</th>
				
			</tr>
		</thead>

		<tbody>
			<tr>
				
				<td>
					<select v-model="selected_product_id" class="form-control"
						@change="onProductChange">
						<option disabled>Select one</option>
						<option v-for="Product in CategoryProducts"
								:value="Product.id" v-if="Product.id == 17 || Product.id == 18 || Product.id == 19 || Product.id == 20 || Product.id == 32" >
								{{ Product.name }}
						</option>
					</select>
				</td>

			
			</tr>
		</tbody>
	</table>
</template>

<script>
export default {
	props: {
		productCategories: {
			required: true,
			type: Array,
			validator: function(ProductCategories) {
				return ProductCategories.every( Category => {
					if (typeof Category.product == 'array') {
						return Category.product.length > 0;
					}
					else if (typeof Category.product == 'object') {
						return Object.keys(Category.product).length > 0;
					}

					return false;
				});
			},
		},

		initialSelectedProduct: {
			required: false,
			default: "3",
			validator: function(value) {
				return value == parseInt(value);
			},
		},

		initialQuantity: {
			required: false,
			default: 0,
			validator: function(value) {
				return value == parseInt(value);
			},
		},
	},

	data() {
		return {
			selected_category_id: this.initialSelectedProduct? this.getCategoryForProduct(this.initialSelectedProduct) : 0,
			selected_product_id: this.initialSelectedProduct,
			selected_quantity: 1,
			ProductCategories: this.productCategories,
		};
	},

	computed: {
		CategoryProducts() {
			this.SelectedCategory="3";
			return this.SelectedCategory? this.SelectedCategory.product : [];
		},

		SelectedCategory() {
			return this.ProductCategories.filter( Category => Category.id == "3" )[0] || null;
		},

		SelectedProduct() {
			if ( ! this.SelectedCategory || ! this.selected_product_id) {
				return null;
			}

			return Object.values(this.SelectedCategory.product).filter( Product => Product.id == this.selected_product_id )[0];
		},
	},

	methods: {
		getCategoryForProduct(product_id) {
			return this.productCategories.reduce( (selected_category_id, Category) => {
				if (selected_category_id) return selected_category_id;

				return Object.values(Category.product).filter( tmpProduct => {
					return tmpProduct.id == product_id;
				}).length? Category.id : null;
			}, null);
		},

		onCategoryChange() {
			this.selected_product_id = null;

			this.selectionChanged();
		},

		onProductChange() {
			this.selectionChanged();
		},

		onQuantityChange() {
			this.selectionChanged();
		},

		onQuantityEscape(evt) {
			if (this.selected_quantity) {
				this.selected_quantity = null;

				evt.stopPropagation();
			}
		},

		selectionChanged() {
			let selectedProduct = {
				...(this.SelectedProduct? this.SelectedProduct : {}),
				quantity: this.selected_quantity,
				category: this.SelectedCategory,
			};

			this.$emit('change', this.isValid(), selectedProduct);
		},

		isValid() {
			return this.SelectedProduct  > 0;
		},
	}
}
</script>