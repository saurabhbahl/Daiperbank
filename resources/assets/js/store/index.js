import 'es6-promise/auto';

import Vuex from 'vuex';
import Vue from 'vue';

// store modules
import Modal from './modules/Modal';
import Order from './modules/Order';
import AdminOrder from './modules/AdminOrder';
import AdminPickupDate from './modules/AdminPickupDate';

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		Children: [
			{ id: 1, name: 'Brenden' },
			{ id: 2, name: 'Piper' },
			{ id: 3, name: 'Trey' },
		],
	},

	getters: {
		AllChildren(state) {
			return state.Children;
		},

		Child(state) {
			return child_id => {
				return Object.values(state.Children).find( Child => {
					return Child.id == child_id;
				});
			}
		}
	},

	mutations: {

	},

	modules: {
		Order,
		AdminOrder,
		AdminPickupDate,
		Modal
	}
});