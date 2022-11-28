<template>
	<SelectFilter
		placeholder="Select one"
		:items="Guardians"
		value-key="id"
		:search-fields="searchFields"
		:initialValue="value"
		@selected="onSelect">

		<template slot="selected-item" slot-scope="Guardian">
			<p class="b">
				<i class="fa"
					:class='{"fa-male": Guardian.gender == "m", "fa-female": Guardian.gender == "f"}'></i>
				{{ Guardian.name }}
			</p>
			<p class="flex justify-between f4 muted">
				<span>{{ Guardian.email }}</span>
			</p>
		</template>

		<template slot="item" slot-scope="Guardian">
			<p class="b">
				<i class="fa"
					:class='{"fa-male": Guardian.gender == "m", "fa-female": Guardian.gender == "f"}'></i>
				{{ Guardian.name }}
			</p>
			<p class="flex justify-between f4 muted">
				<span>{{ Guardian.email }}</span>
				<span v-if="Guardian.dob">
					<span class="i">dob</span>
					{{ Guardian.dob | formatDate("MM/DD/YYYY") }}
				</span>
			</p>
		</template>

		<template slot="empty-results" slot-scope="searched">
			<p class="i pa tc">Could not find a Parent/Guardian that matches your search.</p>
		</template>
	</SelectFilter>
</template>

<script>
import SelectFilter from '../General/SelectFilter';

export default {
	components: { SelectFilter },

	props: {
		Guardians: {
			type: Array,
			required: true,
		},

		value: {
			type: Number,
			required: false,
			default: null,
		},
	},

	data() {
		return {
			selected_id: this.value,
		}
	},

	computed: {
		searchFields() {
			return ['name'];
		},

		selectedGuardian() {
			return this.Guardians.filter(Guardian => {
				return this.selected_id && Guardian.id == this.selected_id;
			})[0] || null;
		}

	},

	methods: {
		onSelect(id) {
			this.selected_id = id;
			this.$emit('selected', this.selected_id, this.selectedGuardian);
		}
	}
}
</script>