<template>
	<div>
		<h2 class="ma0 pa0 pb3 mb3 f2 bb b--black-20">Diaper Usage Report</h2>
		<form method="post">
			<div class="pxr pb3">
				<label for="agency-id">Agency</label>
				<p v-if=" ! Agencies">
					<i class="fa fa-spinner fa-spin"></i> Loading Agencies...
				</p>
				<AgencySelector v-if="Agencies"
					class="pxr zx999"
					:agents="Agencies"
					@change="agencySelected"
				></AgencySelector>
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

			<input type="hidden" name="report" value="diaper-usage-report">
			<input type="text" name="agency-id" v-model="agency_id" class="dn">
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
import AgencySelector from '../../General/AgencySelector.vue';

export default {
	mixins: [ ReportBehaviors ],
	components: { AgencySelector },
	data() {
		return {
			Agencies: null,
			agency_id: null,
			retries: 0,
		}
	},

	mounted() {
		this.fetchAgencies();
	},

	computed: {
		downloadEnabled() {
			return this.start_date && this.end_date && this.agency_id;
		}
	},

	methods: {
		fetchAgencies() {
			axios.get('/api/agency')
			.then( (response) => {
				if (response.data.success) {
					this.loadAgencies(response.data.data);
				} else {
					if (this.retries < 3) {
						this.$toast.error({
							title: "Error",
							message: "Could not load Agency Partners. Trying again in.",
						});

						this.retries++;
						return this.fetchAgencies();
					} else {
						this.$toast.error({
							title: "Error",
							message: "Could not load Agency Partners at this time. If this continues, please contact support.",
						});
					}
				}
			})
			.catch( err => {
				let response = err.response;
				this.$toast.error({
					title: "Error",
					message: "An unexpected error occurred while attempting to access Agency Partners. If this continues, please contact support.",
				});
			});
		},

		loadAgencies(Agencies) {
			this.Agencies = Agencies;
		},

		agencySelected(id) {
			this.agency_id = id;
		},
	}
}
</script>