export default {
	'org': {
		'id': 'org',
		'name': 'Organizational Overview',
		'component': require('./OrganizationOverview.vue'),
	},

	'agency': {
		'id': 'agency',
		'name': 'Agency Overview',
		'component': require('./AgencyOverview.vue'),
	},

	'location': {
		'id': 'location',
		'name': 'Location Overview',
		'component': require('./LocationOverview.vue'),
	},

	'child-age': {
		'id': 'child-age',
		'name': 'Age of Children',
		'component': require('./AgeOfChildren.vue'),
	},

	'military': {
		'id': 'military',
		'name': 'Military Overview',
		'component': require('./MilitaryOverview.vue'),
	},

	'donations': {
		'id': 'donations',
		'name': 'Donations',
		'component': require('./Donations.vue'),
	},

	// 'sizes': {
	// 	'id': 'sizes',
	// 	'name': 'Sizes Distributed',
	// 	'component': require('./OrganizationOverview.vue'),
	// },

	'inventory': {
		'id': 'inventory',
		'name': 'Inventory',
		'component': require('./Inventory.vue'),
	},

	'diaper-drives': {
		'id': 'diaper-drives',
		'name': 'Diaper Drives',
		'component': require('./DiaperDrives.vue'),
	},

	'pull-up-usage': {
		'id': 'pull-up-usage',
		'name': 'Pull-Up-Usage',
		'component': require('./PullUpUsage.vue'),
	},

	'diaper-usage': {
		'id': 'diaper-usage',
		'name': 'Diaper Usage',
		'component': require('./DiaperUsage.vue'),
	},

	'purchases': {
		'id': 'purchases',
		'name': 'Purchases',
		'component': require('./Purchases.vue'),
	},
};