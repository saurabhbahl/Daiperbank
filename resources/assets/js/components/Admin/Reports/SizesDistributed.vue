<template>
	<div>
		<h2>Sizes Distributed</h2>
		<form method="post">
			<input type="hidden" name="report" value="org-report">

			<label for="product-type">Item Type</label>
			<div>
				<select v-model="category_id" class="form-control">
					<option>Select one</option>
					<option v-for="Category in ProductCategories"
							:key="'product-category-' + Category.id"
							:value="Category.id">
							{{ Category.name }}
					</option>
				</select>
			</div>

			<label for="product-id">Size</label>
			<div>
				<select v-model="product_id" class="form-control">
					<option>Select one</option>
					<option v-for="Product in CurrentCategoryProducts"
							:key="'product-' + Product.id"
							:value="Product.id">
							{{ Product.name }}
					</option>
				</select>
			</div>

			<label for="date-range">Date Range</label>
			<div>
				<datepicker
					name="date-range"
					:ReportingPeriods="ReportingPeriods"
					:DefaultRange="CurrentRange"
					@change="dateRangeChanged"
				></datepicker>
			</div>

			<input type="text" name="product_id" v-model="product_id" class="dn">
			<input type="text" name="start-date" v-model="start_date_formatted" class="dn">
			<input type="text" name="end-date" v-model="end_date_formatted" class="dn">

			<button
				type="submit"
				class="mt btn btn-primary"
				:class="{ 'disabled': ! this.downloadEnabled }"
			>Download Report</button>
		</form>
	</div>
</template>

<script>
import ReportBehaviors from './ReportBehaviors';

export default {
	mixins: [ ReportBehaviors ],

	data() {
		return {
			AllProductCategories: null,
			category_id: null,
			product_id: null,
		};
	},

	mounted() {
		this.fetchProducts();
	},

	computed: {
		downloadEnabled() {
			return this.start_date && this.end_date && this.product_id;
		},

		ProductCategories() {
			return this.AllProductCategories;
		},

		CurrentCategory() {
			if ( !this.category_id ) {
				return null;
			}

			let Category = this.AllProductCategories.filter( (Category) => {
				return Category.id == this.category_id;
			});

			if ( ! Category.length) {
				return null;
			}

			return Category[0];
		},

		CurrentCategoryProducts() {
			return this.CurrentCategory ? this.CurrentCategory.product : null;
		},
	},

	methods: {
		fetchProducts() {
			axios.get('/api/product')
			.then( (response) => {
				if (response.data.success) {
					this.loadProducts(response.data.data);
				} else {
					if (this.retries < 3) {
						this.$toast.error({
							title: "Error",
							message: "Could not load Products. Trying again.",
						});

						this.retries++;
						return this.fetchProducts();
					} else {
						this.$toast.error({
							title: "Error",
							message: "Could not load Products at this time. If this continues, please contact support.",
						});
					}
				}
			})
			.catch( err => {
				let response = err.response;
				this.$toast.error({
					title: "Error",
					message: "An unexpected error occurred while attempting to access Products. If this continues, please contact support.",
				});
			});
		},

		loadProducts(Categories) {
			this.AllProductCategories = Categories;
		},
	}
}
</script>