import moment from 'moment';

export default {
	'less-than-one': {
		'id': 'less-than-one',
		'name': '0 - 12 months',
		'start_date': moment().subtract(1, 'years').add(1, 'days'),
		'end_date': moment(),
	},

	'12-24-mos': {
		'id': '12-24-mos',
		'name': '12 - 24 months',
		'start_date': moment().subtract(24, 'months'),
		'end_date': moment().subtract(12, 'months'),
	},

	'24-36-mos': {
		'id': '24-36-mos',
		'name': '24 - 36 months',
		'start_date': moment().subtract(36, 'months'),
		'end_date': moment().subtract(24, 'months'),
	},

	'36-plus-mos': {
		'id': '36-plus-mos',
		'name': '36+ months',
		'start_date': moment().subtract(10, 'years'),
		'end_date': moment().subtract(36, 'months'),
	},
};