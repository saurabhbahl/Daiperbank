<template>
	<div>
		<div class="flex flex-column justify-between h-100">
			<div class="pa oy-auto">
				<p class="b f2">
					<i class="fa" :class="{ 'fa-female': Child.gender === 'f', 'fa-male': Child.gender === 'm'}"></i>
					{{ Child.name }}
				</p>

				<div class="flex justify-between">
					<div class="flex-auto w-50 mr3">
						<p class="mb2">
							<span class="b">ID:</span>
							{{ Child.uniq_id }}
						</p>

						<p class="mb2">
							<span class="b">Zip:</span>
							{{ Child.zip }}
						</p>

						<p class="mb2">
							<span class="b">DOB:</span>
							<span>{{ Child.dob | formatDate("MM/DD/YYYY") }}</span>
						</p>

						<p class="mb2">
							<span class="b">Gender:</span>
							<span>
								<i class="fa" :class="{ 'fa-female': Child.gender === 'f', 'fa-male': Child.gender === 'm'}"></i>
								<span v-if="Child.gender === 'f'">Female</span>
								<span v-else-if="Child.gender === 'm'">Male</span>
							</span>
						</p>
					</div>
					<div class="flex-auto w-50">
						<p class="mb2">
							<span class="b">Current Weight:</span>
							<span>{{ Child.weight_str }}</span>
						</p>

						<p class="mb2">
							<span class="b">Current Age:</span>
							<span>{{ Child.age_str }}</span>
						</p>

						<p class="mb2">
							<span class="b">Potty Training:</span>
							<span>
								<span v-if="Child.status_potty_train">
									<i class="fa fa-check dark-green"></i>
									Yes
								</span>
								<span v-else>
									<i class="fa fa-times dark-red"></i>
									No
								</span>
							</span>
						</p>

						<p class="mb2">
							<span class="b">Receiving WIC:</span>
							<span>
								<span v-if="Child.status_wic">
									<i class="fa fa-check dark-green"></i>
									Yes
								</span>
								<span v-else>
									<i class="fa fa-times dark-red"></i>
									No
								</span>
							</span>
						</p>
					</div>
				</div>

				<p class="b f2 bb bw2 b--black-20 mv3">Parent/Guardian</p>
				<div class="flex justify-between items-start">
					<p class="flex-auto">
						<i class="fa" :class="{'fa-female': Child.child.guardian.gender === 'f', 'fa-male': Child.child.guardian.gender === 'm'}"></i>
						<span class="b">{{ Child.child.guardian.name }}</span>
						<br>
						<span class="muted f4">{{ Child.child.guardian.email }}</span>
					</p>

					<p class="flex-auto tr">
						<span class="b">Veteran?</span>
						{{ Child.child.guardian.military_status.toLowerCase().indexOf('non-military') < 0? 'Yes' : 'No' }}
						<br>
						<span v-if="Child.child.guardian.dob"
							class="muted f4">
							<span class="i">dob</span>
							{{ Child.child.guardian.dob }}
						</span>
					</p>
				</div>

				<p class="b f2 bb bw2 b--black-20 mv3">Current Order</p>
				<table class="table table-condensed table-striped" v-if=" ! editing">
					<thead>
						<tr>
							<th class="w-33">Type</th>
							<th class="w-33">Size</th>
							<th class="w-33">Quantity</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td v-if="Child.item">{{ Child.item.product.category.name }}</td>
							<td v-else>--</td>

							<td v-if="Child.item">
								<!-- {{ Child.item.product.name }} -->
								{{ Child.item.product.name.replace(/Boy|Girl/g, '') }}
							</td>
							<td v-else>--</td>

							<td v-if="Child.item">{{ Child.item.quantity }}</td>
							<td v-else>--</td>
						</tr>
					</tbody>
				</table>

				<ProductSelector
					v-else-if="editing"
					:product-categories="productCategories"
					:initial-selected-product="selectedProductId"
					:initial-quantity="selectedProductQuantity"
					@change="onProductChange"
				></ProductSelector>

				<div v-if="editing"
					class="flex justify-end pt3">

					<div class="w-33 mr fg-no">
						<label for="weight">Weight:</label>
						<div class="input-group">
							<input type="tel"
								id="weight"
								name="weight"
								class="form-control"
								v-model="EditedChild.weight"
								@change="onChildChanged"
								@keyup="onChildChanged">
							<span class="input-group-addon">lbs</span>
						</div>
					</div>

					<div class="w-33">
						<label for="status_potty_train_yes">Potty Training?</label>
						<p>
							<label for="status_potty_train_yes"
								class="ma0">
								<input type="checkbox"
									class="fwn"
									id="status_potty_train_yes"
									v-model="EditedChild.status_potty_train"
									value="1"
									@change="onChildChanged"
									@mouseup="onChildChanged">
									Yes
							</label>
						</p>
					</div>
				</div>

				<div v-if="editing && hasError()"
					class="dark-red bg-washed-red pa3 mt3">
					<p v-for="(msg, field) in error()" :key="'error-' + field">{{ msg }}</p>
				</div>

				<div v-if="editing && allow_child_note">
					<p class="b f2 bb bw2 b--black-20 mv3">
						Add a reason:

						<label for="agency_share_note"
							class="dib f3 pull-right">
							<input type="checkbox"
									:value="true"
									id="agency_share_note"
									v-model="flag_editing_reason_share">

							Share with Agency?
						</label>
					</p>

					<p v-if="flag_editing_reason_share"
						class="i f4 mv2">
						<i class="fa fa-exclamation-triangle orange"></i>
						Any message you type here will be shared with your agency partner.
					</p>

					<textarea class="form-control mb"
							rows="4"
							v-model="editing_reason">
					</textarea>
				</div>
			</div>

			<!-- Putting this here because IE11 does not remove position:absolute items from the flexbox layout flow
			documentation:
				- https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/12500543/
				- https://stackoverflow.com/questions/32991051/absolutely-positioned-flex-item-is-not-removed-from-the-normal-flow-in-ie11
			-->
			<a
				href="#"
				class="pxa pinr pint a-plain pa"
				@click="close">

				<i class="fa fa-times"></i>
			</a>

			<div class="bg-washed-blue pa bt b--black-20">
				<slot name="buttons"></slot>
			</div>
		</div>
	</div>
</template>

<script>
import ProductSelector from './ProductSelector.vue';
import validation from '../../../mixins/validation';

export default {
	components: { ProductSelector },
	mixins: [ validation ],

	props: {
		initialChild: {
			type: Object,
			required: true,
		},

		productCategories: {
			type: Array,
			required: true,
		},

		isEditable: {
			type: Boolean,
			required: false,
			default: true,
		},

		initialEditing: {
			required: false,
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			Child: this.initialChild,
			EditedChild: this.clone(this.initialChild),
			editing: this.initialEditing,
			product_selection_valid: true,
			selectedProduct: {
				...(this.initialChild.item? this.initialChild.item.product : []),
				quantity: this.initialChild.item? this.initialChild.item.quantity : null,
			},
			editing_reason: null,
			flag_editing_reason_share: false,

			allow_child_note: false,
		};
	},

	computed: {
		selectedProductId() {
			if (this.Child.item) {
				return this.Child.item.product_id;
			}

			return null;
		},

		selectedProductQuantity() {
			if (this.Child.item) {
				return this.Child.item.quantity || null;
			}

			return null;
		}
	},

	methods: {
		close() {
			this.$emit('close');
		},

		onChildChanged() {
			this.$emit('change', this.EditedChild);
		},

		onProductChange(valid, selectedProduct) {
			this.product_selection_valid = valid;
			this.selectedProduct = selectedProduct;

			let existingProduct = {
				product_id: this.EditedChild.product_id,
				quantity: this.EditedChild.quantity,
			};

			this.EditedChild.product_id = selectedProduct.id;
			this.EditedChild.quantity = selectedProduct.quantity;

			this.$emit('productSelected', valid, selectedProduct);

			if (existingProduct.product_id != this.EditedChild.product_id
				|| existingProduct.quantity != this.EditedChild.quantity) {
				this.onChildChanged();
			}
		},

		clone(object) {
			return JSON.parse(JSON.stringify(object));
		}
	},
}
</script>