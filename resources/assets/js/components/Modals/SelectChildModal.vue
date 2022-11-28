<template>
	<Modal>
		<span slot="header">
			Add Child to Order
		</span>

		<div slot="body">
			<p>
				Select a child from the dropdown below.
			</p>

			<ChildSelector
				:children="Children"
				@change="onSelect"
				@emptyClicked="onEmptyClicked"
			></ChildSelector>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				Cancel
			</button>

			<button class="btn btn-primary" @click="confirmSelection">
				Add to Order
			</button>
		</div>
	</Modal>
</template>

<script>
import ChildSelector from '../General/ChildSelector.vue';
import Modal from './Modal';

export default {
	components: { Modal, ChildSelector },

	props: {
		allChildren: {
			required: true,
			type: Array,
		},
	},

	data() {
		return {
			Children: this.allChildren,
			selected_child_id: null,
		}
	},

	computed: {
		SelectedChild() {
			return this.Children.filter( (Child) => Child.id == this.selected_child_id )[0] || null;
		},
	},

	methods: {
		onSelect(child_id) {
			this.selected_child_id = child_id
		},

		onEmptyClicked(child_name) {
			this.$root.$emit('child/create', child_name);
			this.close();
		},

		confirmSelection() {
			this.$root.$emit('child/select', this.SelectedChild);
			this.close();
		},

		cancel() {
			this.$root.$emit('child/select/cancel');
			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>