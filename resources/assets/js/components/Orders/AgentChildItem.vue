<template>
	<ChildItem
		:child="Child"
		:is-editable="isEditable"
		@click="onClick">

		<template slot="action">
			<p v-if=" ! isValid">
				<i class="fa fa-warning fa-lg washed-orange"></i>
			</p>
		</template>
	</ChildItem>
</template>

<script>
import ChildItem from './ChildItem.vue';
import sharedChildItemBehaviors from './sharedChildItemBehaviors';

export default {
	components: { ChildItem },
	mixins: [ sharedChildItemBehaviors ],
	data() {
		return { ChildItem };
	},

	computed: {
		isValid() {
			if ( ! this.Child.item) return false;
			if ( ! this.Child.item.product_id) return false;
			if ( ! this.Child.item.quantity || this.Child.item.quantity < 1 ) return false;
			if (2 == this.Child.item.product.category_id) {
				if (null === this.Child.weight) return false;
				if (0 === this.Child.weight) return false;
			}

			return true;
		},
	},
}
</script>