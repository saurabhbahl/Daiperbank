<template>
	<div class="bb b--light-gray">
		<div class="flex justify-between bl br b--light-gray pa">
			<div class="w-10 fg-no fs-no tl">
				<a href="#"
					@click="toggle"
					class="a-plain">
						<i class="fa clickable"
							:class="{
								'fa-caret-up': visible,
								'fa-caret-down': !visible,
							}"
						></i>
				</a>
			</div>
			<div class="fg">
				<p>
					<a v-if="adjustment.order_id" :href="'/admin/order/' + adjustment.order_id">
						{{ adjustment.detail_string }}
					</a>
					<span v-else>
						{{ adjustment.detail_string }}
					</span>
				</p>
				<p class="f4 muted">
					{{ adjustment.adjustment_note }}
				</p>
			</div>
			<div class="w-20 fg-no fs-no tr">
				{{ moment(adjustment.adjustment_datetime).format('MMM D, YYYY') }}
			</div>
		</div>
		<div v-if="visible" class="pl5 bt brw0 b--light-gray" style="margin-right: -1px;">
			<table class="table table-condensed table-striped inventory-detail br b--light-gray">
				<thead>
					<tr>
						<th class="inventory-detail__column mod--header mod--product">Product</th>
						<th class="inventory-detail__column mod--header mod--credit">Incoming</th>
						<th class="inventory-detail__column mod--header mod--debit brw0">Outgoing</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="Inventory in adjustment.inventory"
						class="inventory-adjustment__product"
						:key="Inventory.id">

						<td class="inventory-detail__column mod--product">
							{{ Inventory.product.full_name }} Hello
						</td>
						<td class="inventory-detail__column mod--credit">
							{{ isInventoryCredit(Inventory)? Inventory.amount : '' }}
						</td>
						<td class="inventory-detail__column mod--debit brw0">
							{{ isInventoryDebit(Inventory)? Inventory.amount : '' }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import moment from 'moment';

export default {
	props: ['adjustment'],
	data() {
		return {
			visible: false,
		};
	},

	methods: {
		toggle() {
			this.visible = !this.visible;
		},

		isInventoryCredit(Inventory) {
			return Inventory.txn_type == 'credit';
		},

		isInventoryDebit(Inventory) {
			return Inventory.txn_type == 'debit';
		},

		moment(str) {
			return moment(str);
		},
	}
}

/*
<tr class="inventory-adjustment">
		<td class="inventory-ledger__column mod--toggle">
			<a href="#" @click="toggle">-</a>
		</td>
		<td class="inventory-ledger__column mod--type">
			<a v-if="adjustment.order_id" :href="'/admin/order/' + adjustment.order_id">
				{{ adjustment.detail_string }}
			</a>
			<span v-else>
				{{ adjustment.detail_string }}
			</span>
		</td>
		<td class="inventory-ledger__column mod--note">
			{{ adjustment.adjustment_note }}
		</td>
		<td class="inventory-ledger__column mod--date">
			{{ moment(adjustment.adjustment_datetime).format('MMM D, YYYY') }}
		</td>
	</tr>
	<tr v-if="visible" class="inventory-adjustment__detail">
		<td colspan="4">
			<table class="table table-condensed table-bordered table-striped inventory-detail">
				<thead>
					<tr>
						<th class="inventory-detail__column mod--header mod--product">Product</th>
						<th class="inventory-detail__column mod--header mod--credit">Incoming</th>
						<th class="inventory-detail__column mod--header mod--debit">Outgoing</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="Inventory in adjustment.inventory"
						class="inventory-adjustment__product"
						:key="Inventory.id">

						<td class="inventory-detail__column mod--product">
							{{ Inventory.product.full_name }}
						</td>
						<td class="inventory-detail__column mod--credit">
							{{ isInventoryCredit(Inventory)? Inventory.amount : '' }}
						</td>
						<td class="inventory-detail__column mod--debit">
							{{ isInventoryDebit(Inventory)? Inventory.amount : '' }}
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
 */

</script>