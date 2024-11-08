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
						<p class="b f2 pr5" :class="getOrderStatusClasses()">
							{{ Order.readable_status }}
						</p>
					</div>

					<div class="mlauto self-center">
						<button class="btn btn-default" @click="addChildClicked"
							:class="{ 'disabled': processing }">Add Child</button>&nbsp;
							<button class="btn btn-default" @click="addMenstruatorChildClicked"
							:class="{ 'disabled': processing }">Add Menstruator</button>
					</div>
				
				</div>

				<div class="flex-auto oy-scroll">
					<ChildItem v-for="Child in OrderChildren"
						:key="'order_child_' + Child.id"
						class="flex justify-between items-start bb b--black-20 pa clickable a:hov-nou a-plain hov:bg-washed-blue"
						:class="getChildItemClasses(Child)"
						:child="Child"

						@click="selectChild(Child)"
					></ChildItem>

					<div v-if=" ! Order.child.length">
						<p class="tc f2 wtl pa4">
							There are no people on this order yet.
							<br>
							<br>
							<button class="btn btn-primary"
								:class="{ 'disabled': processing }"
								@click="addChildClicked">
								Add a Child
							</button>
							<button class="btn btn-primary"
								:class="{ 'disabled': processing }"
								@click="addMenstruatorChildClicked">
								Add a Menstruator
							</button>
						</p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-4 pa0 bg-white flex flex-column justify-between">
			<div class="pa o-hidden oy-auto">
				<p class="f2 b">
					Pickup date
					<span class="required">*</span>
				</p>

				<PickupDateSelector
					:pickup-dates="PickupDates"
					:initial-date="Order.pickup_date_id"
					@change="onPickupDateChange"
				></PickupDateSelector>

				<p class="f2 b mv3">Order Summary</p>
				<OrderSummary
					:order="Order"
				></OrderSummary>
			</div>

			<div class="bg-washed-blue pa bt b--black-20 fs-no fg-no">
				<form action="" method="post" ref="discard-form">
					<input type="hidden" name="_token" v-model="csrfToken">
					<input type="hidden" name="action" value="discard">

					<button
						name="action"
						value="discard"
						ref="discard-order-btn"
						class="btn btn-block btn-danger btn-alt mb4"
						:class="{'disabled': processing }"
						@click.stop.prevent="promptDiscardOrder">
						<i class="fa fa-lg fa-trash-o"></i>
						Discard Order
					</button>
				</form>

				<form action="" method="post" ref="save-form">
					<input type="hidden" name="_token" v-model="csrfToken">
					<input type="hidden" name="pickup_date_id" v-model="Order.pickup_date_id">
					<button
						type="submit"
						name="action"
						value="submit"
						ref="submit-order-btn"
						class="btn btn-block btn-success"
						:class="{'disabled': (! isValid) || processing}"
						@click="processing = true">
						<i class="fa fa-lg fa-check"></i>
						Submit Order
					</button>
				</form>
			</div>
		</div>

		<ChildDetail
			v-if="(CurrentOrderChild && CurrentOrderChild.is_menstruator==0)"
			:key="'child-detail-' + CurrentOrderChild.id"

			class="pxa pinr pint pinb w-33 bg-white shadow-2"

			:initial-child="CurrentOrderChild"
			:is-editable="true"
			:initial-editing="true"
			:product-categories="ProductCategories"
			:all-children="AllChildren"
			:processing="processing"

			@close="closeChildDetail"
			@remove="removeChild"
			@save="saveChild"
			 
		></ChildDetail>
		<MenstruatorChildDetail
			v-if="(CurrentOrderChild && CurrentOrderChild.is_menstruator==1)"
			:key="'child-detail-' + CurrentOrderChild.id"

			class="pxa pinr pint pinb w-33 bg-white shadow-2"

			:initial-child="CurrentOrderChild"
			:is-editable="true"
			:initial-editing="true"
			:product-categories="ProductCategories"
			:processing="processing"

			@close="closeChildDetail"
			@remove="removeChild"
			@save="saveChild"
		></MenstruatorChildDetail>
	</div>
</template>

<script>
import ChildDetail from './AgentChildDetail.vue';
import MenstruatorChildDetail from './AgentMenstruatorChildDetail.vue';
import ChildItem from './AgentChildItem.vue';
import OrderSummary from './OrderSummary.vue';
import PickupDateSelector from './PickupDateSelector.vue';
import SelectChildModal from '../Modals/SelectChildModal.vue';
import SelectMenstruatorChildModal from '../Modals/SelectMenstruatorChildModal.vue';
import DiscardOrderConfirmationModal from '../Modals/DiscardOrderConfirmation.vue';
import moment from 'moment';

export default {

	components: { ChildDetail, ChildItem, OrderSummary, PickupDateSelector,MenstruatorChildDetail },

	props: {
		initialOrder: {
			required: true,
			type: Object,
		},

		allChildren: {
			required: true,
			type: Array,
		},

		availableProductCategories: {
			required: true,
			type: Array,
			validator: (prop) => {
				return prop.every( (category) => {
					return !! category.product;
				});
			},
		},

		availablePickupDates: {
			required: true,
			type: Array,
		}
	},

	data() {
		return {
			Order: this.initialOrder,
			AllChildren: this.allChildren,
			PickupDates: this.availablePickupDates,
			ProductCategories: this.availableProductCategories,
			CurrentOrderChild: null,
			isValid: false,
			processing: false,
		};
	},

	computed: {
		csrfToken() {
			return window.csrf;
		},

		AvailableChildren() {
			return this.AllChildren.filter( Child => {
				return this.OrderChildren.filter( OrderChild => {
					return OrderChild.child_id == Child.id;
				}).length == 0;
			});
		},

		OrderChildren() {
			if ( ! this.Order.child) return [];

			this.Order.child.sort((a, b) => {
				return a.name < b.name? -1 : 1;
			});

			return this.Order.child;
		},
	},

	methods: {
		getChildItemClasses(Child) {
			if (this.CurrentOrderChild && Child.id == this.CurrentOrderChild.id) {
				return {
					'bl blw3 bl--blue': true,
				};
			}

			return {};
		},

		getOrderStatusClasses() {
			return {};
		},

		updateAvailableChildren(Children) {
			this.AllChildren = Children;
		},

		selectChild(Child) {
			if (! (Child instanceof Object)) {
				Child = this.getChildById(Child);
			}

			this.CurrentOrderChild = Child;
		},

		closeChildDetail() {
			this.deselectChild();
		},

		deselectChild() {
			this.CurrentOrderChild = null;
		},

		getChildById(child_id) {
			return this.OrderChildren.filter( Child => Child.child_id == child_id)[0] || null;
		},

		addChildClicked() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: SelectChildModal,
				props: {
					allChildren: this.AvailableChildren,
				}
			});

			this.$root.$once('child/select', this.addChild);
			this.$root.$once('child/create', this.createChild);
			this.$root.$once('child/select/cancel', () => {
				this.$root.$off('child/select', this.addChild);
				this.$root.$off('child/create', this.createChild);
			});
		},
		addMenstruatorChildClicked() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: SelectMenstruatorChildModal,
				props: {
					allChildren: this.AvailableChildren,
				}
			});

			this.$root.$once('child/select', this.addMenstruatorChild);
			this.$root.$once('child/create', this.createChild);
			this.$root.$once('child/select/cancel', () => {
				this.$root.$off('child/select', this.addMenstruatorChild);
				this.$root.$off('child/create', this.createChild);
			});
		},

		addChild(Child) {
			if (this.processing) return;
			this.processing = true;

			axios.post(`/api/orders/${this.Order.id}/child`, { child_id: Child.id })
			.then(response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child added to order",
					});
					this.updateOrder(response.data.data.Order);
					this.selectChild(response.data.data.Child.id);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not add child to order. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.catch( err => {
				let response = err.response;
				if (err.status == 422) {
					this.$toast.error({
						title: "Error",
						message: "Could not add child to order."
					});
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not add child to order. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},
		addMenstruatorChild(Child) {
			if (this.processing) return;
			this.processing = true;

			axios.post(`/api/orders/${this.Order.id}/child`, { child_id: Child.id })
			.then(response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child added to order",
					});
					this.updateOrder(response.data.data.Order);
					this.selectChild(response.data.data.Child.id);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not add child to order. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.catch( err => {
				let response = err.response;
				if (err.status == 422) {
					this.$toast.error({
						title: "Error",
						message: "Could not add child to order."
					});
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not add child to order. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},
		saveChild(Child, data) {
			if (this.processing) return;
			this.clearErrors(); 
			this.processing = true;

			axios.post(`/api/orders/${this.Order.id}/child/${Child.id}`, data)
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child order info updated successfully.",
					});
					this.updateChild(response.data.data.Child);
					this.closeChildDetail();
					return
				}

				this.$toast.error({
					title: "Error",
					message: "Could not update child order info. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.catch( error => {
				let response = error.response;
				if (response.status == 422) {
					this.$toast.error({
						title: "Error",
						message: "Could not update child order info. " + (response.data.message || "An unexpected error occurred"),
					});
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not update child order info. " + (response.data.message || "An unexpected error occurred.")
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		updateChild(NewChild) {
			let childIdx = this.OrderChildren.reduce( (foundIdx, Child, idx) => {
				if (Child.id == NewChild.id) return idx;
				return foundIdx;
			}, null);

			if (childIdx !== null) {
				this.$set(this.Order.child, childIdx, NewChild);
			}

			this.validate();
		},

		updateOrder(Order) {
			this.Order = Order;

			this.validate();
		},

		removeChild(Child) {
			if (this.processing) return;
			this.processing = true;

			axios.delete(`/api/orders/${this.Order.id}/child/${Child.id}?force=true`)
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child removed from order successfully",
					});
					this.onChildRemoved(Child);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not remove child from this order. " + (response.data.message || ""),
				});
			})
			.catch( error => {
				this.$toast.error({
					title: "Error",
					message: "Could not remove child from this order. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		onChildRemoved(Child) {
			let childIdx = this.Order.child.reduce( (foundIdx, child, currentIdx) => {
				if (foundIdx) return foundIdx;

				if (child.id == Child.id) return currentIdx;

				return null;
			}, null);

			if (childIdx !== null) {
				this.Order.child.splice(childIdx, 1);
				this.closeChildDetail();
			}

			this.validate();
		},

		onChildSaved(Child) {
			this.updateChild(Child);
		},

		onPickupDateChange(pickup_date_id) {
			this.Order.pickup_date_id = pickup_date_id;

			this.validate();
		},

		promptDiscardOrder() {
			if (this.processing) return;
			this.processing = true;

			this.$store.commit({
				type: 'Modal/show',
				modalComponent: DiscardOrderConfirmationModal,
				props: {
					Order: this.Order,
					isAdmin: false,
				},
			});


			this.$root.$once('order/discard', this.discardOrder);
			this.$root.$once('order/discard/cancel', () => {
				this.$root.$off('order/discard', this.discardOrder);
				this.processing = false;
			});
		},

		discardOrder(Order) {
			if (Order.id != this.Order.id) {
				this.processing = false;
				return;
			}

			this.$nextTick(() => {
				this.$refs['discard-form'].submit();
			});
		},


		validate() {
			let valid = true;
			if ( ! this.Order.pickup_date_id) valid = false;
			else {
				let now = moment();
				let pickupDateIds = this.availablePickupDates.filter( PickupDate => {
					return moment(PickupDate.pickup_date).isAfter(now);
				}).map(PickupDate => PickupDate.id);

				if (pickupDateIds.indexOf( this.Order.pickup_date_id ) == -1 ) valid = false;
			}
			if (valid) {
				valid = valid && this.Order.child.reduce( (childrenValid, Child) => {
					return childrenValid && this.validateChild(Child);
				}, true);
			}

			this.isValid = valid;
			return this.isValid;
		},

		validateChild(Child) {
			// each child must have an item
			if (undefined == Child.item) return false;

			// each child must have a quantity > 0 for the item being ordered..
			if ( ! Child.item.quantity || Child.item.quantity == 0) return false;

			// the pullup category
			if (Child.item.product.product_category_id == 2) {
				// if the child is in pullups, they have to specify a weight
				if ( ! Child.weight || Child.weight == 0) return false;
			}

			return true;
		},
	},

	mounted() {
		this.validate();
	}
}
</script>