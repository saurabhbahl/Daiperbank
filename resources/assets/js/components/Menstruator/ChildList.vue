<template>
	<div>
			<slot v-for="item in Children" name="item" v-bind="item">
				{{ item.id }}
			</slot>
	</div>
</template>

<script>
export default {
	props: ['initialChildren'],
	data() {
		return {
			Children: this.initialChildren,
		}
	},

	computed: {
		childIds() {
			return this.Children.map( Child => Child.id );
		}
	},

	methods: {
		childSaved(Child) {
			const idx = this.childIds.indexOf(Child.id);

			if (idx >= 0) {
				this.$set(this.Children, idx, Child);
			}
			else {
				this.$set(this.Children, this.Children.length, Child);
				// this.Children.push(Child);
			}
		},

		childDeleted(Child) {
			const idx = this.childIds.indexOf(Child.id);

			if (idx < 0) return;

			this.Children.splice(idx, 1);
		}
	},

	mounted() {
		this.$on('saved', this.childSaved);
		this.$on('deleted', this.childDeleted);
	},

	beforeDestroy() {
		this.$off('saved', this.childSaved);
		this.$off('deleted', this.childDeleted);
	}

}
</script>
