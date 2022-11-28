<style lang="less" scoped>
.dom { width: 100/7%; }
</style>

<template>
	<div class="flex-auto flex flex-column">
		<div class="tc fg-no pb flex justify-between">
			<p>
				<a :href="'?month=' + prevMonth">
					<i class="fa fa-chevron-left"></i>
					Previous
				</a>
			</p>

			<p class="f2 b">
				{{ dateContext.format('MMMM YYYY') }}
			</p>

			<p>
				<a :href="'?month=' + nextMonth">
					Next
					<i class="fa fa-chevron-right"></i>
				</a>
			</p>
		</div>

		<div class="flex justify-start fg-no">
			<div v-for="dow in daysOfWeek"
				class="dom tc">
				{{ dow.name }}
			</div>
		</div>

		<div class="flex flex-wrap justify-start fg fs-no br bb">
			<day-of-month
				v-for="(day, idx) in daysInMonth"
				:key="[currentYear, currentMonth, idx].join('-')"
				:day="day"
				@click="selectDate"
			/>
		</div>
	</div>
</template>

<script>
import moment from 'moment-timezone';
import CalendarDayOfMonth from './CalendarDayOfMonth.vue';
import Day from './day';

export default {
	props: ['pickupDates', 'month', 'year'],
	components: { 'day-of-month': CalendarDayOfMonth },
	data() {

		let month = moment().month();

		if ( undefined != this.month) {
			month = this.month;
		}

		return {
			dateContext: moment.tz([
				parseInt(this.year || moment().year()),
				parseInt(month),
				1
			], 'America/New_York'),
			today: moment(),
			daysOfWeek: [
				{ id: 0, abbr: 'Sun', name: 'Sunday' },
				{ id: 1, abbr: 'Mon', name: 'Monday' },
				{ id: 2, abbr: 'Tue', name: 'Tuesday' },
				{ id: 3, abbr: 'Wed', name: 'Wednesday' },
				{ id: 4, abbr: 'Thu', name: 'Thursday' },
				{ id: 5, abbr: 'Fri', name: 'Friday' },
				{ id: 6, abbr: 'Sat', name: 'Saturday' },
			],
		}
	},

	computed: {
		currentYear() {
			return this.dateContext.year();
		},

		currentMonth() {
			return this.dateContext.month();
		},

		firstDom() {
			const firstOfMonth = moment.tz([ this.currentYear, this.currentMonth, 1 ], 'America/New_York');
			return firstOfMonth.day();
		},

		daysInMonth() {
			const daysInMonth = this.dateContext.daysInMonth();
			const firstDom = this.firstDom;
			const eomPadding = this.eomPadding;

			const days = [];

			for (var x = 0; x < firstDom; x++) {
				days.push(new Day());
			}

			for (var x = 0; x < daysInMonth; x++) {
				let curDate = moment.tz([ this.currentYear, this.currentMonth, x + 1 ], 'America/New_York');
				let curDateFormatted = curDate.format('YYYY-MM-DD');
				let scheduledPickups = null;

				if (this.pickupDates[ curDateFormatted ]) {
					scheduledPickups = Object.values(this.pickupDates[ curDateFormatted ]);
				}

				days.push(new Day(curDate, scheduledPickups));
			}

			for (var x = 0; x < eomPadding; x++) {
				days.push(new Day());
			}

			return days;
		},

		eomPadding() {
			const dimWithPadding = this.dateContext.daysInMonth() + this.firstDom;
			const weeksInMonth = Math.ceil(dimWithPadding / 7);
			return (weeksInMonth * 7) - dimWithPadding;
		},

		prevMonth() {
			return moment(this.dateContext).subtract(1, 'months').format('YYYY-MM');
		},

		nextMonth() {
			return moment(this.dateContext).add(1, 'months').format('YYYY-MM');
		}
	},

	methods: {
		getDom(dom) {
			return moment.tz([ this.currentYear, this.currentMonth, dom ], 'America/New_York');
		},

		selectDate(date) {
			this.$emit('select', date.dateContext);
		},
	}
}
</script>
