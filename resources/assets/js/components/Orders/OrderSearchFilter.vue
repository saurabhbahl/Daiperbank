<template>
	<div>
		<a href="#"
			class="flex justify-between content-center items-center a-plain"
			@click="toggleVisible">
			<h1 class="ma0 pa0 f2">
				<i class="fa fa-filter "></i>
				Filter

				 <span class="f4" v-if="filtersActive">
					<a href="?" @click.stop>
						clear filters <i class="fa fa-remove"></i>
					</a>
				 </span>
			</h1>

			<p>
				<i class="fa"
					:class="{'fa-caret-down': !this.is_visible, 'fa-caret-up': this.is_visible}"></i>
			</p>
		</a>

		<div class="pt3" v-show="is_visible">
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	props: ['show', 'filtersActive'],

	data() {
		return {
			'is_visible': this.show || false,
		};
	},

	methods: {
		toggleVisible() {
			this.is_visible = !this.is_visible;
		}
	}
}
</script>

