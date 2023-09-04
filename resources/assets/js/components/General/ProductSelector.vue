<template>
	<table class="table table-condensed table-striped ma0">
		<thead>
			<tr>
				<th class="w-33">Type
					<span class="required">*</span>
				</th>
				<th class="w-33">Size
					<span class="required">*</span>
				</th>
				<th class="w-33">Quantity
					<span class="required">*</span>
				</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td>
					<select v-model="selected_category_id" class="form-control"
						@change="onCategoryChange" v-if="Child.age_mo >=24">
						<option disabled>Select one</option>
						<option v-for="Category in ProductCategories" :value="Category.id" :disabled="Category.id === 2 && ordercount >= 6" v-if="Category.id != 3">
								{{ Category.name }}
						</option>
					</select>
					<select v-model="selected_category_id" class="form-control"
						@change="onCategoryChange" v-if="Child.age_mo < 24">
						<option disabled>Select one</option>
						<option v-for="Category in ProductCategories"
								v-if="Category.id == 1"
								:value="Category.id">
								{{ Category.name }}
						</option>
					</select>
				</td>

				<td>
					<select v-model="selected_product_id" class="form-control"
						@change="onProductChange">
						<option disabled>Select one</option>
						<option v-for="Product in CategoryProducts"
								:value="Product.id">
								{{ Product.name }}
						</option>
					</select>
				</td>

				<td>
					<input type="tel" v-model="selected_quantity"
						class="form-control"
						@change="onQuantityChange"
						@keyup="onQuantityChange"
						@keyup.esc="onQuantityEscape">
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
			default: null,
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
		initialChild: {
			type: Object,
			required: true,
		},
		allChildren: {
			required: true,
			type: Array,
		},
	},

	data() {
		return { 
			Child: this.initialChild,
			selected_category_id: this.initialSelectedProduct? this.getCategoryForProduct(this.initialSelectedProduct) : 0,
			selected_product_id: this.initialSelectedProduct,
			selected_quantity: this.initialQuantity,
			ProductCategories: this.productCategories,
		};
	},

	computed: {
		CategoryProducts() {
			return this.SelectedCategory? this.SelectedCategory.product : [];
		},

		SelectedCategory() {
			return this.ProductCategories.filter( Category => Category.id == this.selected_category_id )[0] || null;
		},

		SelectedProduct() {
			if ( ! this.SelectedCategory || ! this.selected_product_id) {
				return null;
			}

			return Object.values(this.SelectedCategory.product).filter( Product => Product.id == this.selected_product_id )[0];
		},

		childs(){ 
			return this.allChildren.filter(	
				obj => obj.id === this.initialChild.child_id
			);
		},

		ordercount() {
			var childs = this.childs;
			var relatedData = [];
			var order_count = 0;
			childs.forEach((item) => {
				item.order_item.forEach((childitem) =>{
					var orderid = childitem.order.id;
					var productId = childitem.product.product_category_id;
					var orderstatus = childitem.order.order_status;
					// Relate the order and product data
					if(productId == 2){
						var key = orderid+'-'+orderstatus;
						relatedData[orderid] = orderstatus;
						
					}
				});
			});
			// order count for pull ups
			if(relatedData.length > 0){		
				relatedData.forEach((item,index) => {
					if(item !== 'cancelled' || item !== 'rejected'){
						return order_count++;
					}
				});
			}
			return order_count;
		}

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
			if(this.selected_category_id == 1){
				this.selected_quantity = '50';
			}
			else{
				this.selected_quantity = '40';
			}
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
				// ...(this.SelectedProduct? quantity),
				quantity: this.selected_quantity,
				category: this.SelectedCategory,
				order_count: this.ordercount,
			};

			this.$emit('change', this.isValid(), selectedProduct);
		},

		isValid() {
			return this.SelectedProduct && parseInt(this.selected_quantity) > 0;
		},
	}
}
</script>