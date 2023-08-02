<template>
	<div>
		<ul class="nav nav-tabs ph3 pt3">
			<li v-for="(tab, id) in TabItems"
				:key='"tab-" + id'
				:class="tabClasses(id)"
				@click="!tab.link? selectTab($event, id) : true">

				<a :href="getTabLink(tab, id)">
					<span v-html="tab.label"></span>

					<span v-if="hasBadge(tab)"
						class="badge">
						{{ tab.badge }}
					</span>
				</a>
			</li>
		</ul>
	</div>
</template>

<script>
export default {
	props: {
		tabs: {
			type: Object,
			required: true,
		},

		initialActive: {
			type: String,
			required: false,
		},

		activeClasses: {
			type: String,
			required: false,
			default: "",
		},
	},

	data() {
		return {
			current_tab: this.initialActive,
		};
	},

	computed: {
		TabItems() {
			return Object.keys(this.tabs).reduce( (Tabs, tab_id) => {
				let tab = this.tabs[ tab_id ];

				if (typeof tab == 'string') {
					tab = {
						label: tab,
					};
				}

				Tabs[ tab_id ] = tab;
				return Tabs;
			}, {});
		},
	},

	methods: {
		tabClasses(tab_id) {
			let classes = [];

			if (this.isTabActive(tab_id)) {
				classes.push('active');
				classes.push( this.activeClasses );
			}

			if (this.TabItems[ tab_id ].classes) {
				if (typeof this.TabItems[ tab_id ].classes == 'string') {
					classes = classes.concat( this.TabItems[ tab_id ].classes.split(" ") );
				}
				else {
					let prop = null;
					for (prop in this.TabItems[ tab_id ].classes) {
						if (this.TabItems[ tab_id ].classes[ prop ]) {
							classes.push(prop);
						}
					}
				}
			}

			let classDict = classes.reduce( (acc, current) => {
				acc[ current ] = true;
				return acc;
			}, {});

			return classDict;
		},

		getTabLink(Tab, tab_id) {
			if (Tab.link) {
				return Tab.link
			}

			return "#" + tab_id;
		},

		getTabTarget(Tab) {
			if (Tab.link) {
				return "_blank";
			}

			return "top";
		},

		isTabActive(tab_id) {
			return this.current_tab == tab_id;
		},

		selectTab(evt, tab_id) {
			if (this.tabHasClass(tab_id, 'disabled')) {
				evt.preventDefault();
				return false;
			}

			this.current_tab = tab_id;
			this.$emit('selected', tab_id, this.TabItems[ tab_id ]);
		},

		hasBadge(tab) {
			return !! tab.badge;
		},

		tabHasClass(tab_id, classname) {
			let tabClasses = this.tabClasses(tab_id);

			for (let tabClass in tabClasses) {
				if (tabClass == classname && tabClasses[ tabClass ] == true) return true;
				if (tabClass.split(' ').indexOf(classname) >= 0 && tabClasses[ tabClass ] == true) return true;
			}

			return false;
		}
	}
}
</script>