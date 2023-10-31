<template>
	<table class="table table-condensed table-striped">
		<thead>
			<tr>
				<th>Size</th>
				<th class="w-25 tc" v-if="isAdmin">On-hand</th>
				<th class="w-25 tc">Order Qty</th>
			</tr>
		</thead>

		<tbody>
			<tr v-for="Product in Products"
				v-if="getOrderedCount(Product.id) > 0"
				:class="getProductRowClasses(Product)">

				<th scope="row">
					<!-- <p class="ma0 pa0 wtn">{{ Product.name }}</p> -->
					{{ Product.name.replace(/Boy|Girl/g, '') }}
					<p class="f4 ma0 pa0 black-50">{{ Product.category.name }}</p>
				</th>
				<td class="tc br b--black-10 bl inventory-on-hand"
					v-if="isAdmin">
					{{ getProductOnHand(Product) }}
				</td>
				<td class="tc">
					{{ getOrderedCount(Product.id) }}
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script>
export default {
	props: {
		orderSummary: {
			required: false,
			type: [Array, Object],
		},

		order: {
			required: true,
			type: Object,
		},

		isAdmin: {
			required: false,
			type: Boolean,
			default: false,
		},
	},

	computed: {
		Products() {
			return this.Order.child.reduce( (Products, Child) => {
				if (Child.item) {
					if (undefined == Products[ Child.item.product_id ]) {
						Products[ Child.item.product_id ] = Child.item.product;
					}
				}

				return Products;
			}, {});
		},

		Order() {
			return this.order;
		},

		isDraft() {
			return this.Order.order_status == 'draft';
		},

		isPending() {
			return this.Order.order_status == 'pending_approval';
		}
	},

	methods: {
		getOrderedCount(product_id) {
			return this.Order.child.reduce( (count, Child) => {
				if (Child.item
					&& Child.item.product_id == product_id
					&& !Child.item.deleted_at) {

					return count + Child.item.quantity;
				}

				return count;
			}, 0);
		},

		getProductInventory(Product) {
			return Object.values(this.orderSummary).reduce( (Inventory, SummaryProduct) => {
				if (Inventory) return Inventory;

				if (SummaryProduct.id == Product.id) return SummaryProduct.Inventory || null;

				return null;
			}, null);
		},

		getProductOnHand(Product) {
			let Inventory = this.getProductInventory(Product);

			if (Inventory) {
				return Inventory.on_hand;
			}

			return null;
		},

		getProductRowClasses(Product) {
			if (!this.isAdmin) return {};

			let Inventory = this.getProductInventory(Product);
			if (!Inventory) return {};

			let projected = Inventory.on_hand;
			if (this.isDraft || this.isPending) {
				projected = projected - this.getOrderedCount(Product.id);
			}
			const category_id = Product.product_category_id;
			let low_water = null;
			let classes = {};

			if (category_id == 1) {
				low_water = window.HSDB.lowWater.diaper;
			} else {
				low_water = window.HSDB.lowWater.pullup;
			}

			if (low_water.critical > projected) {
				classes['inventory-critical'] = true;
			} else if (low_water.warning > projected) {
				classes['inventory-warning'] = true;
			}

			return classes;
		}
	},
}
</script>