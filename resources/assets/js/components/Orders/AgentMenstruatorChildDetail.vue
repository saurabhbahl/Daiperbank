<template>
	<MenstruatorChildDetail
		:is-editable="isEditable"
		:is-editing="isEditing"
		:initial-child="Child"
		:product-categories="productCategories"
		:initial-errors="errors"

		@change="onChildChanged"
		@close="close">

		<template slot="buttons">
			<button
				v-if="isEditable"

				key="remove-btn"
				class="btn btn-block btn-danger btn-alt mb4"
				:class="{'disabled': processing }"
				@click="removeClicked">
				<i class="fa fa-trash-o"></i>
				Remove From Order
			</button>

			<button
				v-if="isEditable"

				key="save-btn"
				class="btn btn-block btn-success mb"
				:class="{'disabled': (! isValid) || processing }"
				@click="saveClicked">
				<i class="fa fa-check"></i>
				Save
			</button>

			<button class="btn btn-block btn-default"
				key="cancel-btn"
				@click="cancelClicked">
				<i class="fa fa-times"></i>
				Close
			</button>
		</template>
	</MenstruatorChildDetail>
	
</template>

<script>

import ChildDetailBehavior from './sharedChildDetailBehaviors';
import MenstruatorChildDetail from './MenstruatorChildDetail.vue';

export default {
	components: { MenstruatorChildDetail },
	mixins: [ ChildDetailBehavior ],

	data() {
		return {
			// selectedProduct: (this.initialChild.item? this.initialChild.item : null),
		};
	},

	methods: {
		/** Event Handling **/
		cancel() {
			// if (this.isEditing) {
				// this.isEditing = false;
			// } else {
				this.close();
			// }
		},

		removeClicked() {
			this.$emit('remove', this.Child);
		},

		saveClicked() {
			this.$emit('save', this.Child, this.getSaveData());
		},

		getSaveData() {
			return {
				product_id: this.editedChild.product_id,
				quantity: this.editedChild.quantity,
				weight: this.editedChild.weight,
				status_potty_train: this.editedChild.status_potty_train,
			};
		},

		// onProductSelected(isValid, selectedProduct) {
		// 	if (isValid) {
		// 		// this.selectedProduct = selectedProduct;
		// 		this.editedChild.product_id = selectedProduct.product_id;
		// 		this.editedChild.quantity = selectedProduct.quantity;
		// 		return;
		// 	}
		// }
	},
}
</script>