<template>
	<div v-if="paneActive" :key="activeDate"
		class="pxa pint pinr pinb w-50 flex flex-column justify-stretch">

		<transition>
			<div class="flex flex-column justify-stretch flex-auto bg-white pa4 shadow-2 pxr"
					v-if="!reschedule">
				<a href="#"
					@click="close"
					class="pxa pint pinr a-plain pa4">
					<i class="fa fa-lg muted fa-times-circle"></i>
				</a>

				<h1 class="f2 tc ma0 pa0 b mb4">{{ PickupDate | formatDate("dddd, MMMM Do, YYYY") }}</h1>

				<div v-if="items.length"
					class="oy-hidden flex flex-column justify-stretch">

					<p class="fg-no fs-no tc f2">
						<span class="b">Pickup Scheduled for</span>
						<br>
						{{ PickupDate | formatDate("h:mm A") }}
					</p>

					<div class="fg-no fs-no pv3 tc">
						<button v-if="ordersPending > 0 || ordersApproved > 0"
							class="btn btn-danger"
							:class="{'disabled': processing}"
							@click="showRescheduleForm">
							Re-schedule Pickup
						</button>

						<button v-else
							class="btn btn-danger"
							:class="{'disabled': processing}"
							@click="cancelPickup">
							Cancel Pickup
						</button>
					</div>

					<div class="fg-no fs-no flex justify-around w-60 mlauto mrauto mv3">
						<p class="tc pa3">
							<span class="f2">{{ ordersPending }}</span>
							<br>
							<span class="f2 wtl">Orders Pending Approval</span>
						</p>

						<p class="tc pa3">
							<span class="f2">{{ ordersApproved }}</span>
							<br>
							<span class="f2 wtl">Orders Scheduled</span>
						</p>
					</div>

					<div class="oy-auto flex-auto pt3" v-if="ListableOrders.length">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="w-20">Order #</th>
									<th class="w-40">Agency</th>
									<th class="w-20">Status</th>
									<th class="w-20">Created on</th>
								</tr>
							</thead>

							<tbody>
								<tr v-for="Order in ListableOrders" :key="Order.id"
									class="clickable hov:bg-washed-blue-important"
									@click="viewOrder(Order)">
									<th scope="row">#{{ Order.full_id }}</th>
									<td>{{ Order.agency.name }}</td>
									<td>{{ Order.readable_status }}</td>
									<td>{{ Order.created_at | formatDate("MM/DD/YYYY") }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div v-else class="tc f2">
					<label class="b" for="pickup_time">
						Schedule Pickup for
					</label>

					<p v-if="hasError('scheduled_time')" class="validation-error f3 tc">
						{{ error('scheduled_time') }}
					</p>

					<input type="time" class="form-control input-lg tc" id="pickup_time" v-model="scheduled_time">

					<button class="btn btn-primary mt4"
						:class="{'disabled': processing}"
						@click="schedulePickup">
						Schedule Pickup
					</button>
				</div>
			</div>
			<div v-if="reschedule"
				 class="flex-auto bg-white pa4 shadow-2 pxr">

				<h1 class="f2 tc ma0 pa0 b mb4">{{ PickupDate | formatDate("dddd, MMMM Do, YYYY") }}</h1>

				<p class="tc f2">
					<span class="b">Pickup Scheduled for</span>
					<br>
						{{ PickupDate | formatDate("h:mm A") }}
				</p>

				<p class="pb">
					<label for="rescheduled_datetime b">Reschedule for</label>
					<br>

					<p v-if="hasError('reschedule_datetime')" class="validation-error tc f3">
						{{ error('reschedule_datetime') }}
					</p>

					<input type="datetime-local" id="reschedule_datetime" class="tc form-control input-lg"
							v-model="rescheduled_datetime">
				</p>

				<p class="pb">
					<label for="rescheduled_reason">Add a reason for rescheduling</label>
					<textarea id="rescheduled_reason" v-model="rescheduled_reason" class="form-control" rows="3"></textarea>
					<p class="f4 muted">
						The reason for rescheduling will be sent along with the rescheduled order notification to all agencies
						which have orders scheduled for pickup on this date.
					</p>
				</p>

				<div class="tc mv4">
					<button class="btn btn-primary"
						:class="{'disabled': processing}"
						@click="reschedulePickup">Re-schedule this Pick-up</button>
					<button class="btn btn-default"
						:class="{'disabled': processing}"
						@click="cancelReschedule">Cancel</button>
					<p class="f4 muted tl pt">
						By rescheduling this pickup, all orders that are pending, or approved, for this
						date will be re-scheduled to the new pickup date and future orders will be not
						be allowed to be scheduled for pickup on this date.
					</p>
				</div>

			</div>
		</transition>

	</div>
</template>

<script>
import moment from 'moment';

export default {
	props: {
		selectedDate: {
			required: true,
			type: Object,
		},
	},

	data() {
		return {
			activeDate: this.selectedDate,
			reschedule: false,
			rescheduled_datetime: null,
			rescheduled_reason: null,
			scheduled_time: null,
			processing: false,
		};
	},

	computed: {
		paneActive() {
			return this.activeDate !== null;
		},

		PickupDate() {
			return this.hasItems()? this.item.pickup_date : this.activeDate.dateContext;
		},

		items() {
			return this.hasItems()? Object.values(this.activeDate.items) : [];
		},

		item() {
			return this.hasItems()? this.items[0] : null;
		},

		ordersPending() {
			return this.hasItems()? this.item.orders_pending : 0;
		},

		ordersApproved() {
			return this.hasItems()? this.item.orders_approved : 0;
		},

		ListableOrders() {
			if ( ! this.item) {
				return [];
			}

			return Object.values(this.item.order).filter( Order => {
				return Order.order_status == 'draft'
						|| Order.order_status == 'pending_approval'
						|| Order.order_status == 'pending_pickup'
						|| Order.order_status == 'fulfilled';
			});
		}
	},

	methods: {
		hasItems() {
			return !! Object.values(this.activeDate.items).length;
		},

		close() {
			if (this.processing) return;
			this.$emit('close');
		},

		showRescheduleForm() {
			this.reschedule = true;
		},

		cancelReschedule() {
			this.reschedule = false;
		},

		schedulePickup() {
			this.clearErrors();

			if (null == this.scheduled_time) {
				this.displayErrors({
					'scheduled_time': ["This field is required."],
				});

				return false;
			}

			let datetime = this.PickupDate;
			let time = {
				hour: this.scheduled_time.split(":")[0],
				minute: this.scheduled_time.split(":")[1],
			}

			datetime.hour(time.hour).minute(time.minute);

			if (this.processing) return;
			this.processing = true;

			axios.post('/api/pickup-dates', {
				pickup_date: datetime.format("YYYY-MM-DD HH:mm:ss"),
			})
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "New pickup date scheduled successfully.",
					});
					this.$emit('add', response.data.data.PickupDate);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "An error occurred while scheduling this pickup date. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.catch( err => {
				let response = err.response;
				if (response.status == 422 && ! undefined == response.data.data) {
					this.displayErrors(response.data.data.errors || null);
				}

				this.$toast.error({
					title: "Error",
					message: "An error occurred while scheduling this pickup date. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		reschedulePickup() {

			this.clearErrors();

			if ( ! this.rescheduled_datetime) {
				this.displayErrors({
					'rescheduled_datetime': ['This field is required.'],
				});
				return;
			}

			if (this.processing) return;
			this.processing = true;

			let pickup_date = this.item;
			axios.post(`/api/pickup-dates/${pickup_date.id}/reschedule`, {
				reschedule_for: this.rescheduled_datetime,
				reschedule_reason: this.rescheduled_reason,
			}).then( (response) => {
				let data = response.data;
				if (data.success) { //successfully rescheduled the pickup
					this.$toast.success({
						title: "Success",
						message: "All unfulfilled orders, scheduled for this date, have been rescheduled successfully.",
					});

					this.$emit('remove', this.item);
					this.$emit('add', data.data.PickupDate);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not reschedule orders. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.catch( (error) => {
				let response = error.response;
				if (response.status == 422 && ! undefined == response.data.data) {
					this.displayErrors(response.data.data.errors || null);
				}

				this.$toast.error({
					title: "Error",
					message: "Could not reschedule orders. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		cancelPickup() {
			if (this.processing) return;
			this.processing = true;

			let pickup_date = this.item;
			axios.delete(`/api/pickup-dates/${pickup_date.id}`)
			.then( (response) => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Cancelled pickup date successfully",
					});
					this.$emit('remove', this.item);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not cancel pickup date. " + (response.data.message || ""),
				});
			})
			.catch( (error) => {
				let response = error.response;
				if (response.status == 422 && undefined != response.data.data) {
					this.displayErrors(response.data.data.errors || null);
				}

				this.$toast.error({
					title: "Error",
					message: "Could not cancel pickup date. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});;
		},

		closeOnEscape(evt) {
			if (evt.keyCode == 27) {
				if (this.paneActive) {
					this.close();
				}
			}
		},

		viewOrder(Order) {
			window.top.location = `/admin/order/${Order.id}`;
		},
	},

	mounted() {
		this.processing = false;
		window.addEventListener('keydown', this.closeOnEscape);
	},

	beforeDestroy() {
		this.processing = false;
		window.removeEventListener('keydown', this.closeOnEscape);
	}
}
</script>