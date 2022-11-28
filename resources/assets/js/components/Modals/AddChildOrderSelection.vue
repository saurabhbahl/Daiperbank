<template>
	<Modal>
		<span slot="header">
			Add Child to Order
		</span>

		<div slot="body">
			<p v-if="Orders.length"
				class="f3">Select the order you'd like to add this child to, from the list below:</p>

			<table v-if="Orders.length"
				class="table table-condensed table-striped">
				<thead>
					<tr>
						<th style="width: 5%;">&nbsp;</th>
						<th class="w-65">Order Number</th>
						<th class="w-30">Created On</th>
					</tr>
				</thead>

				<tbody>
					<tr v-for="Order in Orders"
						class="clickable"
						@click="toggleOrderSelection(Order.id)">
						<td>
							<i v-if="selected_order_id == Order.id"
								class="fa fa-check dark-green">
							</i>
						</td>
						<td>#{{ Order.full_id }}</td>
						<td>{{ Order.created_at | formatDate("MM/DD/YYYY @ h:mm a") }}</td>
					</tr>

					<tr class="clickable"
						@click="toggleOrderSelection(-1)">
						<td>
							<i v-if="selected_order_id == -1"
								class="fa fa-check dark-green">
							</i>
						</td>
						<td colspan="2">
							<p class="i">
								Create a new order draft.
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<p v-if=" ! Orders.length"
				class="f3 tc">
				When clicking below, a new order will be created in <span class="i">Draft</span> status, with this child added to it.
			</p>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				Cancel
			</button>

			<button class="btn btn-primary" :class="{ 'disabled': ! selected_order_id }" @click="addAndStay">
				Add Child
			</button>

			<button class="btn btn-primary" :class="{ 'disabled': ! selected_order_id }" @click="addAndGo">
				Add & View Order
			</button>
		</div>
	</Modal>

</template>

<script>
import { Modal } from '../Modals';

export default {
	props: {
		Orders: {
			type: Array,
			required: true,
		},
	},

	components: { Modal },

	data() {
		return {
			selected_order_id: this.Orders.length? null : -1,
		}
	},

	methods: {
		toggleOrderSelection(id) {
			if (this.selected_order_id == id) {
				this.selected_order_id = null;
			} else {
				this.selected_order_id = id;
			}

			return false;
		},

		addAndStay() {
			if ( ! this.selected_order_id) return false;

			let order_id = this.selected_order_id > 0? this.selected_order_id : null;
			this.$root.$emit('child/add-to-order', order_id, 'continue');
			this.close();
		},

		addAndGo() {
			if ( ! this.selected_order_id) return false;

			let order_id = this.selected_order_id > 0? this.selected_order_id : null;
			this.$root.$emit('child/add-to-order', order_id, 'finished');
			this.close();
		},

		cancel() {
			this.$root.$emit('child/add-to-order/cancel');
			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>