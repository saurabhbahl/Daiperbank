function arrayize(value) {
	if ( ! value instanceof Array) {
		return [ value ];
	}

	return value;
}

export default {
	methods: {
		countOrders(Fulfillments) {
			return arrayize(Fulfillments).reduce( (total, f) => {
				return total + f.order.length;
			}, 0);
		},

		countChildren(Fulfillments) {
			return arrayize(Fulfillments).reduce( (total, f) => {
				return f.order.reduce( (total, o) => {
					return total + o.approved_child.length;
				}, total);
			}, 0);
		},

		countDiapers(Fulfillments) {
			return arrayize(Fulfillments).reduce( (total, f) => {
				return f.order.reduce( (total, o) => {
					return o.approved_item.reduce( (total, i) => {
						return i.product.category.id == '1'? total + i.quantity : total;
					}, total)
				}, total);
			}, 0);
		},

		countPullups(Fulfillments) {
			return arrayize(Fulfillments).reduce( (total, f) => {
				return f.order.reduce( (total, o) => {
					return o.approved_item.reduce( (total, i) => {
						return i.product.category.id == '2'? total + i.quantity : total;
					}, total)
				}, total);
			}, 0);
		},
	}
}