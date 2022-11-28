<template>
	<Modal>
		<template slot="header">
			Pickup Date {{ this.PickupDate.pickup_date | formatDate("MM/DD/YYYY") }}
		</template>

		<template slot="body">
			<p class="mb tc">Select the orders <strong>that were not picked up</strong>.</p>
			<p class="muted">All orders not marked for cancellation will be assumed to have been fulfilled and picked up.</p>

			<table class="table table-condensed table-striped mv3">
				<thead>
					<tr>
						<th>Order #</th>
						<th>Agency</th>
						<th class="tc">Diapers / Pullups</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr v-if="Orders" v-for="Order in Orders" :key="Order.id"
						class="clickable"
						:class="{'bg-washed-red:important': orderSelected(Order) }"
						@click="toggleOrder(Order)">
						<td>#{{ Order.full_id }}</td>
						<td>{{ Order.agency.name }}</td>
						<td class="tc">
							{{ Order.diaper_count }}
							/
							{{ Order.pullup_count }}
						</td>
						<td class="tr">
							<i class="fa fa-check" :class="{'black-025': ! orderSelected(Order), 'dark-red': orderSelected(Order) }"></i>
						</td>
					</tr>
				</tbody>
			</table>

			<div v-if=" ! Orders">
				Loading Orders
			</div>
		</template>

		<template slot="buttons">
			<button class="btn btn-default" @click="cancel">
				Cancel
			</button>

			<button class="btn btn-success" @click="reconcile"
				:class="{'disabled': processing}">
				Reconcile
			</button>
		</template>
	</Modal>
</template>

<script>
import { Modal } from '../Modals';

export default {
	props: {
		PickupDate: {
			type: Object,
			required: true,
		},
	},

	components: { Modal },

	data() {
		return {
			selected_orders: [],
			processing: false,
			Orders: null,
		};
	},

	computed: {
		// Orders() {
		// 	return this.PickupDate.fulfillment.reduce( (Orders, Batch) => {
		// 		return Orders.concat(Batch.order);
		// 	}, []);
		// },
	},

	methods: {
		loadOrders(pickup_date_id) {
			axios.get(`/api/pickup-dates/${pickup_date_id}/orders`)
			.then( (response) => {
				this.ordersLoaded(response.data.data);
			})
			.catch(function(err) {
				this.$toast.warning({
					title: "Warning",
					message: "Could not load orders. Please refresh and try again.",
				});
			});
		},

		ordersLoaded(Orders) {
			this.Orders = Orders;
		},

		orderSelected(Order) {
			return this.selected_orders.indexOf(Order.id) >= 0;
		},

		toggleOrder(Order) {
			if (this.orderSelected(Order)) {
				this.selected_orders.splice( this.selected_orders.indexOf(Order.id), 1 );
			} else {
				this.selected_orders.push( Order.id );
			}
		},

		reconcile() {
			this.processing = true;
			this.$root.$emit('order/reconcile', this.Orders.filter( Order => ! this.orderSelected(Order) ));
			this.close();
			this.processing = false;
		},

		cancel() {
			this.$root.$emit('order/cancel/cancel');
			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		},
	},

	mounted() {
		this.loadOrders(this.PickupDate.id);
	}

}
</script>