<template>
	<ChildItem
		:child="Child"
		:is-editable="isEditable"
		@click="onClick">
		<template slot="action">
			<div v-if="isEditable">
				<button class="btn btn-success"
					:class="{'disabled': processing}"
					v-if=" ! approved && ! removed"
					@click.stop="onApproveClicked">
					<i class="fa fa-check"></i>
					Approve
				</button>

				<button class="btn btn-default"
					:class="{'disabled': processing}"
					v-if="approved && ! removed"
					@click.stop="onUnApproveClicked">
					<i class="fa fa-times"></i>
					Move to Pending
				</button>

				<button class="btn btn-default"
					:class="{'disabled': processing}"
					v-if="removed"
					@click.stop="onRestoreClicked">
					<i class="fa fa-check"></i>
					Restore
				</button>
			</div>
			<div v-else>
				<p :class="statusClasses">
					{{ readableStatus }}
				</p>

				<p class="muted f4">
					Status
				</p>
			</div>
		</template>
	</ChildItem>
</template>

<script>
import ChildItem from '../ChildItem.vue';
import sharedChildItemBehaviors from '../sharedChildItemBehaviors';

export default {
	components: { ChildItem },
	mixins: [ sharedChildItemBehaviors ],

	computed: {
		approved() {
			return !! (this.Child.item && this.Child.item.flag_approved);
		},

		removed() {
			return !! (this.Child.item && this.Child.item.deleted_at);
		},

		statusClasses() {
			return {
				'bootstrap-info-blue': ! this.approved && ! this.removed,
				'dark-green': this.approved && ! this.removed,
				'dark-red': this.removed,
			};
		},

		readableStatus() {
			if (this.Child.item) {
				if ( this.Child.item.deleted_at) {
					return "Removed";
				}
				else if (this.Child.item.flag_approved) {
					return "Approved";
				}
				else {
					return "Pending Review";
				}
			} else {
				return "Draft"
			}
		}
	},

	methods: {
		onApproveClicked(evt) {
			this.$emit('approve', this.Child);
		},

		onUnApproveClicked() {
			this.$emit('unapprove', this.Child);
		},

		onRestoreClicked() {
			this.$emit('restore', this.Child);
		}
	}
}
</script>