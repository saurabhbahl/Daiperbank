<template>
	<SelectFilter
		:items="PickupDates"
		value-key="id"
		:search="false"
		:initialValue="selected_pickup_date"
		@selected="onChange">

		<template slot="selected-item" slot-scope="PickupDate">
			<div class="flex justify-start items-center pv3">
				<span class="fa-stack fa-2x fg-no">
					<i class="fa fa-calendar-o fa-stack-2x"></i>
					<span class="fa-stack-1x calendar-text"
						style="margin-top: .25em;">{{ parsePickupDate(PickupDate.pickup_date) | formatDate("DD", "YYYY-MM-DD") }}</span>
				</span>

				<span class="pl3 f2">
					{{ parsePickupDate(PickupDate.pickup_date) | formatDate("MMM D, YYYY @ h:mma", "YYYY-MM-DD") }}
				</span>
			</div>
		</template>

		<template slot="item" slot-scope="PickupDate">
			<div class="flex justify-start items-center pv3">
				<span class="fa-stack fa-2x fg-no">
					<i class="fa fa-calendar-o fa-stack-2x"></i>
					<span class="fa-stack-1x calendar-text"
						style="margin-top: .25em;">{{ parsePickupDate(PickupDate.pickup_date) | formatDate("DD", "YYYY-MM-DD") }}</span>
				</span>

				<span class="pl3 f2 wtl">
					{{ parsePickupDate(PickupDate.pickup_date) | formatDate("MMM D, YYYY @ h:mma", "YYYY-MM-DD") }}
				</span>
			</div>
		</template>

		<template slot="default-selection">
			<div class="flex justify-start items-center pv3">
				<span class="fa-stack fa-2x fg-no">
					<i class="fa fa-calendar-o fa-stack-2x"></i>
					<span class="fa-stack-1x calendar-text"
						style="margin-top: .25em;"></span>
				</span>

				<span class="pl3 f2 wtl">
					Choose a Date
				</span>
			</div>
		</template>

		<template slot="empty-results">
			No pickup dates are currently available.
		</template>
	</SelectFilter>
</template>

<script>
import SelectFilter from '../General/SelectFilter.vue';

export default {
	components: { SelectFilter },

	props: {
		pickupDates: {
			required: true,
			type: [Array, Object],
		},

		initialDate: {
			required: false,
			type: [Object, Number, String],
		}
	},

	data() {
		return {
			PickupDates: this.pickupDates,
			pickup_date: this.initialDate,
		};
	},

	computed: {
		selected_pickup_date() {
			if (undefined != this.pickup_date) {
				if (typeof this.pickup_date == 'string') {
					return parseInt(this.pickup_date);
				}
				else if (typeof this.pickup_date == 'object') {
					return parseInt(this.pickup_date.id);
				}
				else if (typeof this.pickup_date == 'number') {
					return this.pickup_date;
				}
			}

			return null;
		}
	},

	methods: {
		onChange(id) {
			this.pickup_date = id;
			this.$emit('change', id);
		},

		parsePickupDate(date) {
			let parsed = String(date).substr(0, 19);

			return parsed;
		},
	}
}
</script>