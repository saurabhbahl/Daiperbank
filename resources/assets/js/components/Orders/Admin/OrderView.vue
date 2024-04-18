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
						<p class="muted bb b--black-20">Agency</p>
						<p class="b f2 pr5">
							{{ Order.agency.name }}
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
						v-if="isAdmin"
						ref="pending"
						:active="activeTab == 'pending'">

						<ChildItem
							v-for="Child in PendingChildren"
							:key="'order_child_' + Child.id"
							class="flex justify-between items-start bb b--black-20 pa clickable a:hov-nou a-plain hov:bg-washed-blue"
							:class="getChildItemClasses(Child)"
							:child="Child"
							:is-editable="isOrderEditable"
							:isAdmin="isAdmin"
							:processing="processing"
							:canApprove="isPending"

							@click="onClick"
							@approve="approveChild(Child.id)"
						></ChildItem>

						<div v-if=" ! PendingChildren.length">
							<p class="tc f2 wtl pa4">
								There are no additional children pending approval for this order.
							</p>
						</div>

					</TabContent>

					<TabContent
						v-if="isAdmin"
						ref="approved"
						:active="activeTab == 'approved'">

						<ChildItem
							v-for="Child in ApprovedChildren"
							:key="'order_child_' + Child.id"
							class="flex justify-between items-start bb b--black-20 pa clickable a:hov-nou a-plain hov:bg-washed-blue"
							:class="getChildItemClasses(Child)"
							:child="Child"
							:is-editable="isOrderEditable"
							:isAdmin="isAdmin"
							:processing="processing"

							@click="onClick"
							@unapprove="unApproveChild(Child.id)"
						></ChildItem>

						<div v-if=" ! ApprovedChildren.length">
							<p class="tc f2 wtl pa4">
								No children have been approved for this order.
							</p>
						</div>
					</TabContent>

					<TabContent
						v-if="isAdmin"
						ref="removed"
						:active="activeTab == 'removed'">

						<ChildItem
							v-for="Child in RemovedChildren"
							:key="'order_child_' + Child.id"
							class="flex justify-between items-start bb b--black-20 pa clickable a:hov-nou a-plain hov:bg-washed-blue"
							:class="getChildItemClasses(Child)"
							:child="Child"
							:is-editable="isOrderEditable"
							:isAdmin="isAdmin"
							:processing="processing"

							@click="onClick"
							@restore="restoreChild(Child)"
						></ChildItem>

						<div v-if=" ! RemovedChildren.length">
							<p class="tc f2 wtl pa4">
								No children have been removed from this order.
							</p>
						</div>
					</TabContent>

					<TabContent
						v-if=" ! isAdmin"
						ref="children"
						:active="activeTab == 'children'">

						<ChildItem
							v-for="Child in SortedChildren"
							:key="'order_child_' + Child.id"
							class="flex justify-between items-start bb b--black-20 pa clickable a:hov-nou a-plain hov:bg-washed-blue"
							:class="getChildItemClasses(Child)"
							:child="Child"
							:editable="false"

							@click="onClick"
						></ChildItem>
					</TabContent>

					<TabContent
						ref="notes"
						:active="activeTab == 'notes'">

						<OrderNotes
							:order="this.Order"
							:is-admin="isAdmin"

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

				<div v-if=" ! editing_pickup_date"
					class="flex justify-between items-center">
					<p class="f3">
						{{ Order.pickup_on | formatDate("MMM D, YYYY @ h:mma") }}
					</p>

					<a href="#pickup-date" class="db fr btn btn-sm btn-primary"
						v-if="isAdmin && ! isFulfilled && ! isRejected && ! isCancelled "
						@click.prevent="toggleEditPickupDate">
						<i class="fa fa-calendar-o"></i>
						Change date
					</a>
				</div>

				<PickupDateSelector
					v-if="editing_pickup_date"
					:pickup-dates="PickupDates"
					:initial-date="pickup_date_id"
					@change="pickupDateChanged"
				></PickupDateSelector>

				<p class="tr mb" v-if="editing_pickup_date">
					<a href="#" @click.prevent="cancelEditPickupDate">Cancel</a>
				</p>

				<p class="f2 b mb">Order Summary</p>

				<OrderSummary
					:order-summary="OrderSummary"
					:order="Order"
					:isAdmin="isAdmin"
				></OrderSummary>
			</div>

			<div class="bg-washed-blue pa bt b--black-20 fs-no fg-no">
				<div v-if="isDraft">
					<form action="" method="post" ref="discard-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="action" value="discard">

						<button
							name="action"
							value="discard"
							ref="discard-order-btn"
							class="btn btn-block btn-danger btn-alt mb4"
							:class="{'disabled': processing}"
							@click.stop.prevent="promptDiscardOrder">
							<i class="fa fa-lg fa-trash-o"></i>
							Discard Order
						</button>
					</form>

					<form action="" method="post" ref="approve-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="pickup_date_id" v-model="pickup_date_id">
						<button
							type="submit"
							name="action"
							value="submit"
							ref="submit-order-btn"
							class="btn btn-block btn-success"
							:class="{'disabled':( ! isValid()) || processing }">
							<i class="fa fa-lg fa-check"></i>
							Submit Order
						</button>
					</form>
				</div>

				<div v-if="isPending">
					<form action="" method="post" ref="reject-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="action" value="reject">
						<input type="hidden" name="reason" :value="reason">

						<button
							name="action"
							value="reject"
							ref="reject-order-btn"
							class="btn btn-block btn-danger btn-alt mb4"
							@click.stop.prevent="promptRejectOrder"
							:class="{'disabled': processing}">
							<i class="fa fa-lg fa-trash-o"></i>
							Reject Order
						</button>
					</form>

					<form action="" method="post" ref="clone-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="action" value="clone">

						<button
							name="action"
							value="clone"
							ref="clone-order-btn"
							class="btn btn-block btn-default mb4"
							:class="{'disabled': processing}">
							<i class="fa fa-lg fa-clone"></i>
							Clone Order
						</button>
					</form>

					<form action="" method="post" ref="approve-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="pickup_date_id" v-model="pickup_date_id">
						<input type="hidden" name="action" value="approve">
						<button
							ref="approve-order-btn"
							class="btn btn-block btn-success"
							:class="{'disabled': ( ! isValid()) || processing }"
							@click.prevent="confirmMassApproval">
							<i class="fa fa-lg fa-check"></i> Approve
						</button>
					</form>
				</div>

				<div v-if="isApproved">
					<div v-if=" ! editing_pickup_date">
						<form action="" method="post" ref="cancel-form" v-if=" ! isFulfilled">
							<input type="hidden" name="_token" v-model="csrfToken">
							<input type="hidden" name="action" value="cancel">
							<input type="hidden" name="reason" :value="reason">

							<button
								name="action"
								value="cancel"
								ref="cancel-order-btn"
								class="btn btn-block btn-danger btn-alt mb4"
								:class="{'disabled': processing}"
								@click.stop.prevent="promptCancelOrder">
								<i class="fa fa-lg fa-times"></i>
								Cancel Order
							</button>
						</form>

						<form action="" method="post" ref="return-form" v-if=" isFulfilled">
							<input type="hidden" name="_token" v-model="csrfToken">
							<input type="hidden" name="action" value="return">
							<input type="hidden" name="reason" :value="reason">

							<button
								name="action"
								value="cancel"
								ref="cancel-order-btn"
								class="btn btn-block btn-danger btn-alt mb4"
								:class="{'disabled': processing}"
								@click.stop.prevent="promptReturnOrder">
								<i class="fa fa-lg fa-times"></i>
								Return Order
							</button>
						</form>

						<form action="" method="post" ref="clone-form">
							<input type="hidden" name="_token" v-model="csrfToken">
							<input type="hidden" name="action" value="clone">

							<button
								name="action"
								value="clone"
								ref="clone-order-btn"
								class="btn btn-block btn-default"
								:class="{'disabled': processing}">
								<i class="fa fa-lg fa-clone"></i>
								Clone Order
							</button>
						</form>
					</div>

					<div v-if="editing_pickup_date">
						<form method="post" ref="reschedule-form">
							<input type="hidden" name="_token" v-model="csrfToken">
							<input type="hidden" name="pickup_date_id" v-model="pickup_date_id">
							<button
								type="submit"
								name="action"
								value="reschedule"
								ref="reschedule-btn"
								class="btn btn-block btn-success mb4"
								:class="{'disabled': (! isValid()) || processing }">
								<i class="fa fa-calendar-o"></i>
								Reschedule Pickup
							</button>
						</form>

						<button class="btn btn-block btn-default"
							@click="toggleEditPickupDate">
							Cancel
						</button>
					</div>
				</div>

				<div v-if="isCancelled">
					<form action="" method="post" ref="clone-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="action" value="clone">

						<button
							name="action"
							value="clone"
							ref="clone-order-btn"
							class="btn btn-block btn-default"
							:class="{'disabled': processing}">
							<i class="fa fa-lg fa-clone"></i>
							Clone Order
						</button>
					</form>
					<form action="" method="post" ref="cancel-to-approve-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="pickup_date_id" v-model="pickup_date_id">
						<input type="hidden" name="action" value="canceltoapprove"> 
						<button
							ref="approve-order-btn"
							class="btn btn-block btn-success"
							>
							<i class="fa fa-lg fa-check"></i> FulFill
						</button>
					</form>
				</div>
				<div v-if="isRejected">
					<form action="" method="post" ref="clone-form">
						<input type="hidden" name="_token" v-model="csrfToken">
						<input type="hidden" name="action" value="clone">

						<button
							name="action"
							value="clone"
							ref="clone-order-btn"
							class="btn btn-block btn-default"
							:class="{'disabled': processing}">
							<i class="fa fa-lg fa-clone"></i>
							Clone Order
						</button>
					</form>
				</div>
			</div>
		</div><!-- end left column panel -->

		<ChildDetail
			class="pxa pinr pint pinb w-33 bg-white shadow-2"

			v-if="CurrentOrderChild"
			ref="child-details"
			:key="'child-detail-' + CurrentOrderChild.id"
			:is-editable="isOrderEditable"
			:initial-child="CurrentOrderChild"
			:product-categories="productCategories"
			:all-children="AllChildren"
			:isAdmin="isAdmin"
			:isFulfilled="isFulfilled"

			@close="closeChildDetail"
			@save="saveChild"
			@remove="removeChild"
			@restore="restoreChild"
			@unapprove="unApproveChild"
		></ChildDetail>
	</div>
</template>

<script>
import ChildItem from './AdminChildItem.vue';
import TabBar from '../../General/TabBar.vue';
import TabContent from '../../General/TabContent.vue';
import OrderSummary from './OrderSummary.vue';
import ChildDetail from './AdminChildDetail.vue';
import OrderNotes from './OrderNotes.vue';
import RejectOrderConfirmationModal from '../../Modals/RejectOrderConfirmation.vue';
import CancelOrderConfirmationModal from '../../Modals/CancelOrderConfirmation.vue';
import DiscardOrderConfirmationModal from '../../Modals/DiscardOrderConfirmation.vue';
import OrderMassChildApprovalModal from '../../Modals/OrderMassChildApproval.vue';
import OrderConfirmExcessQuantityModal from '../../Modals/OrderConfirmExcessQuantity.vue';
import ReturnOrderConfirmationModal from '../../Modals/ReturnOrderConfirmation.vue';
import PickupDateSelector from '../PickupDateSelector.vue';

export default {
	components: { ChildItem, ChildDetail, TabBar, TabContent , OrderSummary, PickupDateSelector, OrderNotes },

	props: {
		initialOrder: {
			type: Object,
			required: true,
		},

		initialOrderSummary: {
			type: [Array, Object],
			required: true,
		},

		pickupDates: {
			type: Array,
			required: true,
		},

		productCategories: {
			type: Array,
			required: true,
		},

		isAdmin: {
			type: Boolean,
			required: false,
			default: false,
		},
	},

	data() {
		return {
			current_tab: null,
			PickupDates: this.pickupDates,
			Order: this.initialOrder,
			AllChildren: [],
			OrderSummary: this.initialOrderSummary,
			CurrentOrderChild: null,
			reason: null, // reason for cancellation, rejection, whatever.
			pickup_date_id: this.initialOrder.pickup_date_id,
			editing_pickup_date: this.initialOrder.pickup_date_id? false : true,
			processing: false,
		};
	},

	computed: {
		activeTab() {
			if (this.current_tab == null) {
				if (['pending_pickup', 'fulfilled'].indexOf(this.Order.order_status) >= 0) {
					return 'approved';
				}

				return this.isAdmin? 'pending' : 'children';
			}

			return this.current_tab;
		},

		csrfToken() {
			return window.csrf;
		},

		tabDict() {
			return {
				"pending": {
					label: "Pending Review",
					badge: this.PendingChildren.length,
					classes: {
						'disabled': !this.isOrderEditable && !this.PendingChildren.length,
					},
				},
				"approved": {
					label: "Approved",
					classes: {
						'disabled': !this.isOrderEditable && !this.ApprovedChildren.length,
					},
				},
				"removed": {
					label: "Removed",
					classes: {
						'disabled': !this.isOrderEditable && !this.RemovedChildren.length,
					},
				},
				"notes": {
					label: "Comments",
					classes: "pull-right mr4",
					badge: this.noteBadgeCount(),
				},
			};
		},

		isOrderEditable() {
			// Made pending orders editable only. This prevents the
			// user from approving a child (or an order) when it's in draft status
			// and there may not even be a diaper selected for the child.
			return this.isPending;// || this.isDraft;
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

		PendingChildren() {
			return this.SortedChildren.filter( Child => {
				return ! Child.item || (! Child.item.flag_approved && ! Child.item.deleted_at);
			});
		},

		ApprovedChildren() {
			return this.SortedChildren.filter( Child => {
				return Child.item && !! Child.item.flag_approved && ! Child.item.deleted_at;
			});
		},

		RemovedChildren() {
			return this.SortedChildren.filter( Child => {
				return Child.item && !! Child.item.deleted_at;
			});
		},

		selectedPickupDate() {
			return this.PickupDates.filter( Pickup => {
				return Pickup.id == this.Order.pickup_date_id;
			})[0];
		},
	},

	methods: {
		cancelOrder(Order, reason) {
			if (Order.id != this.Order.id) {
				return;
			}

			this.processing = true;
			this.reason = reason;

			this.$nextTick(() => {
				this.$refs['cancel-form'].submit();
			});
		},

		discardOrder(Order) {
			if (Order.id != this.Order.id) {
				return;
			}

			this.processing = true;

			this.$nextTick(() => {
				this.$refs['discard-form'].submit();
			});
		},

		rejectOrder(Order, reason) {
			if (Order.id != this.Order.id) {
				return;
			}

			this.processing = true;
			this.reason = reason;

			this.$nextTick(() => {
				this.$refs['reject-form'].submit();
			});
		},

		returnOrder(Order, reason) {
			if (Order.id != this.Order.id) {
				return;
			}

			this.processing = true;
			this.reason = reason;

			this.$nextTick(() => {
				this.$refs['return-form'].submit();
			});
		},

		promptCancelOrder() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: CancelOrderConfirmationModal,
				props: {
					Order: this.Order,
				}
			});

			this.$root.$once('order/cancel', this.cancelOrder);
			this.$root.$once('order/cancel/cancel', () => {
				this.$root.$off('order/cancel', this.cancelOrder);
			});
		},

		promptDiscardOrder() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: DiscardOrderConfirmationModal,
				props: {
					Order: this.Order,
				},
			});

			this.$root.$once('order/discard', this.discardOrder);
			this.$root.$once('order/discard/cancel', () => {
				this.$root.$off('order/discard', this.discardOrder);
			});
		},

		promptRejectOrder() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: RejectOrderConfirmationModal,
				props: {
					Order: this.Order,
				}
			});

			this.$root.$once('order/reject', this.rejectOrder);
			this.$root.$once('order/reject/cancel', () => {
				this.$root.$off('order/reject', this.rejectOrder);
			});
		},

		promptReturnOrder() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: ReturnOrderConfirmationModal,
				props: {
					Order: this.Order,
				}
			});

			this.$root.$once('order/return', this.returnOrder);
			this.$root.$once('order/return/cancel', () => {
				this.$root.$off('order/return', this.returnOrder);
			});
		},

		closeChildDetail() {
			this.CurrentOrderChild = null;
		},

		getChildItemClasses(Child) {
			if (this.CurrentOrderChild && Child.id == this.CurrentOrderChild.id) {
				return {
					'bl blw3 bl--blue': true,
				};
			}

			return {};
		},

		confirmMassApproval() {
			if (this.PendingChildren.length == 0) {
				this.checkQuantityAndApprove();
				return;
			}

			this.$store.commit({
				type: 'Modal/show',
				modalComponent: OrderMassChildApprovalModal,
				props: {
					Order: this.Order,
					PendingChildren: this.PendingChildren,
				}
			});

			this.$root.$once('order/approve', this.checkQuantityAndApprove);
			this.$root.$once('order/approve/cancel', () => {
				this.$root.$off('order/approve', this.checkQuantityAndApprove);
			});
		},

		checkQuantityAndApprove() {
			let exceedsInventory = [ ...this.PendingChildren, ...this.ApprovedChildren ].reduce( (excessChildren, Child) => {
				let productInventory = this.getProductInventory(Child.item.product_id);
				if ( ! productInventory.Inventory
					|| ! productInventory.Inventory.on_hand
					|| productInventory.Inventory.on_hand < Child.item.quantity) {
					excessChildren.push({
						Child,
						Product: productInventory,
					});
				}

				return excessChildren;
			}, []);

			if (exceedsInventory.length == 0) {
				return this.approveOrder();
			}

			this.$nextTick(() => {
				this.$store.commit({
					type: 'Modal/show',
					modalComponent: OrderConfirmExcessQuantityModal,
					props: {
						Order: this.Order,
						Children: exceedsInventory,
					}
				});
			});

			this.$root.$once('order/excess-confirm', this.approveOrder);
			this.$root.$once('order/excess-confirm/cancel', () => {
				this.$root.$off('order/excess-confirm', this.approveOrder);
			});
		},

		approveOrder() {
			this.processing = true;
			this.$refs['approve-form'].submit();
		},

		getProductInventory(product_id) {
			let Product = Object.values(this.OrderSummary).filter( Product => {
				return Product.id == product_id;
			});

			if (Product.length) {
				return Product[0];
			}

			return null;
		},

		approveChild(child_id) {
			if (this.processing) return;
			this.processing = true;

			child_id = typeof child_id == 'object'? child_id.id : child_id;

			axios.post(`/api/orders/${this.Order.id}/child/${child_id}/approve`)
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child has been approved.",
					});
					this.updateChild(response.data.data.Child);
					this.closeChildDetail();
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not approve child. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.catch( error => {
				let response = error.response;
				this.$toast.error({
					title: "Error",
					message: "Could not approve child. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		saveChild(Child, data) {
			if (this.processing) return;
			this.processing = true;

			this.$refs['child-details'].clearErrors();

			axios.post(`/api/orders/${this.Order.id}/child/${Child.id}`, data).then( response => {
				this.processing = false;
				if (response.data.success) {
					// maybe remove this message so we don't get duplicate success messages, since we save then approve in 2 requests
					// this.$toast.success({
					// 	title: "Success",
					// 	message: "Child info has been updated.",
					// });
					this.onChildSaved(response.data.data.Child);
					return
				}

				this.$toast.error({
					title: "Error",
					message: "Could not update child info. " + (response.data.message || " An unexpected error occurred"),
				});
			})
			.catch( error => {
				let response = error.response;
				this.processing = false;

				if (response.status == 422) {
					this.$refs['child-details'].displayErrors(response.data.data.errors || null);
				}

				this.$toast.error({
					title: "Error",
					message: "Could not update child info. " + (response.data.message || "An unexpected error occurred"),
				});
			})
		},

		onChildSaved(Child) {
			this.updateChild(Child);
			this.approveChild(Child.id);
		},

		onClick(Child) {
			this.CurrentOrderChild = Child;
		},

		removeChild(Child) {
			if (this.processing) return;
			this.processing = true;

			axios.delete(`/api/orders/${this.Order.id}/child/${Child.id}`)
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child has been removed from order.",
					});
					this.onChildRemoved(response.data.data.Child);
					this.closeChildDetail();
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not remove child from order. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.catch( error => {
				let response = error.response;
				this.$toast.error({
					title: "Error",
					message: "Could not remove child from order. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		onChildRemoved(Child) {
			this.updateChild(Child);
		},

		restoreChild(child_id) {
			if (this.processing) return;
			this.processing = true;

			child_id = typeof child_id == 'object'? child_id.id : child_id;

			axios.post(`/api/orders/${this.Order.id}/child/${child_id}/restore`)
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child has been restored to Pending",
					});
					this.updateChild(response.data.data.Child);
					this.closeChildDetail();
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not restore child to Pending. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.catch( error => {
				let response = error.response;
				this.$toast.error({
					title: "Error",
					message: "Could not restore child to Pending. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		unApproveChild(child_id) {
			if (this.processing) return;
			this.processing = true;

			child_id = typeof child_id == 'object'? child_id.id : child_id;

			axios.delete(`/api/orders/${this.Order.id}/child/${child_id}/approve`)
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child has been restored to Pending",
					});
					this.updateChild(response.data.data.Child);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not restore child to Pending. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.catch( error => {
				let response = error.response;
				this.$toast.error({
					title: "Error",
					message: "Could not restore child to Pending. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		onTabSelected(tab_id, tab) {
			this.current_tab = tab_id;

			// prompt the user about changing tabs while viewing child details
			this.CurrentOrderChild = null;
		},

		updateChild(NewChild) {
			let childIdx = this.Order.child.reduce( (foundIdx, Child, idx) => {
				if (Child.id == NewChild.id) return idx;
				return foundIdx;
			}, null);

			if (childIdx !== null) {
				this.$set(this.Order.child, childIdx, NewChild);
			}
		},

		parsePickupDate(date) {
			let parsed = String(date).substr(0, 19);

			return parsed;
		},

		pickupDateChanged(date_id) {
			this.pickup_date_id = date_id;
		},

		toggleEditPickupDate() {
			this.editing_pickup_date = ! this.editing_pickup_date;
		},

		cancelEditPickupDate() {
			this.pickup_date_id = this.Order.pickup_date_id;
			this.toggleEditPickupDate();
		},

		isValid() {
			if (this.isDraft) return this.validateDraft();
			else if (this.isPending) return this.validatePending();
			else if (this.isFulfilled || this.isApproved) return this.validateFulfilled();
		},

		validateDraft() {
			if (this.PendingChildren.length == 0) return false;
			if ( ! this.pickup_date_id) return false;

			let incompleteChildren = this.PendingChildren.filter(Child => {
				return ! Child.item;
			});

			return incompleteChildren.length == 0;
		},

		validatePending() {
			if (this.RemovedChildren.length == this.SortedChildren.length) {
				return false;
			}

			if ( ! this.pickup_date_id) {
				return false;
			}

			return true;
		},

		validateFulfilled() {
			return !! this.pickup_date_id;
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
			if (this.isAdmin) {
				return '/admin/order';
			}
			else {
				return '/order';
			}
		},

		orderStatusClasses() {
			let classes = {};

			if (this.isRejected || this.isCancelled) classes['dark-red'] = true;
			if (this.isApproved || this.isFulfilled) classes['dark-green'] = true;
			if (this.isDraft || this.isPending) classes['bootstrap-info-blue'] = true;

			return classes;
		}
	},
};
</script>