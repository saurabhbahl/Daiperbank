<template>
	<SelectFilter
		:items="Children"
		value-key="id"
		:searchFields="['name']"
		@selected="onChange"
		@emptyClicked="emptyClicked">

		<template slot="selected-item" slot-scope="Child">
			<div class="flex justify-start items-center pv3">
				<div
					class="pa3 f2 b ba br2 mr3 tc"
					:class="{ [childGenderClass(Child)]: true }"
					style="width: 65px">
					{{ childInitials(Child) }}
				</div>
				<div>
					<p class="f2 wtl">{{ Child.name }}</p>
					<p class="muted f4">DOB {{ Child.dob | formatDate("MM/DD/YYYY") }}</p>
				</div>
			</div>
		</template>

		<template slot="item" slot-scope="Child">
			<div class="flex justify-start items-center pv3">
				<div
					class="pa3 f2 b ba br2 mr3 tc"
					:class="{ [childGenderClass(Child)]: true }"
					style="width: 65px">
					{{ childInitials(Child) }}
				</div>
				<div>
					<p class="f2 wtl">{{ Child.name }}</p>
					<p class="muted f4">DOB {{ Child.dob | formatDate("MM/DD/YYYY") }}</p>
				</div>
			</div>
		</template>

		<template slot="default-selection">
			Choose a Child
		</template>

		<template slot="empty-results" slot-scope="{ query }">
			<div class="flex justify-start items-center pv3 clickable">
				<div
					class="pa3 f2 b ba br2 mr3 tc b--black-20"
					style="width: 65px">
					<i class="fa fa-exclamation fa-2x"></i>
				</div>
				<div>
					<p class="f2 wtl">Could not find a child matching '{{ query }}'</p>
					<p class="muted f4"><a href="/family">Click here to create a new child</a></p>
				</div>
			</div>
		</template>
	</SelectFilter>
</template>

<script>
/**
 * Menu item for creating a new child on the fly
 * Removed because time restraints. 10/3/17 JER
 **
<div class="flex justify-start items-center pv3 clickable">
	<div
		class="pa3 f2 b ba br2 mr3 tc b--black-20"
		style="width: 65px">
		<i class="fa fa-plus fa-2x"></i>
	</div>
	<div>
		<p class="f2 wtl">{{ toProperCase(query) }}</p>
		<p class="muted f4">Create new child</p>
	</div>
</div>
*/
import SelectFilter from './SelectFilter.vue';

export default {
	components: { SelectFilter },

	props: {
		children: {
			required: false,
			type: Array,
			default: [],
		}
	},

	data() {
		return {
			Children: (function(children) {
				children.sort((a, b) => {
					return a.name < b.name? -1 : 1;
				});
				return children;
			})(this.children),
			selected_child_id: null,
		}
	},

	methods: {
		childGenderClass(Child) {
			return Child.gender == 'm'? 'color-male b--light-blue' : 'color-female b--pink';
		},

		childInitials(Child) {
			return Child.name? Child.name.replace(/[^\da-z]+/i, '').substring(0, 2).toUpperCase() : '';
		},

		toProperCase(query) {
			return query.split(' ')
						.map( part => part.trim() )
						.map( part => part.substr(0, 1).toUpperCase() + part.substr(1).toLowerCase() )
						.join(' ');
		},

		onChange(id) {
			this.selected_child_id = id;
			this.$emit('change', id);
		},

		emptyClicked(query) {
			this.$emit('emptyClicked', query);
		}
	}
}
</script>