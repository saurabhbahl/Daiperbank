<template>
	<div @click="onClick">
		<div class="flex-auto self-start">
			<span class="f2">
				<i class="fa"
				:class="childClasses"></i>
				{{ Child.name }} 
			</span>

			<table class="table table-condensed ma0">
				<thead v-if="(Child.is_menstruator>0)">
					<tr>						
						<th class="w-15">Type</th>						
					</tr>
				</thead>
				<thead v-else>
					<tr>
						<th class="w-15">Age</th>
						<th class="w-15">Type</th>
						<th class="w-15">Size</th>
						<th class="w-10">Qty</th>
						<th class="w-15">Weight</th>
						
						<th>Potty Training</th>
					</tr>
				</thead>
				<tbody v-if="(Child.is_menstruator>0)">
					<tr>					

						<td v-if="Child.item">{{ Child.item.product.name }}</td>
						<td v-else>Coming Soon</td>
					</tr>
				</tbody>
				<tbody v-else>
					<tr>
						<td>{{ Child.age_str }}</td>

						<td v-if="Child.item">{{ Child.item.product.category.name }}</td>
						<td v-else>TBD</td>

						<td v-if="Child.item">
							<!-- {{ Child.item.product.name }} -->
							{{ Child.item.product.name.replace(/Boy|Girl/g, '') }}
						</td>
						<td v-else>TBD</td>

						<td v-if="Child.item">{{ Child.item.quantity }}</td>
						<td v-else>TBD</td>

						<td>{{ Child.weight_str }}</td>
												
						<td>
							<span v-if="Child.status_potty_train">
								<i class="fa fa-check"
								:class="pottyTrainClasses"></i>
								Yes
							</span>
							<span v-else-if=" ! Child.status_potty_train">
								<i class="fa fa-times"
								:class="pottyTrainClasses"></i> No
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="tc w-15 fg-no pa self-center">
			<slot name="action" :Child="Child"></slot>
		</div>

		<div class="tc self-center fg-no pa">
			<i class="fa fa-chevron-right"></i>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		child: {
			required: true,
			type: Object,
		},

		isEditable: {
			required: false,
			type: Boolean,
			default: true,
		},
	},

	data() {
		return {
		};
	},

	computed: {
		Child() {
			return this.child;
		},

		childClasses() {
			if(this.Child.is_menstruator == 1){
				return 'fa-female purple-female'
			}
			else{
				return {
					'fa-female': this.Child.gender === 'f',
					'fa-male': this.Child.gender === 'm',
				};
			}
		},

		pottyTrainClasses() {
			return {
				'dark-red': ! this.Child.status_potty_train,
				'dark-green': !! this.Child.status_potty_train,
			};
		}
	},

	methods: {
		onClick(evt) {
			this.$emit('click', this.Child);
		},
	},
}
</script>