const VIEW_CALENDAR = 'calendar';
const VIEW_LIST = 'list';

export default {
	namespaced: true,

	state: {
		currentView: VIEW_CALENDAR,
		activeDate: null,
	},

	getters: {
		currentView(state) {
			return state.currentView;
		},

		activeDate(state) {
			return state.activeDate;
		},
	},

	mutations: {
		changeView(state, { view }) {
			state.currentView = view;
		},

		viewDate(state, { date }) {
			state.activeDate = date;
		}
	},
};
