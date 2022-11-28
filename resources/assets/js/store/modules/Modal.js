export default {
	namespaced: true,

	state: {
		show: false,
		modalComponent: null,
		props: null,
		events: {},
	},

	getters: {
		visible(state) {
			return state.show;
		},

		modalComponent(state) {
			return state.modalComponent;
		},

		props(state) {
			return state.props;
		},

		events(state) {
			return state.events;
		},
	},

	mutations: {
		show(state, payload) {
			state.modalComponent = payload.modalComponent;
			state.props = payload.props;
			state.events = payload.events || {};

			state.show = true;
		},

		close(state) {
			state.show = false;
			state.modalComponent = null;
			state.props = null;
		}
	},

	actions: {
		show(context, payload) {
			context.commit({
				type: 'show',
				modalComponent: payload.modalComponent,
				props: payload.props,
			});
		},

		close(context) {
			context.commit({
				type: 'close'
			});
		}
	}
}