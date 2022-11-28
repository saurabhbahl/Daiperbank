<template>
	<Modal>
		<span slot="header">
			<span class="dark-red b">WARNING!</span>
		</span>

		<div slot="body">
			<p>
				<span class="b">Approving this order will commit you to inventory that you do not have on hand.</span>
				<br>
				<br>
				See below for a list of items that will be over-comitted.
			</p>

			<table class="mv3 table table-condensed table-striped">
				<thead>
					<tr>
						<th>Diaper / Pull-up</th>
						<th class="w-20 tc br b--black-10 bl">On-hand</th>
						<th class="w-20 tc">Committed Quantity</th>
					</tr>
				</thead>

				<tbody>
					<tr v-for="Product in Products" :key="`product-${Product.id}`">
						<th scope="row">
							{{ Product.full_name }}
						</th>
						<td class="tc br b--black-10 bl">{{ Product.Inventory.on_hand || 0 }}</td>
						<td class="tc">{{ Product.Inventory.ordered || 0 }}</td>
					</tr>
				</tbody>
			</table>

			<p class="tc muted f4">
				You may approve this order anyway, or go back and modify the order to bring commitments to within on-hand quantity levels.
			</p>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				No, I want to make changes to the order
			</button>

			<button class="btn btn-success" @click="approve">
				Yes, approve this order with overages
			</button>
		</div>
	</Modal>

</template>

<script>
import { Modal } from '../Modals';
import nl2br from 'vue-nl2br';

export default {
	components: { Modal },

	props: {
		Order: {
			required: false,
			type: Object,
			default: function() { return {}; },
		},

		Children: {
			required: false,
			type: Array,
			default: function() { return []; },
		},
	},

	computed: {
		Products() {
			return Object.values(this.Children.reduce( (Products, ChildProduct) => {
				if (undefined === Products[ ChildProduct.Product.id ]) {
					Products[ ChildProduct.Product.id ] = {
						Inventory: {},
						...ChildProduct.Product,
					};

					Products[ ChildProduct.Product.id ].Inventory.ordered = 0;
				}

				Products[ ChildProduct.Product.id ].Inventory.ordered += ChildProduct.Child.item.quantity;

				return Products;
			}, {})).sort(function(a, b) {
				return a.id < b.id? -1 : 1;
			});
		},
	},

	methods: {
		approve() {
			this.$root.$emit('order/excess-confirm');
			this.close();
		},

		cancel() {
			this.$root.$emit('order/excess-confirm/cancel');
			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>