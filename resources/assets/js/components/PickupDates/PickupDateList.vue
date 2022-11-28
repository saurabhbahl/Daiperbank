<template>
	<div class="flex-auto flex pa4">
		<calendar-view v-if="currentView == VIEW_CALENDAR"
			:pickupDates="PickupDates"
			:month="currentMonth"
			:year="currentYear"
			@select="showDateDetail"
		></calendar-view>

		<list-view v-else-if="currentView == VIEW_LIST"
			:pickupDates="PickupDates"
			:month="currentMonth"
			:year="currentYear"
		></list-view>

		<pickup-date-detail-pane
			v-if="selected_date"
			:key="selected_date? selected_date : 'empty-pane'"
			:selected-date="ActiveDate()"
			@close="closeDateDetail"
			@remove="removeDate"
			@add="addDate"
			@select="selectDate"
		></pickup-date-detail-pane>
	</div>
</template>

<script>
import moment from 'moment-timezone';
import CalendarView from './CalendarView';
import ListView from './ListView';
import Day from './day';

const VIEW_CALENDAR = 'calendar';
const VIEW_LIST = 'list';

export default {
	components: {
		'calendar-view': CalendarView,
		'list-view': ListView,
	},

	props: {
		initialPickupDates: {
			required: true,
			type: Object,
		},

		initialMonth: {
			required: false,
			type: String,
		},
	},

	data() {
		return {
			PickupDates: this.initialPickupDates,
			listMonth: this.initialMonth,
			VIEW_CALENDAR,
			VIEW_LIST,
			selected_date: null,
		};
	},

	computed: {
		currentView() {
			return this.$store.getters['AdminPickupDate/currentView'];
		},

		currentMonth() {
			if ( ! this.listMonth || this.listMonth.indexOf('-') < 0) {
				return null;
			}

			let year, month;
			[ year, month ] = this.listMonth.split('-');

			return parseInt(month) - 1;
		},

		currentYear() {
			if ( ! this.listMonth || this.listMonth.indexOf('-') < 0) {
				return null;
			}

			let year, month;
			[ year, month ] = this.listMonth.split('-');

			return parseInt(year);
		},

	},

	methods: {
		ActiveDate() {
			if (!this.selected_date) return null;

			const date_str = this.selected_date.format('YYYY-MM-DD');
			return new Day(this.selected_date, this.PickupDates[ date_str ] || null);
		},

		showDateDetail(dateContext) {
			this.selected_date = dateContext; //this.PickupDates[ date_str ] || [];
		},

		closeDateDetail() {
			this.selected_date = null;
		},

		removeDate(date) {
			let date_str = moment(date.pickup_date).format('YYYY-MM-DD');

			this.$delete(this.PickupDates, date_str);

			if (this.isDateSelected(date_str)) {
				this.closeDateDetail();
			}
		},

		addDate(newDate) {
			let date_str = moment.tz(newDate.pickup_date, 'America/New_York').format('YYYY-MM-DD');

			if (undefined == this.PickupDates[ date_str ]) {
				this.$set(this.PickupDates, date_str, [ newDate ]);
			}
			else {
				this.PickupDates[ date_str ].push( newDate );
			}

			this.selectDate(date_str);
		},

		selectDate(date_str) {
			if (this.PickupDates[ date_str ]) {
				this.showDateDetail( moment.tz(date_str, 'America/New_York') );
			}
		},

		isDateSelected(date_str) {
			if (this.selected_date) {
				return date_str == this.selected_date.format('YYYY-MM-DD');
			}

			return false;j
		},
	}
}
</script>