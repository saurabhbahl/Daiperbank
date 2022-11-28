export default class {
	constructor(dateContext, items) {
		this.dateContext = dateContext;
		this.items = items || [];
	}

	isPadding() {
		return ! this.dateContext;
	}
}