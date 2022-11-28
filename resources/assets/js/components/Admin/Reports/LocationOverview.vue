<template>
	<div>
		<h2 class="ma0 pa0 pb3 mb3 f2 bb b--black-20">Location Overview</h2>
		<form method="post">
	<div class="pb3">
							<label for="all-zips">All Zips</label>
							<p>
								<label for="all-zips"
									class="ma0">
									<input type="checkbox"
										class="fwn"
										id="all-zips"
										name="all-zips"
										v-model="isAllZips"	
										@change="isAllZipSelected"
										value="1"
										>
										
								</label>
							</p>
						</div>
			<div class="pb3">
				<label for="zip-codes">Zip Codes</label>
				<input type="text" class="form-control" name="zip-codes" id="zip-codes" v-model="zips">
				<p class="small"><strong>To enter multiple zip codes:</strong> Separate each zip code with a comma (Eg, <tt>17404, 17405, 17406</tt>)</p>
			</div>


			<div class="pb3">
				<label for="date-range" class="db mb1">Date Range</label>
				<datepicker
					name="date-range"
					:ReportingPeriods="ReportingPeriods"
					:DefaultRange="CurrentRange"
					@change="dateRangeChanged"
				></datepicker>
			</div>

			<input type="hidden" name="report" value="location-report">
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

export default {
	mixins: [ ReportBehaviors ],
	data() {
		return {
			zips: null,
			isAllZips:false
		};
	},

	computed: {
		downloadEnabled() {
			return this.start_date && this.end_date && (this.zips|| this.isAllZips);
		}


	}

}
</script>