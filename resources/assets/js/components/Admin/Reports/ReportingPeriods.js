import moment from 'moment';

export default {
	'mtd': {
		'id': 'mtd',
		'name': 'Month to Date',
		'start_date': moment().date(1),
		'end_date': moment(),
	},

	'last-month': {
		'id': 'last-month',
		'name': 'Last Month',
		'start_date': moment().subtract(1, 'months').date(1),
		'end_date': moment().date(1).subtract(1, 'days'),
	},

	'ytd': {
		'id': 'ytd',
		'name': 'Year to Date',
		'start_date': moment().dayOfYear(1),
		'end_date': moment(),
	},

	'last-year': {
		'id': 'last-year',
		'name': 'Last Year',
		'start_date': moment().subtract(1, 'years').dayOfYear(1),
		'end_date': moment().dayOfYear(1).subtract(1, 'days'),
	},

	'current-fiscal': {
		'id': 'current-fiscal',
		'name': 'Current Fiscal Year',
		'start_date': moment().format('MM') > 6? moment().set({ month: 6, date: 1 }) : moment().subtract(1, 'years').set({ month: 6, date: 1 }),
		'end_date': moment().format('MM') > 0? moment().set({ month: 5, date: 30 }) : moment().add(1, 'years').set({ month: 5, date: 30 }),
	},

	'prev-fiscal': {
		'id': 'prev-fiscal',
		'name': 'Previous Fiscal Year',
		'start_date': moment().subtract(moment().format('MM') > 0? 2 : 1, 'years').set({ month: 6, date: 1 }),
		'end_date': moment().format('MM') > 0? moment().subtract(1, 'years').set({ month: 5, date: 30 }) : moment().subtract(1, 'years').set({ month: 5, date: 30 }),
	},
};