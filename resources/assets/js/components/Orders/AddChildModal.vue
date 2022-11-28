
<template>
	<Modal>
		<span slot="header">
			Add Child
		</span>

		<div slot="body">
			<p>Type a child's name to add them to the order.</p>
			<p><em>Tip:</em>
				Need to create a child? Just start typing their name, and you'll be able to create a new one.
			</p>

			<SelectFilter
				:items="childItems"
				@chosen="addChild"
				@new="createChild"
				:itemFormatter="itemFormatter"
			/>
		</div>

		<div slot="buttons">
			<button class="btn btn-block btn-default"
				@click="close"
			>Cancel</button>
		</div>
	</Modal>
</template>

<script>
import Vue from 'vue';
import { Modal } from '../Modals';
import SelectFilter from '../General/SelectFilter.vue';

export default {
	props: ['Children'],

	components: { SelectFilter, Modal },

	data() {
		return {
		};
	},

	computed: {
		childItems() {
			return Object.values(this.Children).map(Child => {
				return {
					value: Child.id,
					label: Child.name,
				}
			})
		},

		hashedChildren() {
			let hashed = {};
			this.Children.map(Child => {
				hashed[ Child.id ] = Child;
			});

			return hashed;
		},

		itemFormatter() {
			const parent = this;

			return Vue.component('child-search-item', {
				props: ['item'],
				computed: {
					realItem() {
						return parent.hashedChildren[ this.item.value ];
					},
				},
				template: '<span>'
			+ '<i class="fa" :class="{'
			+ 	'\'fa-male\': realItem.gender && realItem.gender == \'m\','
			+	'\'fa-female\': realItem.gender && realItem.gender == \'f\''
			+ '}"></i> {{ realItem.name }}</span>',
			});
		}
	},

	methods: {
		addChild(item) {
			this.$store.dispatch({
				type: 'Order/addChild',
				Child: this.hashedChildren[ item.value ],
			});

			this.close();
		},

		createChild(name) {
			this.$store.dispatch({
				type: 'Order/createChild',
				name: name,
			});

			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		}
	}
}
</script>