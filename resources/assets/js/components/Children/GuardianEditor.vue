<template>
<div>
	<div>
		<label v-if=" ! editingGuardian && ! creatingGuardian">
			Choose a Unique Family Identifier <span class="required">*</span>
		</label>
		<GuardianSelect
			v-if=" ! editingGuardian && ! creatingGuardian"
			:guardians="Guardians"
			:value="selectedId"
			@selected="updateSelectedGuardian"
		></GuardianSelect>
		<p v-if="initialErrors && !editingGuardian && initialErrors['Child.guardian_id'][0]" class="validation-error">
			{{ initialErrors['Child.guardian_id'][0] }} 
		</p>

		<label for="guardian_name" v-if="editingGuardian || creatingGuardian">
			Unique Family Identifier <span class="required">*</span>
		</label>
		<input
			v-if="editingGuardian || creatingGuardian"
			type="text" v-model="input.name"
			class="form-control" id="guardian_name"
			@change="updateGuardian"
			@keyup="updateGuardian"
		>
		<!-- {{initialErrors}} -->
		<p v-if="initialErrors && editingGuardian &&  initialErrors['Child.guardian_id'][0]" class="validation-error">
			{{initialErrors['Child.guardian_id'][0] }}
		</p>
		<p v-if="hasError('Guardian.name')" class="validation-error">
			{{ error('Guardian.name') }}
		</p>

	<!-- {{initialErrors}} -->
		<div class="flex justify-between mt3">
			<div class="w-50 pr2">
				<label for="guardian_relationship">Relationship to Child:
					<!-- <span class="required" v-if="editingGuardian">*</span> -->
				</label>
				<select
					id="guardian_relationship"
					v-model="input.relationship"
					class="form-control"
					@change="updateGuardian">
					<option :value="null" disabled>Select one</option>
					<option v-for="(rel, rel_id) in guardianRelationships" :key="'guardian-relationship' + rel_id"
						:value="rel">{{ rel }}</option>
				</select>
				<p v-if="hasError('input.relationship')" class="validation-error">
					{{ error('input.relationship') }}
				</p>
			</div>
			<div class="w-50 pl2">
				<label for="guardian_military_status">Military Status:
					<!-- <span class="required">*</span> -->
				</label>
				<select
					:disabled="!editingGuardian && !creatingGuardian"
					id="guardian_military_status"
					v-model="input.military_status"
					class="form-control"
					@change="updateGuardian">
					<option :value="null" disabled>Select one</option>
					<option v-for="(military, mil_id) in militaryStatuses"
						:key="'military-relationship' + mil_id"
						:value="military">{{ military }}</option>
				</select>
				<p v-if="hasError('input.military_status')" class="validation-error">
					{{ error('input.military_status') }}
				</p>
			</div>
		</div>

		<div class="flex justify-between mv2">
			<p>
				<a href="#"
					class="f4"
					@click="editClicked">
					<span v-if="editingGuardian">Cancel editing</span>
					<span v-if="! editingGuardian">Edit Guardian</span>
				</a>
			</p>

			<p class="tr">
				<button class="btn btn-sm btn-default btn-ghost"
					@click="createClicked">
					<span v-if="!creatingGuardian">Create new Parent</span>
					<span v-if="creatingGuardian">Choose from Existing Parents</span>
				</button>
			</p>
		</div>
	</div>
</div>
</template>

<script type="text/javascript">
import GuardianSelect from './GuardianSelect.vue';

export default {
	components: { GuardianSelect },

	props: {
		initialGuardians: {
			type: Array,
			default: () => [],
			required: true
		},

		selectedId: {
			type: Number,
			default: null,
			required: false
		},

		selectedRelationship: {
			type: String,
			default: null,
			required: false
		},

		selectedMilitaryStatus: {
			type: String,
			default: null,
			required: false
		},

		initialErrors: {
			required: false,
			type: Object,
			default: function() { return {}; },
		},
	},

	data() {
		return {
			editingGuardian: false,
			creatingGuardian: false,

			input: {
				id: null,
				name: null,
				relationship: this.selectedRelationship,
				military_status: this.selectedMilitaryStatus,
			},
		}
	},

	watch: {
		initialErrors(errors) {
			// console.log(errors);
			console.log(errors);
			this.errors = errors;
		}
	},

	computed: {
		Guardians() {
			return this.initialGuardians;
		},

		SelectedGuardian() {
			if (!this.selectedId) {
				return null;
			}

			return this.Guardians.filter(Guardian => {
				return Guardian.id == this.selectedId;
			})[0] || null;
		},

		guardianName() {
			if (!this.editingGuardian && !this.creatingGuardian) {
				if (this.SelectedGuardian) {
					return this.SelectedGuardian.name;
				}

				return null;
			}

			return this.input.name;
		},

		guardianMilitaryStatus() {

			if (this.editingGuardian || this.creatingGuardian) {
				if (this.input.military_status) {
					return this.input.military_status;
				}
			}

			return this.SelectedGuardian? this.SelectedGuardian.military_status : null;
		},

		guardianRelationships() {
			return [
				"No relationship",
				"Mother",
				"Father",
				"Grandmother",
				"Grandfather",
				"Aunt",
				"Uncle",
				"Other family member",
				"Foster parent",
			];
		},

		militaryStatuses() {
			return [
				"Non-military / spouse",
				"Active Military / spouse",
				"Retired / spouse",
				"Reserve / spouse",
				"National Guard / spouse",
			];
		}
	},

	methods: {
		getSelectedGuardian(id) {
			if (!id) {
				return this.Guardians.filter(Guardian => {
					return Guardian.id == id;
				})[0] || {};
			}

			return {};
		},

		updateSelectedGuardian(guardian_id) {
			this.$emit('select', guardian_id);

			if ( ! this.editingGuardian && !this.creatingGuardian) {
				this.input.military_status = this.SelectedGuardian? this.SelectedGuardian.military_status : null;
			}
		},

		updateGuardian() {
			let guardian_id = this.selectedId;

			if (this.creatingGuardian) {
				guardian_id = null;
			}

			this.$emit('update', guardian_id, {
				editing: this.editingGuardian,
				creating: this.creatingGuardian,

				id: guardian_id,
				name: this.guardianName, //this.editingGuardian? this.input.name : this.SelectedGuardian.name,
				relationship: this.input.relationship,
				military_status: this.guardianMilitaryStatus, //this this.input.military_status,
			});
		},

		editClicked(evt) {
			this.toggleEdit();
		},

		toggleEdit(toggle) {
			if (undefined !== toggle) {
				this.editingGuardian = toggle;
			} else {
				this.editingGuardian = !this.editingGuardian;
			}

			if (this.editingGuardian) {
				this.$emit('editGuardian', this.selectedId);

				if (this.SelectedGuardian && this.SelectedGuardian.name) {
					this.input.name = this.SelectedGuardian.name;
					this.input.military_status = this.SelectedGuardian.military_status;
					this.input.id = this.SelectedGuardian.id;
				}
			} else {
				this.$emit('stopEditGuardian', this.selectedId);

				if (this.SelectedGuardian && this.SelectedGuardian.name) {
					this.input.name = this.SelectedGuardian.name;
					this.input.military_status = this.SelectedGuardian.military_status;
					this.input.id = this.SelectedGuardian.id;
					this.input.relationship = this.selectedRelationship;
				}
			}
		},

		createClicked() {
			this.toggleCreate();
		},

		toggleCreate(toggle) {
			if (undefined !== toggle) {
				this.creatingGuardian = toggle;
			} else {
				this.creatingGuardian = !this.creatingGuardian;
			}

			if (this.creatingGuardian) {
				this.toggleEdit(false);
				this.$emit('creatingGuardian');

				this.input.name = null;
				this.input.military_status = null;
				this.input.id = null;
				this.input.relationship = null;
			} else {
				this.$emit('stopCreatingGuardian');

				this.input.name = this.SelectedGuardian.name;
				this.input.military_status = this.SelectedGuardian.military_status;
				this.input.id = this.SelectedGuardian.id;
				this.input.relationship = this.selectedRelationship;
			}
		},
	}
}
</script>