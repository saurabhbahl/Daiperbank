export default {
	namespaced: true,
	state: {
		Categories: {
			1: {
				id: 1,
				name: 'Diaper',
				Products: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ],
			},
			2: {
				id: 2,
				name: 'Pull-up',
				Products: [ 10, 11, 12 ],
			},
		},
		Products: {
			1: { id: 1, name: "Preemie", category_id: 1 },
			2: { id: 2, name: "Newborn", category_id: 1 },
			3: { id: 3, name: "Size 1", category_id: 1 },
			4: { id: 4, name: "Size 2", category_id: 1 },
			5: { id: 5, name: "Size 3", category_id: 1 },
			6: { id: 6, name: "Size 4", category_id: 1 },
			7: { id: 7, name: "Size 5", category_id: 1 },
			8: { id: 8, name: "Size 6", category_id: 1 },
			9: { id: 9, name: "Size 7", category_id: 1 },

			10: { id: 10, name: "2T-3T", category_id: 2 },
			11: { id: 11, name: "3T-4T", category_id: 2 },
			12: { id: 12, name: "4T-5T", category_id: 2 },
		},
	},
	getters: {
		get(state) {
			return Object.values(state.Categories);
		},

		getCategory(state) {
			return (category_id) => {
				return state.Categories[ category_id ];
			};
		},

		getCategoryProducts(state) {
			return (category_id) => {
				return Object.values(state.Products).filter( Product => {
					return state.Categories[ category_id ].Products.indexOf( Product.id ) >= 0;
				});
			};
		},

		getProduct(state) {
			return (product_id) => {
				return state.Products[ product_id ];
			}
		}
	}
}