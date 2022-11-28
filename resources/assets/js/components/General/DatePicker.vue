<template>
	<input type="text" :name="name" ref="datepicker">
</template>

<script>
export default {
	props: {
		name: {
			required: true,
			type: String,
		},

		ReportingPeriods: {
			require: false,
			default: () => {},
		},

		DefaultRange: {
			require: false,
			default: () => {
				return {
					start_date: null,
					end_date: null,
				};
			}
		}
	},

	// 	default: {
	// 		required: false,
	// 		default: null,
	// 	},
	// },
	computed: {
		input() {
			return this.$refs['datepicker'];
		},

		dateRangePicker() {
			return $(this.input).daterangepicker;
		}
	},

	methods: {
		getPresetRanges() {
			return Object.values(this.ReportingPeriods).map(function(Range) {
				return {
					text: Range.name,
					dateStart: function() {
						return Range.start_date;
					},
					dateEnd: function() {
						return Range.end_date;
					}
				};
			});
		},

		onChange() {
			let data = $(this.input).daterangepicker('getRange');
			for (let key in data) {
				data[key] = moment(data[key]);
			}
			this.$emit('change', data); // this.dateRangePicker('getRange'));
		}
	},

	mounted() {
		window.$(this.input).daterangepicker({
			presetRanges: this.getPresetRanges(),
			change: this.onChange,
		});

		if (this.DefaultRange.start_date && this.DefaultRange.end_date) {
			window.$(this.input).daterangepicker("setRange", {
				start: this.DefaultRange.start_date.toDate(),
				end: this.DefaultRange.end_date.toDate(),
			});
		}
	},

	beforeDestroy() {
		window.$(this.input).daterangepicker('destroy');
	},
}
</script>