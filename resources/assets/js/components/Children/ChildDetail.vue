<template>
	<div v-show="child_id || creating_child" @keyup.escape="close">
		<div class="pa oy-auto" v-if="Child">
			<div class="mb">
				<p v-if=" ! editing"
					class="b f2">
					{{ Child.name }}
				</p>
				<span v-else class="">
					<label for="name" class="f3">Unique Child Identifier:
						<span class="required">*</span>
					</label>
					<input type="text" v-model="editedChild.name" class="form-control wtn" id="name">
				</span>

				<p v-if="hasError('Child.name')" class="validation-error">
					{{ error('Child.name') }}
				</p>
			</div>
			<div class="flex justify-between">
				<div class="flex-auto w-50 mr3">
					<div class="mb">
						<table class="table mb0">
							<tr>
								<th scope="row" class="w-25">
									<label class="b">Gender:
										<!-- <span class="required" v-if="editing">*</span> -->
									</label>
								</th>
								<td>
									<span v-if=" ! editing">
										<i class="fa" :class="{ 'fa-female': Child.gender === 'f', 'fa-male': Child.gender === 'm'}"></i>
										<span v-if="Child.gender === 'f'">Female</span>
										<span v-else-if="Child.gender === 'm'">Male</span>
									</span>
									<span v-else>
										<label for="gender_male">
											<input type="radio" v-model="editedChild.gender" value="m" id="gender_male">
											<i class="fa fa-male"></i> Male
										</label>
										<label for="gender_female" class="ml">
											<input type="radio" v-model="editedChild.gender" value="f" id="gender_female">
											<i class="fa fa-female"></i> Female
										</label>
									</span>
								</td>
							</tr>
						</table>
						<p v-if="hasError('Child.gender')" class="validation-error">
							{{ error('Child.gender') }}
						</p>
					</div>

					<div class="mb">
						<table class="table mb0">
							<tr>
								<th scope="row" class="w-25">
									<label for="dob" class="b">DOB:
										<!-- <span class="required" v-if="editing">*</span> -->
									</label>
								</th>
								<td>
									<p v-if=" ! editing && Child.dob">{{ Child.dob | formatDate("M/D/YYYY") }} ({{ Child.age_str }})</p>
									<input v-else type="date" v-model="editedChild.dob" id="dob" class="form-control">
								</td>
							</tr>
						</table>
						<p v-if="hasError('Child.dob')" class="validation-error">
							{{ error('Child.dob') }}
						</p>
					</div>

					<div class="mb">
						<table class="table mb0">
							<tr>
								<th scope="row" class="w-25">
									<label for="zip">Zip:
										<!-- <span class="required" v-if="editing">*</span> -->
									</label>
								</th>
								<td>
									<span v-if=" ! editing">{{ Child.zip }}</span>
									<span v-else
										class="">
										<input type="tel" v-model="editedChild.zip" id="zip" class="form-control">
									</span>
								</td>
							</tr>
						</table>
						<p v-if="hasError('Child.zip')" class="validation-error">
							{{ error('Child.zip') }}
						</p>
					</div>
				</div>

				<div class="flex-auto w-50">
					<div class="mb">
						<table class="table mb0">
							<tr>
								<th scope="row" class="w-25">
									<label for="race">Race:
										<!-- <span class="required" v-if="editing">*</span> -->
									</label>
								</th>
								<td>
									<span v-if=" ! editing">{{ Child.race }}</span>
									<span v-else>
										<select v-model="editedChild.race" id="race" class="form-control">
											<option :value="null" disabled>Select one</option>
											<option v-for="race in races"
												:value="race">
												{{ race }}
											</option>
										</select>
									</span>
								</td>
							</tr>
						</table>
						<p v-if="hasError('Child.race')" class="validation-error">
							{{ error('Child.race') }}
						</p>
					</div>

					<div class="mb">
						<table class="table mb0">
							<tr>
								<th scope="row" class="w-25">
									<label for="race">Ethnicity:
										<!-- <span class="required" v-if="editing">*</span> -->
									</label>
								</th>
								<td>
									<span v-if=" ! editing">{{ Child.ethnicity }}</span>
									<span v-else>
										<select v-model="editedChild.ethnicity" id="ethnicity" class="form-control">
											<option :value="null" disabled>Select one</option>
											<option v-for="eth in ethnicities"
												:value="eth">
												{{ eth }}
											</option>
										</select>
									</span>
								</td>
							</tr>
						</table>
						<p v-if="hasError('Child.ethnicity')" class="validation-error">
							{{ error('Child.ethnicity') }}
						</p>
					</div>

					<div class="mb">
						<table class="table mb0">
							<tr>
								<th scope="row">
									<label for="potty_train_yes">Currently potty training?</label>
								</th>
								<td class="tl w-33">
									<span v-if=" ! editing">{{ Child.status_potty_train? "Yes" : "No" }}</span>
									<input v-else type="checkbox" v-model="editedChild.status_potty_train" id="potty_train_yes" value="1">
								</td>
							</tr>
						</table>
						<p v-if="hasError('Child.status_potty_train')" class="validation-error">
							{{ error('Child.status_potty_train') }}
						</p>
					</div>

					<div class="mb">
						<table class="table mb0">
							<tr>
								<th scope="row">
									<label for="status_wic_yes">Currently receiving WIC?</label>
								</th>
								<td class="tl w-33">
									<span v-if=" ! editing">{{ Child.status_wic? "Yes" : "No" }}</span>
									<input v-else type="checkbox" v-model="editedChild.status_wic" id="status_wic_yes" value="1">
								</td>
							</tr>
						</table>
						<p v-if="hasError('Child.status_wic')" class="validation-error">
							{{ error('Child.status_wic') }}
						</p>
					</div>
				</div>
			</div>

			<p class="b f2 bb bw2 b--black-20 mv3">Parent/Guardian
				<span class="required" v-if="editing">*</span>
			</p>
			<div v-if=" ! editing"
				class="flex justify-between items-start">
				<p class="flex-auto">
					<span class="b">{{ Child.guardian.name }}</span>
					<br>
					<span class="muted f4">Relationship: {{ Child.guardian_relationship }}</span>
				</p>

				<p class="flex-auto tr">
					<span class="b">Military Status:</span>
					{{ Child.guardian.military_status }}
				</p>
			</div>
			<p v-if="errors" class="validation-error">
		</p>
			<GuardianEditor v-if="editing"
				:initialGuardians="Guardians"
				:selectedId="editedChild.guardian_id || Child.guardian_id"
				:selectedRelationship="editedChild.guardian_relationship || Child.guardian_relationship"
				:selectedMilitaryStatus="guardianMilitaryStatus(editedChild.guardian_id || (Child.guardian? Child.guardian.id : null) || null)"
				:initialErrors="errors"
				@select="selectGuardian"
				@update="updateGuardian"
				@stopEditingGuardian="stopEditingGuardian"
				@stopCreatingGuardian="stopCreatingGuardian"
			></GuardianEditor>

			<div v-if=" ! editing">
				<p v-if="child_id"
					class="b f2 bb bw2 b--black-20 mv3">
					Lifetime Benefits
				</p>
				<div v-if="child_id"
					class="flex justify-around mb3">
					<div class="flex-auto tc">
						<span class="f2">{{ BenefitSummary.diapers }}</span>
						<br>
						Diapers
					</div>

					<div class="flex-auto tc">
						<span class="f2">{{ BenefitSummary.pullups }}</span>
						<br>
						Pull-ups
					</div>
				</div>

				<div v-if="child_id"
					class="b f2 bb bw2 b--black-20 mv3">Siblings</div>
				<div v-if="child_id && Child.sibling.length > 0">
					<div v-for="(Sibling, sib_id) in Child.sibling" :key="'sibling-' + sib_id"
						class="flex justify-between">
						<p>
							<i class="fa" :class="{'fa-female': Sibling.gender === 'f', 'fa-male': Sibling.gender === 'm' }"></i>
							{{ Sibling.name }}
						</p>

						<div class="tr">
							<button @click="viewChild(Sibling.id)"
								class="btn btn-xs btn-default btn-ghost">
								<i class="fa fa-eye"></i>
								View
							</button>
						</div>
					</div>
				</div>
				<div v-else-if="child_id">
					<p class="tc i muted mt">This child doesn't have any siblings.</p>
				</div>

				<p v-if="child_id"
					class="b f2 bb bw2 b--black-20 mv3">
					Orders
					<span v-if="activeOrderItems.length > 0">
						<span class="badge badge-default">{{ activeOrderItems.length }}</span>
					</span>
				</p>
				<div v-if="child_id && activeOrderItems.length">
					<div
						v-for="(Order, order_idx) in activeOrderItems" :key="'order-' + order_idx"
						class="flex bb b--black-20 pv2"
						:ref="Order.id">

						<p class="w-33 b">#{{ Order.order.full_id }}</p>
						<p class="w-33">
							{{ Order.quantity }}
							{{ Order.product.category.name }}{{ Order.quantity != 1? 's' : '' }}
							({{ Order.product.name }})
						</p>
						<p class="w-15 fs">
							<span class="label" :class="orderStatusBadgeClass(Order.order.order_status)">
								{{ orderStatusString(Order.order.order_status) }}
							</span>
						</p>
						<div v-if="Order.order.updated_at">
							<p class="fs-no fg tr" v-if="Order.order.order_status == 'fulfilled'">
								{{ Order.order.updated_at | formatDate("MMM D, YYYY") }}
							</p>
							<p class="fs-no fg tr" v-else>
								{{ Order.order.created_at | formatDate("MMM D, YYYY") }}
							</p>
						</div>
					</div>
				</div>
				<div v-else-if="child_id">
					<p class="tc i muted mt">This child has received any diapers yet.</p>
				</div>
			</div>
		</div>

		<!-- Putting this here because IE11 does not remove position:absolute items from the flexbox layout flow
			documentation:
				- https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/12500543/
				- https://stackoverflow.com/questions/32991051/absolutely-positioned-flex-item-is-not-removed-from-the-normal-flow-in-ie11
		-->
		<a
			href="#"
			class="pxa pinr pint a-plain pa"
			@click="close">

			<i class="fa fa-times"></i>
		</a>

		<div class="pa bt bw2 b--black-20 bg-washed-blue" v-if="Child">
			<!-- Actions available while not editing -->
			<div v-if=" ! editing">
				<button
					class="btn btn-block btn-ghost-border bg-white btn-default mb"
					@click="addToOrderClicked">
					<i class="fa fa-plus"></i>
					Add to Order
				</button>

				<button
					class="btn btn-block btn-primary"
					@click="toggleEditing()">
					<i class="fa fa-pencil"></i>
					Edit
				</button>
			</div>

			<!-- Buttons available while editing -->
			<div v-if="editing">
				<button
					v-if="child_id"
					class="btn btn-block btn-alt bg-white btn-danger mt1 mb4"
					@click="archiveChild">
					<i class="fa fa-trash-o"></i>
					Inactive
				</button>

				<button
					class="btn btn-block btn-success mb"
					@click="saveChild">
					<i class="fa fa-download"></i>
					Save
				</button>

				<button
					class="btn btn-block btn-ghost-border bg-white btn-default"
					@click="child_id? toggleEditing() : close()">
					<i class="fa fa-times"></i>
					Cancel
				</button>
			</div>
		</div>

		<div v-if="!Child" class="tc pt5">
			<p><i class="fa fa-spin fa-spinner fa-4x muted"></i></p>
			<p class="b f1 muted wtl mt">Loading...</p>
		</div>
	</div>
</template>

<script>
import moment from 'moment';
import AddChildOrderSelectionModal from '../Modals/AddChildOrderSelection.vue';
import GuardianEditor from './GuardianEditor.vue';

export default {
	components: { GuardianEditor },

	props: {
		orders: {
			// required: true,
			type: Array,
		},
	},

	data() {
		return {
			creating_child: false,
			editing: false,
			createNewGuardian: false,
			child_id: null,
			Child: null,
			BenefitSummary: null,
			Guardians: [],
			editedChild: {},
			editableChildFields: [
				'name',
				'zip',
				'dob',
				'gender',
				'race',
				'ethnicity',
				'status_potty_train',
				'status_wic',
				'guardian_id',
				'guardian_relationship',
			],
			newGuardian: {
				name: null,
				military_status: null,
				relationship: null,
			},
			races: [
				'American Indian or Alaska Native',
				'Asian',
				'Black or African American',
				'Native Hawaiian or Pacific Islander',
				'White',
				'Two or More Races',
				'Other',
			],
			ethnicities: [
				'Hispanic or Latino',
				'Non-Hispanic or Latino',
			],
			guardianRelationships: [
				"Mother",
				"Father",
				"Grandmother",
				"Grandfather",
				"Aunt",
				"Uncle",
				"Other family member",
				"Foster parent",
			],
			militaryStatuses: [
				"Non-military / spouse",
				"Active Military / spouse",
				"Retired / spouse",
				"Reserve / spouse",
				"National Guard / spouse",
			]
		};
	},

	computed: {
		resourceUrl() {
			if (this.child_id) {
				return '/api/child/' + this.child_id;
			}
			else {
				return '/api/child';
			}
		},

		activeOrderItems() {
			return this.Child.order_item.filter( Item => {
				return ['pending_approval', 'pending_pickup', 'fulfilled', ].indexOf(Item.order.order_status) >= 0;
			});
		},

		guardianOptions() {
			return this.Guardians.map( (Guardian) => {
				Guardian.value = Guardian.id;
				return Guardian;
			});
		}
	},

	methods: {
		orderStatusBadgeClass(status) {
			switch (status) {
				case 'draft':
				case 'pending_approval':
					return 'label-info';

				case 'pending_pickup':
				case 'fulfilled':
					return 'label-success';

				case 'cancelled':
					return 'label-danger';

				default:
					return 'label-default';
			}
		},

		orderStatusString(status) {
			switch (status) {
				case 'draft':
					return 'Draft';

				case 'pending_approval':
					return 'Pending';

				case 'pending_pickup':
					return 'Approved';

				case 'fulfilled':
					return 'Fulfilled';

				case 'cancelled':
					return 'Cancelled';

				default:
					return 'Unknown';
			}
		},

		viewChild(child_id) {
			this.creating_child = false;
			this.child_id = child_id;
			this.Child = null;
			this.loadChild();
			this.loadGuardians();
		},

		createChild() {
			this.Child = {};
			this.clearEditableData();
			this.creating_child = true;
			this.editing = true;
			this.loadGuardians();
		},

		loadChild() {
			this.editing = false;
			this.createNewGuardian = false;
			this.clearNewGuardianData();

			axios.get('/api/child/' + this.child_id)
			.then( (response) => {
				this.childLoaded(response.data.data);
			})
			.catch(function(err) {
				this.$toast.warning({
					title: "Warning",
					message: "Could not load all of this child's info. Please try again. If problem persists, please refresh the page.",
				});
			});
		},

		loadGuardians() {
			axios.get('/api/guardian')
			.then( (response)  => {
				if (response.data.success) {
					this.Guardians = response.data.data.Guardians;
				}
				else {
					this.$toast.warning({
						title: "Warning",
						message: "Could not load all of this child's info. Please try again. If problem persists, please refresh the page.",
					});
				}
			})
			.catch((err) => {
				this.$toast.warning({
					title: "Warning",
					message: "Could not load all of this child's info. Please try again. If problem persists, please refresh the page.",
				});
			});
		},

		guardianMilitaryStatus(guardian_id) {
			if (guardian_id) {
				return null;
			}

			let Guardian = this.Guardians.filter(Guard => {
				return Guard.id == guardian_id;
			});

			if (Guardian.length) {
				Guardian = Guardian[0];
			} else {
				return null;
			}

			return Guardian.military_status;
		},

		saveChild() {
			this.clearErrors();
			if(this.editedChild.dob=='Invalid date'){
				this.editedChild.dob=null;
			}
			// console.log(this.editedChild.dob+' test');
			let postData = {
				Child: this.editedChild,
			};

			if (this.editingGuardian || this.creatingGuardian) {
				postData.update_guardian = true;
				postData.Guardian = this.newGuardian;
			}
			console.log(postData.Guardian, 'postData.Guardian');
			axios.post(this.resourceUrl, postData)
			.then( (response) => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child has been added successfully",
					});

					this.childLoaded(response.data.data);
					this.$emit('save', this.Child);
					this.toggleEditing();
					this.creating_child = !this.creating_child;
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not added child, an unexpected error occurred.",
				});
			})
			.catch(error => {
				let response = error.response;
				if (response.status == 422) {
					this.$toast.error({
						title: "Error",
						message: response.data.message,
					});
					this.displayErrors(response.data.data.errors || {});
					return;
				}

				this.$toast.error({
					title: "Error",
					message: "Could not added child, an unexpected error occurred.",
				});
			});
		},

		addToOrderClicked() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: AddChildOrderSelectionModal,
				props: {
					Orders: this.orders,
				}
			});

			this.$root.$once('child/add-to-order', this.addToOrder);
			this.$root.$once('child/add-to-order/cancel', () => {
				this.$root.$off('child/add-to-order', this.addToOrder);
			});
		},

		addToOrder(order_id, action) {
			this.doAddChildToOrder(order_id, this.child_id)
			.then(status => {
				if (status.success) {
					this.$toast.success({
						title: "Success",
						message: `Child added to order successfully!`,
					});

					if (action == 'finished') { // user is finished adding children to the order, so we take them to the order
						window.top.location = `/order/${status.order_id}/create`;
						return;
					}
				}
				else {
					this.$toast.error({
						title: "Error",
						message: status.message,
					});

					if (status.errors) {
						this.displayErrors( status.errors );
					}
				}
			});
		},

		doAddChildToOrder(order_id, child_id) {
			if ( ! order_id) {
				return axios.post(`/api/orders/create`)
				.then(response => {
					if (response.data.success) {
						return this.doAddChildToOrder(response.data.data.Order.id, child_id);
					}

					return {
						success: false,
						message: "Could not create order, an unexpected error occurred.",
					};
				})
				.catch(error => {
					let response = error.response;
					if (response.status == 422) {
						// console.log('testtt---'+response.data.data.errors);
						return {
							success: false,
							message: response.data.message,
							errors: response.data.data.errors,
						};
					}

					return {
						success: false,
						message: "Could not create order, an unexpected error occurred.",
					};
				});
			}

			return axios.post(`/api/orders/${order_id}/child`, { child_id })
			.then(response => {
				if (response.data.success) {
					return {
						success: true,
						order_id: response.data.data.Order.id,
					};
				}

				return {
					success: false,
					message: "Could not add child to order.",
				}
			})
			.catch(err => {
				return {
					success: false,
					message: "Could not add child, an unexpected error occurred.",
				};
			});
		},

		archiveChild() {
			this.clearErrors();

			axios.delete(this.resourceUrl)
			.then( (response) => {
				if (response.data.success) {
					this.$toast.success({
						title: "Success",
						message: "Child has been Inactive.",
					});
					this.$emit('delete', this.Child);
					this.close();
					return;
				}

				this.$toast.success({
					title: "Error",
					message: "Could not active child.",
				});
			})
			.catch( (err) => {
				this.$toast.success({
					title: "Error",
					message: "Could not active child, an unexpected error occurred.",
				});
			});
		},

		close() {
			const old_child_id = this.child_id;
			this.creating_child = false;
			this.editing = false;
			this.child_id = null;
			this.Child = null;
			this.clearEditableData();
			this.clearNewGuardianData();
			this.clearErrors();
			this.createNewGuardian = false;
			this.$emit('closed', old_child_id);
		},

		childLoaded(data) {
			this.child_id = data.Child.id;
			this.Child = data.Child;
			this.BenefitSummary = data.BenefitSummary;
			this.clearEditableData();
			this.clearNewGuardianData();
			this.clearErrors();

			let editedChild = this.editableChildFields.reduce( (carry, field) => {
				carry[ field ] = this.Child[ field ] !== undefined? this.Child[ field ] : null;
				return carry;
			}, {});

			editedChild.weight_lb = Math.floor( this.Child.weight / 16 );
			editedChild.weight_oz = this.Child.weight % 16;
			// console.log(editedChild.dob);
			editedChild.dob = moment(this.Child.dob).format('YYYY-MM-DD');

			this.$set(this, 'editedChild', editedChild);

			this.$emit('loaded', this.Child.id);
		},

		createGuardian() {
			this.createNewGuardian = true;
		},

		selectGuardian(id) {
			this.editedChild.guardian_id = id;
		},

		stopEditingGuardian() {
			this.editingGuardian = false;
			this.newGuardian = {};
		},

		stopCreatingGuardian() {
			this.creatingGuardian = false;
			this.newGuardian = {};
		},

		updateGuardian(guardian_id, Guardian) {
			// console.log('Guardian=',Guardian);
			// return;
			this.editedChild.guardian_relationship = Guardian.relationship;
			this.editingGuardian = Guardian.editing;
			this.creatingGuardian = Guardian.creating;

			if (Guardian.editing || Guardian.creating) {
				this.newGuardian = {
					id: Guardian.id,
					name: Guardian.name,
					relationship: Guardian.relationship,
					military_status: Guardian.military_status,
				};
			}
			// console.log('this.newGuardian==',this.newGuardian);
		},

		clearEditableData() {
			this.editedChild = this.editableChildFields.reduce( (carry, field) => {
				carry[ field ] = null;
				return carry;
			}, {});
		},

		clearNewGuardianData() {
			Object.keys(this.newGuardian).map( key => {
				this.newGuardian[ key ] = null;
			});
		},

		toggleEditing() {
			this.editing = ! this.editing;
			this.clearErrors();
		},
	},

	mounted() {
		this.$on('view', this.viewChild);
		this.$on('create', this.createChild);
		this.$on('hide', this.close);
	},

	beforeDestroy() {
		this.$off('view');
		this.$off('create');
		this.$off('hide');
	}
}
</script>