
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
import CxltToast from 'cxlt-vue2-toastr';
import moment from 'moment-timezone';
import Raven from 'raven-js';
import RavenVue from 'raven-js/plugins/vue';
import validationPlugin from './plugins/validationErrors.js';

window.Vue = require('vue');

// install sentry
// Raven
//     .config('https://518b43c402ee4f549ccac1f5fe06f2f4@sentry.io/226694')
//     .addPlugin(RavenVue, window.Vue)
//     .install();

// install toast library
Vue.use(CxltToast, {
	position: 'top right',
	showDuration: 500,
	hideDuration: 500,
	timeOut: 3000,
	closeButton: true,
	progressBar: true,
});

Vue.use(validationPlugin);

// install vuex
import Store from './store';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.filter('formatDate', function(value, format, fromFormat) {
	if (moment.isMoment(value)) {
		return value.format(format);
	}

	if (typeof value == 'object' && value.date) {
		value = value.date;
	}

	let date = moment(String(value));

	if ( ! date.isValid() && fromFormat) {
		date = moment(String(value), String(fromFormat));
	}

	if (date.isValid()) {
		return date.format(format);
	}

	return String(value);
});

Vue.component('notification-nav-item', require('./components/General/NotificationNavItem.vue'));
Vue.component('notification-broadcast-form', require('./components/General/NotificationBroadcastForm.vue'));

// order creation components
Vue.component('order-creator', require('./components/Orders/Creator.vue'));

// order admin components
Vue.component('order-search-filter', require('./components/Orders/OrderSearchFilter.vue'));

Vue.component('admin-order-view', require('./components/Orders/Admin/OrderView.vue'));
Vue.component('agent-order-view', require('./components/Orders/OrderView.vue'));

Vue.component('hs--modal', require('./components/Modal.vue'));

// pickup date admin components
Vue.component('pickup-date-view-selector', require('./components/PickupDates/PickupDateViewSelector.vue'));
Vue.component('pickup-date-list', require('./components/PickupDates/PickupDateList.vue'));
Vue.component('pickup-date-detail-pane', require('./components/PickupDates/DateDetailPane.vue'));

// inventory admin components
Vue.component('inventory-adjustment-table', require('./components/Inventory/InventoryAdjustment.vue'));
Vue.component('inventory-adjustment-form', require('./components/Inventory/AdjustmentForm.vue'));
Vue.component('inventory-adjustment-update-form', require('./components/Inventory/AdjustmentUpdateForm.vue'));

// agent components
Vue.component('agency-notes', require('./components/Agency/Notes.vue'));

// agent child management components
Vue.component('child-list', require('./components/Children/ChildList.vue'));
Vue.component('child-detail-pane', require('./components/Children/ChildDetail.vue'));
Vue.component('unarchive-child', require('./components/Children/unarchivechild.vue'));
Vue.component('guardian-select', require('./components/Children/GuardianSelect.vue'));

// fulfillment admin components
Vue.component('admin-exported-pickups', require('./components/Orders/Admin/Fulfillment/ExportedPickupsList.vue'));

// reporting component(s)
Vue.component('admin-reports', require('./components/Admin/Reports.vue'));
// agent menstruator managementcomponents
Vue.component('menstruator-detail-pane', require('./components/Menstruator/MenstruatorDetail.vue'));
Vue.component('menstruator-unarchive-pane', require('./components/Menstruator/unarchivemenstruator.vue'));

// Agency Mail
// Vue.component('agency-editor', require('./components/Admin/AgencyMail/MailEditor.vue'));


window.app = new Vue({
	el: '#app',
	store: Store
});