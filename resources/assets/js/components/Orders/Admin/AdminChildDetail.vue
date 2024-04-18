<template >
	<ChildDetail 
		:is-editable="isEditable"
		:is-editing="isEditing"
		:initial-child="Child"
		:product-categories="productCategories"
		:is-admin="isAdmin"
		:isFulfilled="isFulfilled"
		:all-children="AllChildren"
		
		@change="onChildChanged"
		@close="close" 
		v-if="this.Child.is_menstruator==0" @edit-order="isEditing = true"
		>

		<template slot="buttons">
			<div v-if=" ! isEditing && pending && isEditable">
				<button
					key="edit-btn"
					class="btn btn-block btn-primary mb"
					@click="editClicked">
					<i class="fa fa-pencil"></i>
					Edit
				</button>
			</div>

			<div v-else-if="isEditing">
				<button
					key="remove-btn"
					class="btn btn-block btn-danger btn-alt mb4"
					:class="{'disabled': processing}"
					@click="removeClicked">
					<i class="fa fa-trash-o"></i>
					Remove From Order
				</button>

				<button
					key="save-btn"
					class="btn btn-block btn-success mb"
					:class="{'disabled': (! isValid) || processing }"
					@click="saveClicked">
					<i class="fa fa-check"></i>
					Save & Approve
				</button>
			</div>

			<div v-else-if="approved && isEditable">
				<button
					key="unapprove-btn"
					class="btn btn-block btn-default mb"
					:class="{'disabled': processing}"
					@click="unapprovedClicked">
					<i class="fa fa-times"></i>
					Move to Pending
				</button>
			</div>

			<div v-else-if="removed && isEditable">
				<button
					key="restore-btn"
					class="btn btn-block btn-default mb"
					:class="{'disabled': processing}"
					@click="restoreClicked">
					<i class="fa fa-check"></i>
					Restore
				</button>
			</div>

			<button class="btn btn-block btn-default btn-ghost"
				:class="{'disabled': processing}"
				v-if="isEditable"
				key="cancel-btn"
				@click="cancelClicked">
				<i class="fa fa-times"></i>
				Cancel
			</button>

			<button class="btn btn-block btn-default btn-ghost"
				v-else
				key="cancel-btn"
				@click="cancelClicked">
				<i class="fa fa-times"></i>
				Close
			</button>
		</template>
	</ChildDetail>
	<MenstruatorChildDetail
		:is-editable="isEditable"
		:is-editing="isEditing"
		:initial-child="Child"
		:product-categories="productCategories"

		@change="onChildChanged"
		@close="close" v-else>

		<template slot="buttons">
			<div v-if=" ! isEditing && pending && isEditable">
				<button
					key="edit-btn"
					class="btn btn-block btn-primary mb"
					@click="editClicked">
					<i class="fa fa-pencil"></i>
					Edit
				</button>
			</div>

			<div v-else-if="isEditing">
				<button
					key="remove-btn"
					class="btn btn-block btn-danger btn-alt mb4"
					:class="{'disabled': processing}"
					@click="removeClicked">
					<i class="fa fa-trash-o"></i>
					Remove From Order
				</button>

				<button
					key="save-btn"
					class="btn btn-block btn-success mb"
					:class="{'disabled': (! isValid) || processing }"
					@click="saveClicked">
					<i class="fa fa-check"></i>
					Save & Approve
				</button>
			</div>

			<div v-else-if="approved && isEditable">
				<button
					key="unapprove-btn"
					class="btn btn-block btn-default mb"
					:class="{'disabled': processing}"
					@click="unapprovedClicked">
					<i class="fa fa-times"></i>
					Move to Pending
				</button>
			</div>

			<div v-else-if="removed && isEditable">
				<button
					key="restore-btn"
					class="btn btn-block btn-default mb"
					:class="{'disabled': processing}"
					@click="restoreClicked">
					<i class="fa fa-check"></i>
					Restore
				</button>
			</div>

			<button class="btn btn-block btn-default btn-ghost"
				:class="{'disabled': processing}"
				v-if="isEditable"
				key="cancel-btn"
				@click="cancelClicked">
				<i class="fa fa-times"></i>
				Cancel
			</button>

			<button class="btn btn-block btn-default btn-ghost"
				v-else
				key="cancel-btn"
				@click="cancelClicked">
				<i class="fa fa-times"></i>
				Close
			</button>
		</template>
	</MenstruatorChildDetail>
</template>

<script>
import ChildDetail from '../ChildDetail.vue';
import MenstruatorChildDetail from '../MenstruatorChildDetail.vue';

import ChildDetailBehavior from '../sharedChildDetailBehaviors';

export default {
	components: { ChildDetail ,MenstruatorChildDetail},
	mixins: [ ChildDetailBehavior ],
	props: {
		allChildren: {
			required: true,
			type: Array,
		},
		isAdmin: {
			type: Boolean,
			required: false,
			default: false,
		},
		isFulfilled: {
			type: Boolean,
			required: false,
			default: false,
		},
	},

	data() {
		return {
			AllChildren: this.allChildren,
			// selectedProduct: (this.initialChild.item? this.initialChild.item : null),
		};
	},
	computed: {
		pending() {
			return ! this.approved && ! this.removed;
		},

		approved() {
			return !! this.Child.item && this.Child.item.flag_approved;
		},

		removed() {
			return !! this.Child.item && this.Child.item.deleted_at;
		},
	},

	methods: {
		/** Event Handling **/
		cancel() {
			if (this.isEditing) {
				this.isEditing = false;
			} else {
				this.close();
			}
		},

		close() {
			this.$emit('close');
		},

		cancelClicked() {
			this.cancel();
		},

		editClicked() {
			if (this.isEditable) {
				this.isEditing = true;
			}
		},

		removeClicked() {
			this.$emit('remove', this.Child);
		},

		restoreClicked() {
			this.$emit('restore', this.Child);
		},

		saveClicked() {
			this.$emit('save', this.Child, this.getSaveData());
		},

		getSaveData() {
			// TODO
			// Need to update this
			// This component doesn't have direct access to these values
			// The child child detail component (ugh, names) has this info, and needs
			// to emit events to pass them back up (le sigh).
			//
			/*
			 {
				product_id: this.selectedProduct.id,
				quantity: this.selectedProduct.quantity,
				note: this.editing_reason,
				flag_note_share: this.flag_editing_reason_share
			}
			 */
			return {
				product_id: this.editedChild.product_id,
				quantity: this.editedChild.quantity,
				weight: this.editedChild.weight,
				status_potty_train: this.editedChild.status_potty_train,
			};
		},

		unapprovedClicked() {
			this.$emit('unapprove', this.Child);
		},

		onDocumentKeyup(evt) {
			if (evt.keyCode == 27) {
				this.cancel();
				evt.preventDefault();
			}
		},
	},

	mounted() {
		document.addEventListener('keyup', this.onDocumentKeyup);
	},

	beforeDestroy() {
		document.removeEventListener('keyup', this.onDocumentKeyup);
	}
}
</script>