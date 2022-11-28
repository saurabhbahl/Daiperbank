<template>
	<div class="pxr">
		<div class="pa0 bg-white br b--black-20 flex-auto">
			<div class="flex flex-column" style="height:100%;">
				<div class="pa flex justify-start items-start fg-no fs-no bg-washed-blue">
					<div class="mr4">
						<p class="muted bb b--black-20">Order Number</p>
						<p class="b f2 pr5">
							#{{ Order.full_id }}
						</p>
					</div>

					<div class="mr4">
						<p class="muted bb b--black-20">Order Status</p>
						<p class="b f2 pr5" :class="orderStatusClasses()">
							{{ Order.readable_status }}
						</p>
					</div>
				</div>

				<TabBar
					class="fg-no bg-washed-blue"
					:tabs="tabDict"
					:initial-active="activeTab"
					active-classes="bg-white"
					@selected="onTabSelected">
				</TabBar>

				<div class="flex-auto oy-scroll">
					<TabContent
						ref="children"
						:active="activeTab == 'children'">

						<ChildItem
							v-for="Child in SortedChildren"
							:key="'order_child_' + Child.id"
							class="flex justify-between items-start bb b--black-20 pa clickable a:hov-nou a-plain hov:bg-washed-blue"
							:class="getChildItemClasses(Child)"
							:child="Child"
							:editable="false"

							@click="selectChild(Child)"
						></ChildItem>
					</TabContent>

					<TabContent
						ref="notes"
						:active="activeTab == 'notes'">

						<OrderNotes
							:order="Order"

							@create="updateNotes"
							@delete="noteDeleted"
						></OrderNotes>
					</TabContent>
				</div>
			</div>
		</div>

		<div class="col-xs-4 pa0 bg-white flex flex-column justify-between">
			<div class="pa o-hidden oy-auto">
				<p class="f2 b">
					Pickup date
				</p>

				<div class="flex justify-between items-center">
					<p class="f3">
						{{ Order.pickup_on | formatDate("MMM D, YYYY @ h:mma") }}
					</p>
				</div>

				<p class="f2 b mv3">Order Summary</p>

				<OrderSummary
					:order="Order"
				></OrderSummary>
			</div>

			<div class="bg-washed-blue pa bt b--black-20 fs-no fg-no">
				<div v-if="isDraft || isPending">
					<form method="post" ref="cancel-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="action" value="cancel">

						<button
							name="action"
							value="cancel"
							ref="cancel-order-btn"
							class="btn btn-block btn-danger btn-alt mb4"
							@click.stop.prevent="promptCancelOrder">
							<i class="fa fa-lg fa-times"></i>
							Cancel Order
						</button>
					</form>
				</div>

				<form method="post" ref="clone-form">
					<input type="hidden" name="_token" v-model="csrfToken">
					<input type="hidden" name="action" value="clone">

					<button
						name="action"
						value="clone"
						ref="clone-order-btn"
						class="btn btn-block btn-default">
						<i class="fa fa-lg fa-clone"></i>
						Clone Order
					</button>
				</form>
			</div>
		</div><!-- end left column panel -->

		<ChildDetail
			v-if="CurrentOrderChild"
			class="pxa pinr pint pinb w-33 bg-white shadow-2"
			:is-editable="false"
			:initialChild="CurrentOrderChild"
			:key="'child-detail-' + CurrentOrderChild.id"
			:product-categories="productCategories"

			@close="closeChildDetail"
		></ChildDetail>
	</div>
</template>

<script>
import ChildItem from './AgentChildItem.vue';
import TabBar from '../General/TabBar.vue';
import TabContent from '../General/TabContent.vue';
import OrderSummary from './OrderSummary.vue';
import ChildDetail from './AgentChildDetail.vue';
import OrderNotes from './Admin/OrderNotes.vue';
import CancelOrderConfirmationModal from '../Modals/CancelOrderConfirmation.vue';

export default {
	components: { ChildItem, ChildDetail, TabBar, TabContent , OrderSummary, OrderNotes },

	props: {
		initialOrder: {
			type: Object,
			required: true,
		},

		productCategories: {
			type: Array,
			required: true,
		},
	},

	data() {
		return {
			isAdmin: false, // do we need this?
			current_tab: null,
			Order: this.initialOrder,
			CurrentOrderChild: null,
		};
	},

	computed: {
		activeTab() {
			if (this.current_tab == null) {
				return 'children';
			}

			return this.current_tab;
		},

		csrfToken() {
			return window.csrf;
		},

		tabDict() {
			return {
				"children": {
					label: "Children",
				},

				"notes": {
					label: "Comments",
					classes: "pull-right mr4",
					badge: this.noteBadgeCount(),
				},

				"print": {
					label: "Print <i class=\"fa fa-print\"></i>",
					classes: "pull-right mr4",
					link: "/order/" + this.Order.id + "/print",
				}
			};
		},

		isDraft() {
			return this.Order.order_status == 'draft';
		},

		isPending() {
			return this.Order.order_status == 'pending_approval';
		},

		isApproved() {
			return this.Order.order_status == 'pending_pickup' || this.Order.order_status == 'fulfilled';
		},

		isFulfilled() {
			return this.Order.order_status == 'fulfilled';
		},

		isCancelled() {
			return this.Order.order_status == 'cancelled';
		},

		isRejected() {
			return this.Order.order_status == 'rejected';
		},

		SortedChildren() {
			return [...this.Order.child].sort( (a, b) => {
				return a.name < b.name? -1 : 1;
			});
		},

		// selectedPickupDate() {
		// 	return this.PickupDates.filter( Pickup => {
		// 		return Pickup.id == this.Order.pickup_date_id;
		// 	})[0];
		// },
	},

	methods: {
		cancelOrder(Order, reason) {
			if (Order.id != this.Order.id) {
				return;
			}

			this.reason = reason;

			this.$nextTick(() => {
				this.$refs['cancel-form'].submit();
			});
		},

		promptCancelOrder() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: CancelOrderConfirmationModal,
				props: {
					Order: this.Order,
					isAdmin: false,
				}
			});

			this.$root.$once('order/cancel', this.cancelOrder);
			this.$root.$once('order/cancel/cancel', () => {
				this.$root.$off('order/cancel', this.cancelOrder);
			});
		},

		onTabSelected(tab_id, tab) {
			this.current_tab = tab_id;

			// prompt the user about changing tabs while viewing child details
			this.CurrentOrderChild = null;
		},

		parsePickupDate(date) {
			let parsed = String(date).substr(0, 19);

			return parsed;
		},

		updateNotes(note, notes) {
			this.Order.note = notes;
		},

		noteDeleted(AllNotes) {
			this.updateNotes(null, AllNotes);
		},

		noteBadgeCount() {
			return this.Order.note.reduce( (count, Note) => {
				if (Note.flag_share || this.isAdmin) {
					count = (count === null? 1 : count + 1);
				}

				return count;
			}, null);
		},

		orderListUrl() {
			return '/order';
		},

		orderStatusClasses() {
			let classes = {};

			if (this.isRejected || this.isCancelled) classes['dark-red'] = true;
			if (this.isApproved || this.isFulfilled) classes['dark-green'] = true;
			if (this.isDraft || this.isPending) classes['bootstrap-info-blue'] = true;

			return classes;
		},

		selectChild(Child) {
			if (! (Child instanceof Object)) {
				Child = this.getChildById(Child);
			}

			this.CurrentOrderChild = Child;
		},

		getChildItemClasses(Child) {
			if (this.CurrentOrderChild && Child.id == this.CurrentOrderChild.id) {
				return {
					'bl blw3 bl--blue': true,
				};
			}

			return {};
		},

		closeChildDetail() {
			this.CurrentOrderChild = null;
		},
	},
};
</script>