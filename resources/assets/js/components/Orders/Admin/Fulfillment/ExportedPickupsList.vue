<template>

	<div class="pxr flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
			<div class="fg-no bg-washed-blue">
				<ul class="nav nav-tabs ph3 pt3">
					<li class="clickable:important">
						<a href="/admin/fulfillment" class="clickable:important">
							Export Pending Orders
						</a>
					</li>

					<li class="dropdown bg-white active clickable:important">
						<a href="?" class="clickable:important">
							Upcoming Pickups
						</a>
					</li>
				</ul>
			</div>

			<div class="fg fs oy-auto">
				<a v-for="PickupDate in PickupDates"
					v-if=" ! PickupDate.reconciled_at"
					class="db bb b--black-20 a:hov-nou a-plain hov:bg-washed-blue"
					:class="{'bl blw3 bl--blue': isActive(PickupDate) }"
					href="#"
					@click.prevent="selectPickupDate(PickupDate)">
						<div class="pa pr5 flex justify-between items-center content-center">
							<div class="fg-no fs-no w-25">
								<p>
									<span class="f4 muted">{{ PickupDate.pickup_date | formatDate("dddd") }}</span>
									<br>
									<span class="f2 wtl lh5">{{ PickupDate.pickup_date | formatDate("MMM D, YYYY") }}</span>
								</p>
							</div>

							<div class="fg fs-no w-75 flex justify-between items-center">
								<div>
									<p>{{ PickupDate.order_count }}</p>
									<p class="muted wtl">Orders</p>
								</div>

								<div>
									<p>{{ PickupDate.child_count }}</p>
									<p class="muted wtl">Children</p>
								</div>

								<div>
									<p>{{ PickupDate.diaper_count }}</p>
									<p class="muted wtl">Diapers</p>
								</div>
								<div>
									<p>{{ PickupDate.pullup_count }}</p>
									<p class="muted wtl">Pull-ups</p>
								</div>

								<div>
									<i class="fa fa-chevron-right"></i>
								</div>
							</div>
						</div>
				</a>
			</div>
		</div>

		<div class="col-xs-4 pa0 bg-white flex flex-column justify-between">
			<div class="pa o-hidden oy-auto">

			</div>

			<div class="bg-washed-blue pa bt b--black-20 fs-no fg-no">

			</div>
		</div>

		<FulfillmentDetail
			v-if="SelectedPickupDate"
			class="pxa pinr pint pinb w-33 bg-white shadow-2"
			:initialPickupDate="SelectedPickupDate"
			:key="'pickup-detail-' + SelectedPickupDate.id"
			@close="closeDetails"
			@update="updatePickupDate"
		></FulfillmentDetail>
	</div>

</template>

<script>
import FulfillmentDetail from './Detail.vue';
import orderCounts from './orderCounts.js';

export default {
	components: { FulfillmentDetail },

	mixins: [ orderCounts ],

	props: {
		initialPickupDates: {
			required: true,
			type: [Object, Array],
		},
	},

	data() {
		return {
			PickupDates: Object.values(this.initialPickupDates),
			SelectedPickupDate: null,
		};
	},

	methods: {
		selectPickupDate(PickupDate) {
			this.SelectedPickupDate = PickupDate;
		},

		isActive(PickupDate) {
			return this.SelectedPickupDate && this.SelectedPickupDate.id == PickupDate.id;
		},

		closeDetails() {
			this.SelectedPickupDate = null;
		},

		updatePickupDate(PickupDate) {
			let currentIndex = this.PickupDates.reduce( (idx, SearchDate, current_idx) => {
				if (idx) return idx;
				if (SearchDate.id == PickupDate.id) return current_idx;
				return null;
			}, null);

			if (currentIndex) {
				this.$set(this.PickupDates, currentIndex, PickupDate);
			}
		}
	}
}
</script>