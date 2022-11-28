<template>
	<div class="pa">
		<div>
			<div class="tr mb">
				<button v-if=" ! adding_note"
					class="btn btn-default"
					@click="toggleAddNote">
					<i class="fa fa-plus"></i>
					Add comment
				</button>
			</div>

			<div v-if="adding_note"
				class="pb3 bb b--black-20 mb5 bw2">
				<div class="b bg-black-025 pa3 f3">
					Create new comment
				</div>

				<div class="mv3">
					<p v-if="hasError('note')" class="validation-error">
						{{ error('note') }}
					</p>
					<textarea
						ref="note-input"
						v-model="new_note"
						class="form-control"
						rows="5"
					></textarea>
				</div>

				<div class="tr">
					<label for="flag_share" class="dib mr3" v-if="isAdmin">
						<input type="checkbox" v-model="flag_share" id="flag_share" :value="true">
						Share with ordering agent?
					</label>

					<button class="btn btn-default btn-ghost mr3"
						:class="{'disabled': processing}"
						@click="toggleAddNote">
						<i class="fa fa-times"></i>
						Cancel
					</button>

					<button class="btn btn-success"
						:class="{'disabled': processing}"
						@click="save">
						<i class="fa fa-download"></i>
						Save
					</button>
				</div>
			</div>
		</div>

		<div v-for="Note in Notes"
			v-if="shouldShowNote(Note)"
			class="pb3 bb b--black-20 mb3">

			<div class="bg-black-025 pa3 f3 wtl clearfix">
				{{ Note.created_at | formatDate("MM/DD/YYYY @ h:mma", "YYYY-MM-DD HH:mm:ss") }}
				by
				{{ Note.author.name }}

				<div class="pull-right tr">
					<span v-if="Note.flag_share && isAdmin"
						class="f4 i dib mr3">
						Shared with {{ order.agency.name }}
					</span>

					<a href="#" v-if="isAdmin" @click.prevent="deleteNote(Note)"
						class="a-plain muted"
						title="Delete comment">
						<i class="fa fa-times"></i>
					</a>
				</div>
			</div>

			<div class="pa3">
				<nl2br tag="p" :text="Note.note"></nl2br>
			</div>
		</div>
	</div>
</template>

<script>
import nl2br from 'vue-nl2br';

export default {
	components: { nl2br },

	props: {
		order: {
			required: true,
			type: Object,
		},

		isAdmin: {
			type: Boolean,
			required: false,
			default: false,
		}
	},

	data() {
		return {
			adding_note: false,
			new_note: null,
			flag_share: this.getDefaultFlagShare(),
			processing: false,
		};
	},

	computed: {
		Notes() {
			return this.order.note || [];
		}
	},

	methods: {
		getDefaultFlagShare() {
			if (this.isAdmin) return false;

			// an agent's comments are always shared
			return true;
		},

		toggleAddNote() {
			this.adding_note = ! this.adding_note;

			if (this.adding_note) {
				this.$nextTick( () => this.$refs['note-input'].focus());
			}
		},

		save() {
			if (this.processing) return;
			this.processing = true;

			this.clearErrors();

			axios.post('/api/orders/' + this.order.id + '/notes', {
				note: this.new_note,
				flag_share: this.flag_share,
			}).then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Comment saved successfully.",
					});
					this.noteCreated(response.data.data.Note, response.data.data.Notes);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not save comment. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.catch( err => {
				let response = err.response;

				if (response.status == 422) {
					this.displayErrors(response.data.data.errors || null);
				}

				this.$toast.error({
					title: "Error",
					message: "Could not save comment. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		deleteNote(Note) {
			axios.delete('/api/orders/' + this.order.id + '/notes/' + Note.id)
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Comment deleted",
					});
					this.noteDeleted(response.data.data.Notes);
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not delete comment. " + (response.data.message || ""),
				});
			})
			.catch( err => {
				this.$toast.error({
					title: "Error",
					message: "Could not delete comment. " + (response.data.message || "An unexpected error occurred"),
				});
			});
		},

		noteCreated(Note, AllNotes) {
			this.$emit('create', Note, AllNotes);

			this.clearInput();
		},

		noteDeleted(AllNotes) {
			this.$emit('delete', AllNotes);
		},

		clearInput() {
			this.new_note = null;
			this.flag_share = this.getDefaultFlagShare();
		},

		shouldShowNote(Note) {
			if (Note.flag_share) return true;
			return this.isAdmin;
		}
	}
}
</script>