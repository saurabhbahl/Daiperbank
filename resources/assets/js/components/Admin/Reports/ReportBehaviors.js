import Datepicker from '../../General/DatePicker.vue';
import ReportingPeriods from './ReportingPeriods';
import moment from 'moment';

export default {
	components: { Datepicker },

	props: {
		defaultDateRange: {
			type: Object,
			required: false,
			default: () => {
				return {
					start_date: null,
					end_date: null,
				};
			},
		},
	},

	data() {
		return {
			start_date: this.defaultDateRange.start_date,
			end_date: this.defaultDateRange.end_date,
		}
	},

	computed: {
		ReportingPeriods() {
			return ReportingPeriods;
		},

		downloadEnabled() {
			return this.start_date && this.end_date;
		},

		start_date_formatted() {
			if (this.start_date) {
				return this.formatDate(this.start_date);
			}

			return null;
		},

		end_date_formatted() {
			if (this.end_date) {
				return this.formatDate(this.end_date);
			}

			return null;
		},

		CurrentRange() {
			return {
				start_date: this.start_date,
				end_date: this.end_date,
			};
		}
	},

	methods: {
		formatDate(date) {
			if ( ! moment.isMoment(date)) {
				return null;
			}

			return date.format('YYYY-MM-DD');
		},

		selectPeriod(period) {
			this.start_date = period.start_date;
			this.end_date = period.end_date;

			this.$emit('dateRangeSelected', {
				start_date: this.start_date,
				end_date: this.end_date,
			});
		},

		dateRangeChanged(range) {
			this.selectPeriod({
				start_date: range.start,
				end_date: range.end,
			});
		}
	},
}