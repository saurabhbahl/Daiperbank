<template>
	<div class="flex-auto flex justify-start content-stretch pt3">
		<div class="col-xs-4">
			<div class="bg-white pb3 br3 oh">
				<h2 class="ma3 mt0 pv3 f2 bb b--black-20">Reports</h2>

				<div v-for="report in reports" :key="report.id"
					class="pa3 hov:bg-washed-blue clickable"
					:class="{ 'b bg-washed-blue': reportSelected(report) }"
					@click.prevent="selectReport(report.id)">
					<a :href="'#' + report.id" >
						{{ report.name }}
					</a>
				</div>
			</div>
		</div>

		<div class="col-xs-8">
			<component v-if="SelectedReport"
				class="bg-white pa3 br3 oh"
				:is="ReportComponent"
				@dateRangeSelected="updateDateRange"
				:defaultDateRange="currentDateRange"
			></component>
			<div v-else>
				<p class="i">Waiting for resources to load...</p>
			</div>
		</div>
	</div>
</template>

<script>
import ReportTypes from './Reports/ReportTypes';

function getReportById(report_id) {
	return Object.values(ReportTypes).filter(function(Report) {
		return Report.id == report_id;
	})[0] || null;
}

export default {
	data() {
		return {
			current_report: null,
			currentDateRange: {
				start_date: null,
				end_date: null,
			},
		};
	},

	computed: {
		reports() {
			return ReportTypes;
		},

		SelectedReport() {
			if (this.current_report) {
				return this.current_report;
			}
			return null;
		},

		ReportComponent() {
			let Report = this.SelectedReport;

			if ( ! Report) {
				return null;
			}

			return Report.component;
		}
	},

	methods: {
		reportSelected(report) {
			if (this.SelectedReport) {
				if (this.SelectedReport.id == report.id) {
					return true;
				}
			}

			return false;
		},

		selectReport(report_id) {
			let report = getReportById(report_id);

			if (report) {
				this.current_report = report;
			}
		},

		updateDateRange(newRange) {
			this.currentDateRange = newRange;
		}
	},
}
</script>