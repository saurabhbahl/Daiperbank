<template>
	<div>
		<h2 class="ma0 pa0 pb3 mb3 f2 bb b--black-20">Age of Children</h2>
		<form method="post">

			<div class="pb3">
				<label for="date-range" class="db mb1">Birthdate Range</label>
				<datepicker
					name="date-range"
					:ReportingPeriods="ReportingPeriods"
					:DefaultRange="CurrentRange"
					@change="dateRangeChanged"
				></datepicker>
			</div>

			<input type="hidden" name="report" value="child-report">
			<input type="text" name="start-date" v-model="start_date_formatted" class="dn">
			<input type="text" name="end-date" v-model="end_date_formatted" class="dn">

			<button
				type="submit"
				class="mt btn btn-primary"
				:class="{ 'disabled': ! this.downloadEnabled }"
			>Download Report</button>
		</form>
	</div>
</template>

<script>
import ReportBehaviors from './ReportBehaviors';
import ReportingPeriods from './ChildAgeReportingPeriods';

export default {
	mixins: [ ReportBehaviors ],
	data() {
		return {
			start_date: null,
			end_date: null,
		};
	},

	computed: {
		ReportingPeriods() {
			return ReportingPeriods;
		},
	}
}
</script>