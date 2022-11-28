import ProductCategories from './ProductCategories';
import moment from 'moment';

export default {
	namespaced: true,
	modules: {
		ProductCategories
	},
	state: {
		Children: {},
		NewGuardians: [],
		current_child_id: null,
		new_child_ids: [],
	},

	getters: {
		Children(state) {
			return state.Children;
		},

		PickupDates(state) {
			return state.PickupDates;
		},

		activeChild(state) {
			return Object.values(state.Children).find( Child => {
				return Child.id == state.current_child_id;
			});
		},

		Guardians(state) {
			return state.NewGuardians;
		},

		ProductCategories(state, getters) {
			return getters['ProductCategories/get']; //state.ProductCategories;
		},

		activeChildId(state) {
			return state.current_child_id;
		},

		nextNewChildId(state) {
			return 'new-' + ((state.new_child_ids.length + 1) * -1);
		},

		Products(state) {
			let allProducts = Object.values(state.Children).map( Child => {
				return {...Child.Product};
			});

			let groupedProducts = {};
			allProducts.forEach( Product => {
				if (Product && undefined == groupedProducts[ Product.id ]) {
					groupedProducts[ Product.id ] = {
						...Product
					};
				}
				else if (Product) {
					groupedProducts[ Product.id ].qty += parseInt(Product.qty);
				}
			});

			return Object.values(allProducts);
		},

		CategoryProducts(state, getters) {
			return (category_id) => {
				let all_category_product_ids = getters['ProductCategories/getCategoryProducts'](category_id).map(Product => {
					return Product.id;
				});
				let allOrderProducts = getters['Products'];

				return allOrderProducts.filter( Product => {
					return all_category_product_ids.indexOf(Product.id) >= 0;
				});
			};
		},
	},
	mutations: {
		addItem(state, { Child, Product, qty }) {
			// have to re-compose the entire children object
			// due to the way vue.js detects changes on objects
			// which is to say, new properties on objects are not
			// observed. So you have to replace the object completely.

			state.Children = {
				...state.Children,
				[ Child.id ]:  {
					...Child,
					Product: {
						...Product,
						qty: parseInt(qty)
					},
				},
			};
		},

		addChild(state, { Child }) {
			if (Child) {
				state.Children = {
					...state.Children,
					[Child.id]: {
						...Child,
						Product: null,
					}
				};
			}
		},

		createChild(state, { Child }) {
			state.new_child_ids.push( Child.id );
			state.Children = {
				...state.Children,
				[Child.id]: {
					...Child,
					isNew: true,
					Product: null,
				},
			};
		},

		removeChild(state, { child_id }) {
			delete state.Children[ child_id ];
		},

		pickupDateSelected(state, date) {
			state.pickup_date_id = date;
		},

		selectChild(state, child_id) {
			state.current_child_id = child_id;
		},

		viewOrderReceipt(state, { order_id }) {
			window.location = '/order/' + order_id;
		},

		createGuardian(state, { Guardian }) {
			Guardian.id = "new-" + ((state.NewGuardians.length + 1) * -1);
			Guardian.isNew = true;
			state.NewGuardians.push( Guardian );

			state.Children[ state.current_child_id ].guardian_id = Guardian.id;
		},
	},

	actions: {
		addChild(context, payload) {
			const Child = payload.Child;

			context.commit({
				type: 'addChild',
				Child: Child,
			});

			context.dispatch({
				type: 'selectChild',
				child_id: Child.id,
			});
		},

		createChild(context, { name }) {
			const newChildId = context.getters.nextNewChildId;
			context.commit({
				type: 'createChild',
				Child: {
					id: newChildId,
					name: name,
				},
			});

			context.dispatch({
				type: 'selectChild',
				child_id: newChildId,
			});
		},

		removeChild(context, { child_id }) {
			context.commit({
				type: 'removeChild',
				child_id: child_id,
			});

			const childIndexes = Object.keys(context.getters.Children)
			let lastChildId = null;

			if (childIndexes.length > 1) {
				lastChildId = childIndexes[ childIndexes.length - 1 ];
			}

			context.dispatch({
				type: 'selectChild',
				child_id: lastChildId,
			});
		},

		addItem(context, payload) {
			let { Child, Product, qty } = payload;

			context.commit({
				type: 'addItem',
				Child,
				Product,
				qty
			});
		},

		selectChild(context, { child_id }) {
			context.commit('selectChild', child_id);
		},

		pickupDateSelected(context, { date }) {
			context.commit('pickupDateSelected', date);
		},

		submit(context) {
			const Children = Object.values(context.getters.Children).map(Child => {
				let tmpChild = {
					...Child
				};

				let Guardian = context.getters.Guardians.find(Guardian => {
					return Guardian.id == tmpChild.guardian_id;
				});

				if (Guardian) {
					tmpChild.Guardian = Guardian;
				}

				return tmpChild;
			});
			window.axios.post("/api/orders/create", {
				Children: Children,
				pickup_date_id: context.state.pickup_date_id,
			}).then(response => {
				window.console.log(response);

				context.commit({
					type: 'viewOrderReceipt',
					order_id: response.data.id,
				});
			}).catch(error => {
				console.log(error);
			});
		}
	}
};