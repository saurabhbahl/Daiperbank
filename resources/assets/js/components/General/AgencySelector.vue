<template>
	<SelectFilter
		:items="SortedAgents"
		value-key="id"
		:searchFields="['name']"
		@selected="onChange">

		<template slot="selected-item" slot-scope="Agent">
			<div class="flex justify-start items-center pv3">
				<div
					class="pa3 f2 b ba br2 mr3 tc"
					:class="{}"
					style="width: 65px">
					{{ agencyInitials(Agent) }}
				</div>
				<div>
					<p class="f2 wtl">{{ Agent.name }}</p>
					<p class="muted f4">{{ `${ Agent.id_prefix }-${ Agent.id }` }}</p>
					<p class="muted f4">{{ `${ Agent.city } ${ Agent.state }, ${ Agent.zip }` }}</p>
				</div>
			</div>
		</template>

		<template slot="item" slot-scope="Agent">
			<div class="flex justify-start items-center pv3">
				<div
					class="pa3 f2 b ba br2 mr3 tc"
					:class="{}"
					style="width: 65px">
					{{ agencyInitials(Agent) }}
				</div>
				<div>
					<p class="f2 wtl">{{ Agent.name }}</p>
					<p class="muted f4">{{ `${ Agent.city } ${ Agent.state }, ${ Agent.zip }` }}</p>
				</div>
			</div>
		</template>

		<template slot="default-selection">
			Select an Agency Partner
		</template>

		<template slot="empty-results" slot-scope="{ query }">
			<div class="flex justify-start items-center pv3 clickable">
				<div
					class="pa3 f2 b ba br2 mr3 tc b--black-20"
					style="width: 65px">
					<i class="fa fa-exclamation fa-2x"></i>
				</div>
				<div>
					<p class="f2 wtl">Could not find any Agencies matching '{{ query }}'</p>
				</div>
			</div>
		</template>
	</SelectFilter>
</template>

<script>
import SelectFilter from './SelectFilter.vue';

export default {
	components: { SelectFilter },

	props: {
		agents: {
			required: false,
			type: Array,
			default: () => [],
		}
	},

	data() {
		return {
			SortedAgents: (function(Agents) {
				Agents.sort((a, b) => {
					return a.name < b.name? -1 : 1;
				});
				return Agents;
			})(this.agents),
			selected_agent_id: null,
		}
	},

	methods: {
		agencyInitials(Agency) {
			if (Agency.name.match(/.+\s.+/)) {
				return Agency.name.split(' ', 2).map(function(part) {
					return part.substring(0, 1);
				}).join('').toUpperCase();
			}

			return Agency.name.substring(0, 2).toUpperCase();
		},

		onChange(id) {
			this.selected_agent_id = id;
			this.$emit('change', id);
		}
	}
}
</script>