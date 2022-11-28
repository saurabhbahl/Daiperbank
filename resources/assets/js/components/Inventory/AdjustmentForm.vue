<template>
	<div>
		<p class="mb">
			<label for="debit_credit">Type:
				<span class="required">*</span>
			</label>
			<select
					name="debit_credit"
					id="debit_credit"
					class="form-control"
					v-model="adjustment.debit_credit">
				<option value="credit">Incoming</option>
				<option value="debit">Outgoing</option>
			</select>

			<p v-if="hasError('debit_credit')" class="validation-error">
				{{ error('debit_credit') }}
			</p>
		</p>

		<p class="mb">
			<label for="type">Source:
				<span class="required">*</span>
			</label>
			<select
				name="type"
				id="type"
				class="form-control"
				v-model="adjustment.type">
				<option :value="null" disabled>Select one</option>
				<option v-for="(type, id) in typeMap"
					:key="id"
					:value="id">
					{{ type }}
				</option>
			</select>
			<p v-if="hasError('type')" class="validation-error">
				{{ error('type') }}
			</p>
		</p>

		<p class="mb">
			<label for="adjustment_datetime">Date of adjustment:
				<span class="required">*</span>
			</label>
			<input type="datetime-local" id="adjustment_datetime"
				name="adjustment_datetime"
				v-model="adjustment.adjustment_datetime">

			<p v-if="hasError('adjustment_datetime')" class="validation-error">
				{{ error('adjustment_datetime') }}
			</p>
		</p>

		<p class="mb">
			<a href="#" @click="note_visible = !note_visible">{{ ( ! note_visible? 'Add' : 'Remove') }} note</a>
			<p v-if="hasError('note')" class="validation-error">
				{{ error('note') }}
			</p>
			<textarea v-show="note_visible"
				v-model="adjustment.note"
				name="note"
				class="form-control" rows="3">
				</textarea>
		</p>

		<p class="b mb">Diapers</p>

		<div class="mt3 bl bt br b--black-20">
			<div class="flex justify-start pa4 bg-black-10 mb3 bb b--black-40 bw2">
				<p class="b w-30 ph3">Type</p>
				<p class="b w-30 ph3">Size</p>
				<p class="b w-20 ph3">Quantity</p>
			</div>

			<div v-for="(product, i) in adjustment.products"
				:key="'product_' + i"
				class="flex justify-start items-center pb3 mb3 bb b--black-20">

				<div class="w-30 ph3">
					<select v-model="product.product_category_id"
							:name="'product[' + i + '][product_category_id]'"
							class="form-control"
							@change="addProductIfNeeded">

						<option :value="null" disabled>Select one</option>

						<option v-for="category in productCategories"
								:value="category.id">
								{{ category.name }}
						</option>
					</select>
				</div>

				<div class="w-30 ph3">
					<select v-model="product.product_id"
						:name="'product[' + i + '][product_id]'"
						class="form-control"
						:disabled="product.product_category_id == null">

						<option :value="null" disabled>Select one</option>

						<option v-for="diaper in getCategoryProducts(product.product_category_id)"
								:value="diaper.id">
								{{ diaper.name }}
						</option>
					</select>
				</div>

				<div class="w-20 ph3">
					<input type="tel" class="form-control"
						:disabled="product.product_category_id == null || product.product_id == null"
						v-model="product.quantity"
						:name="'product[' + i + '][quantity]'">
				</div>

				<div class="w-10 ph3">
					<a href="#"
						v-if="product.product_category_id != null"
						@click="removeProduct(i)">

						<i class="fa fa-close"></i>
					</a>
				</div>
			</div>
		</div>

		<div>
			<button class="btn btn-primary"
				:disabled="! submittable">
				Save
			</button>
		</div>

	</div>
</template>

<script>
export default {
	props: {
		typeMap: {
			required: true,
			type: Object
		},

		productCategories: {
			required: true,
			type: Array,
		},

		initialErrors: {
			required: false,
			default: null,
		},

		initialValues: {
			required: false,
			default: {},
			type: [Object, Boolean, Array],
		}
	},

	data() {
		return {
			adjustment: {
				debit_credit: this.initialValues.debit_credit || 'credit',
				adjustment_datetime: this.initialValues.adjustment_datetime || null,
				type: this.initialValues.type || null,
				note: this.initialValues.note || null,
				products: this.initialValues.product || [],
			},

			note_visible: this.initialValues.note && this.initialValues.note.length || false,
			errors: this.initialErrors,
		};
	},

	computed: {
		submittable() {
			let valid = this.adjustment.debit_credit
					&& this.adjustment.type
					&& ( ! this.note_visible || this.adjustment.note )
					&& ( this.adjustment.products.length );

			let valid_products = this.adjustment.products.map( (product) => {
				let product_valid = ( product.product_category_id && product.product_id && product.quantity && product.quantity > 0);

				valid =  valid && (! product.product_category_id || product_valid);

				return product_valid;
			});

			return valid && !! valid_products.filter((valid) => valid === true).length;
		}
	},

	methods: {
		getCategoryProducts(category_id) {
			let products = this.productCategories.map( (category) => {
				return category_id && category.id == category_id? category.product : [];
			});


			return [].concat(...products);
		},

		addProduct() {
			this.adjustment.products.push( { product_category_id: null, product_id: null, quantity: null } );
		},

		addProductIfNeeded() {
			let needs_empty = true;

			this.adjustment.products.map( (product) => {
				needs_empty = needs_empty && product.product_category_id != null;
			});

			if (needs_empty) {
				this.addProduct();
			}
		},

		removeProduct(index) {
			this.adjustment.products.splice(index, 1);

			this.addProductIfNeeded();
		}
	},

	created() {
		this.addProduct();
	}
}
</script>