<template>
	<div>
		<div class="flex flex-column justify-between h-100">
			<div class="pa oy-auto">
				<p class="b f2 pb">
					{{ PickupDate.pickup_date | formatDate("dddd, MMM D, YYYY") }}
				</p>

				<div class="flex justify-around items-center pb bb b--black-20 tc">
					<p>
						<span class="f2">{{ PickupDate.order_count }}</span>
						<br>
						<span class="wtl f3">Orders</span>
					</p>
					<p>
						<span class="f2">{{ PickupDate.children_count }}</span>
						<br>
						<span class="wtl f3">Children</span>
					</p>
					<p>
						<span class="f2">{{ PickupDate.diaper_count }}</span>
						<br>
						<span class="wtl f3">Diapers</span>
					</p>
					<p>
						<span class="f2">{{ PickupDate.pullup_count }}</span>
						<br>
						<span class="wtl f3">Pull-ups</span>
					</p>
				</div>
				<div class="flex justify-around items-center pb bb b--black-20 tc">
					<p>
						<span class="f2">{{ PickupDate.menstruator_count }}</span>
						<br>
						<span class="wtl f3">Menstruators</span>
					</p>
					<p>
						<span class="f2">{{ PickupDate.period_count }}</span>
						<br>
						<span class="wtl f3">Period Packets</span>
					</p>			
				</div>
				<p class="f2 b pt4">Item Summary</p>
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th class="w-40">Size</th>
							<th class="w-30 tc br bl b--black-10">Orders</th>
							<th class="w-30 tc">Quantity</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="Item in MergeData">
							<td>
								<!-- {{ Item.name }} -->
								{{ Item.name }}
								<br>
								<span class="f4 muted">{{ Item.category_name }}</span>
							</td>
							<td class="tc br bl b--black-10">
								{{ Item.order_count }}
							</td>
							<td class="tc ph4">
								{{ Item.quantity }}
							</td>
						</tr>
					</tbody>
				</table>

				<p class="f2 b pt4">Order Batches</p>
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th class="w-40">Date Created</th>
							<th class="tc w-30 br bl b--black-10"># Orders</th>
							<th class="tc w-30">Labels</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="Batch in PickupDate.Fulfillment">
							<td>{{ Batch.created_at | formatDate("MMM D, YYYY") }}</td>
							<td class="tc br bl b--black-10">{{ Batch.order_count }}</td>
							<td class="tr">
								<a class="btn btn-sm btn-primary"
									:class="{'disabled': processing}"
									:href="batchAssetDownloadURL(Batch)">
									<i class="fa fa-download"></i>
									Download
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Putting this here because IE11 does not remove position:absolute items from the flexbox layout flow
				documentation:
				- https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/12500543/
					- https://stackoverflow.com/questions/32991051/absolutely-positioned-flex-item-is-not-removed-from-the-normal-flow-in-ie11
			-->
			<a
				href="#"
				class="pxa pinr pint a-plain pa"
				@click="close">

				<i class="fa fa-times"></i>
			</a>

			<div class="bg-washed-blue pa bt b--black-20 fs-no fg-no">
				<button class="btn btn-block btn-success btn-alt"
					@click="reconcileClicked"
					:class="{'disabled': processing || !canReconcile}">
					<i class="fa fa-check"></i>
					Reconcile
				</button>

				<div v-if=" ! canReconcile">
					<span class="small b">Can not reconcile orders, as there are orders pending approval and/or export for fulfillment.</span>
				</div>

				<a :href="pickupDateAssetDownloadUrl()"
					class="btn btn-block btn-primary mt5"
					:class="{'disabled': processing}">
					<i class="fa fa-download fa-lg"></i>
					Download All
				</a>

				<button class="btn btn-block btn-default btn-ghost"
					:class="{'disabled': processing}"
					@click="close">
					<i class="fa fa-times"></i>
					Close
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import orderCounts from './orderCounts.js';
import ReconcileOrdersModal from '../../../Modals/ReconcileOrdersModal.vue';

export default {
	mixins: [ orderCounts ],

	props: {
		initialPickupDate: {
			required: true,
			type: Object,
		}
	},

	data() {
		return {
			PickupDate: this.initialPickupDate,
			processing: false,
		};
	},

	computed: {
		AggregateItems() {
			let Items = this.PickupDate.fulfillment.reduce( (aggregate, batch) => {
				return batch.order.reduce( (aggregate, order) => {
					return order.approved_item.reduce( (aggregate, item) => {

						if (undefined == aggregate[ item.product_id ]) {
							aggregate[ item.product_id ] = {
								...item,
								product: item.product,
								order_count: 0,
								quantity: 0,
							};
						}

						aggregate[ item.product_id ].order_count++;
						aggregate[ item.product_id ].quantity += item.quantity;

						return aggregate;
					}, aggregate);
				}, aggregate);
			}, {});

			return Items;
		},

		canReconcile() {
			return this.PickupDate.orders_pending == 0 && this.PickupDate.orders_pending_export == 0;
		},
		MergeData() {
			let mergedData = [];

			for (const property in this.PickupDate.ProductSummary) {
				let item = {};
				for (const propertyy in this.PickupDate.ProductSummary[property]) {
					let key = propertyy;
					let value = this.PickupDate.ProductSummary[property][propertyy];

					// Check if value contains 'boy' or 'girl' and remove it
					if (typeof value === 'string' && (value.includes('Boy') || value.includes('Girl'))) {
						console.log(value);
						value = value.replace(/Boy|Girl/g, '').trim();
					}

					item[key] = value;
				}

				// Check if the item's name already exists in mergedData
				const existingItemIndex = mergedData.findIndex(
					existingItem => existingItem.name === item.name
				);

				if (existingItemIndex !== -1) {
					// If item already exists, merge order_count and quantity
					mergedData[existingItemIndex].order_count += item.order_count;
					mergedData[existingItemIndex].quantity += item.quantity; // Assuming quantity exists in your data
				} else {
					// Otherwise, add the item to mergedData
					mergedData.push(item);
				}
			}

			// console.log(mergedData);
			return mergedData;
		},
	},

	methods: {
		batchAssetDownloadURL(Batch) {
			return "/admin/fulfillment/batch/" + Batch.id + "/download/all";
		},

		pickupDateAssetDownloadUrl() {
			return "/admin/fulfillment/pickup-date/" + this.PickupDate.id + "/download/all";
		},

		reconcileClicked() {
			if (this.processing) return;

			this.$store.commit({
				type: 'Modal/show',
				modalComponent: ReconcileOrdersModal,
				props: {
					PickupDate: this.PickupDate
				},
			});

			this.$root.$once('order/reconcile', this.reconcileOrder);
			this.$root.$once('order/reconcile/cancel', () => {
				this.$root.off('order/reconcile', this.reconcileOrder);
			});
		},

		reconcileOrder(fulfilledOrders) {
			if (this.processing) return;
			this.processing = true;

			axios.post(`/api/pickup-dates/${this.PickupDate.id}/reconcile`, { orders: fulfilledOrders.map( Order => Order.id ) })
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Orders have been reconciled successfully.",
					});
					this.updatePickupDate(response.data.data.PickupDate);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not reconciled orders. " + (response.data.message || ""),
				});
			})
			.catch( error => {
				this.$toast.error({
					title: "Error",
					message: "An error occurred while reconciling orders for this pickup date. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		updatePickupDate(PickupDate) {
			this.$emit('update', PickupDate);
			this.close();
		},

		close() {
			this.$emit('close');
		},

		onDocumentKeyup(evt) {
			if (evt.keyCode == 27) {
				this.close();
				evt.preventDefault();
			}
		},
	},

	mounted() {
		document.addEventListener('keyup', this.onDocumentKeyup);
	},

	beforeDestory() {
		document.removeEventListener('keyup', this.onDocumentKeyup);
	}
}
</script>