<template>
	<div>
		<button v-if=" ! addingNote"
			class="btn btn-block btn-default mv3"
			@click="toggleAddNote" type="button">
			<i class="fa fa-plus"></i>
			Add Note
		</button>

		<div v-else
			class="mb3 pb3 bb b--black-20">

			<p v-if="hasError('note')" class="validation-error">
				{{ error('note') }}
			</p>

			<textarea
				rows="3"
				class="form-control mb3"
				v-model="note"
				ref="note-field"
				:readonly="processing"
			></textarea>

			<button class="btn btn-block btn-success mb3"
				:class="{ 'disabled': processing }"
				@click.prevent="saveNote">
				<i class="fa fa-comment-o"></i>
				Save Note
			</button>

			<button class="btn btn-block btn-default"
				@click.prevent="toggleAddNote">
				Cancel
			</button>
		</div>

		<div v-for="Note in Notes"
			:key="`note-${Note.id}`"
			class="pv3 mb3">
			<p class="f4 muted i bg-black-05 pa2">
				Posted on {{ Note.created_at | formatDate("MMM D, YYYY @ h:mma") }}
			</p>

			<nl2br tag="p" :text="Note.note"></nl2br>
		</div>
	</div>
</template>

<script>
import nl2br from 'vue-nl2br';
export default {
	components: { nl2br },

	props: {
		initialNotes: {
			required: true,
			type: Array,
		},

		agency: {
			required: true,
			type: Object,
		},
	},

	data() {
		return {
			Notes: this.initialNotes,
			addingNote: false,
			note: null,
			processing: false,
		}
	},

	computed: {
		Agency() { return this.agency; },
	},

	methods: {
		checkForEscape(evt) {
			if (evt.keyCode == 27) {
				evt.preventDefault();
				this.toggleAddNote();
				return false;
			}
		},

		saveNote() {
			if (this.processing) return;

			this.clearErrors();

			if (!this.note || this.note.trim().length == 0) return;

			this.processing = true;
			axios.post(`/api/agency/${this.Agency.id}/note`, { note: this.note.trim() })
			.then( response => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Note saved successfully",
					});
					this.toggleAddNote();
					this.updateNotes(response.data.data.Notes);
					return;
				}

				this.$toast.error({
					title: "Success",
					message: "Could not save note. " + (response.data.message || "An unexpected error occurred."),
				});
			})
			.catch( err => {
				let response = err.response;

				if (response.status == 422) {
					// validation error
					this.$toast.error({
						title: "Error",
						message: response.data.message || "Please check the form for errors.",
					});

					this.displayErrors(response.data.data.errors || null);
					return;
				}

				this.$toast.error({
					title: "Success",
					message: "Could not save note. " + (response.data.message || "An unexpected error occurred"),
				});
			})
			.then(() => {
				this.processing = false;
			});
		},

		startListeningForEscape() {
			window.addEventListener('keyup', this.checkForEscape);
		},

		stopListeningForEscape() {
			window.removeEventListener('keyup', this.checkForEscape);
		},

		toggleAddNote() {
			this.addingNote = ! this.addingNote;
			this.clearErrors();

			if ( ! this.addingNote) {
				this.note = null;
				this.stopListeningForEscape();
			}
			else {
				this.startListeningForEscape();
				this.$nextTick(() => {
					this.$refs['note-field'].focus();
				});
			}

			return false;
		},

		updateNotes(Notes) {
			this.Notes = Notes;
		},
	}
}
</script>