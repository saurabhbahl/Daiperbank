export default {
	namespaced: true,

	state: {

	},

	mutations: {

	},

	actions: {
		reject(context, payload) {
			axios.post(
				'/api/orders/' + payload.Order.id + '/reject',
				{
					reason: payload.reason,
					flag_share_reason: payload.flag_share_reason,
				},
				{
					responseType: 'json',
				}
			).then(function(response) {
				window.location = response.data.next;
			}).catch(function(error) {
				console.log(error.message);
			});
		}
	}
}