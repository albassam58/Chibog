<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>
	<v-container>
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4">View Store</div>
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="secondary" dark @click="$router.back(-1)" text>
					<v-icon>mdi-arrow-left</v-icon>
					Back
				</v-btn>
			</v-col>
		</v-row>

		<v-row
			v-if="!(Object.keys(store).length === 0 && store.constructor === Object)"
		>
			<v-col
				cols="12"
				sm="6"
				md="4"
			>
				<store-card :store="store" :hasEdit="true"></store-card>
			</v-col>

			<!-- ADD ITEM -->
			<v-col
				cols="12"
				sm="6"
				md="8"
			>
				<div class="text-h5">Add Item</div>
				<v-form
					v-if="items.length < 15"
			    	ref="form"
			    	v-model="valid"
			    	lazy-validation
			  	>
			  		<v-file-input
				    	:rules="imageRules"
				    	accept="image/png, image/jpeg"
				    	placeholder="Pick an image"
				    	prepend-icon="mdi-camera"
				    	label="Image"
				    	v-model="file"
				  	></v-file-input>
			    	<v-text-field
			      		v-model="form.name"
			      		:rules="rules"
			      		label="Name"
			      		required
			    	></v-text-field>

			    	<v-textarea
			      		v-model="form.description"
			      		:rules="rules"
			      		label="Short description about your item"
			      		required
			    	></v-textarea>

			    	<v-select
			      		v-model="form.dish"
			      		:items="dishes"
			      		:rules="rules"
			      		item-value="id"
			      		item-text="name"
			      		label="Type of Dish"
			      		required
			    	></v-select>

			    	<v-text-field
			      		v-model="form.amount"
			      		:rules="rules"
			      		label="Amount"
			      		type="number"
			      		required
			    	></v-text-field>

			    	<v-select
			      		v-model="form.status"
			      		:items="status"
			      		:rules="rules"
			      		item-value="id"
			      		item-text="name"
			      		label="Status"
			      		required
			    	></v-select>

			    	<v-btn
			      		:disabled="!valid"
			      		color="success"
			      		class="my-4"
			      		@click="submit"
			    	>
			      		Submit
			    	</v-btn>
			  	</v-form>

			  	<v-div fill-height v-else>
					<v-layout row wrap align-center>
						<v-row>
							<v-col cols="12"><div class="text-h2 text-center">Whoops</div></v-col>
							<v-col cols="12"><div class="text-h5 text-center text--secondary">Item limit exceeded. You are only allowed to make 15 items per store.</div></v-col>
						</v-row>
					</v-layout>
				</v-div>
			</v-col>
		</v-row>

		<v-simple-table>
			<template v-slot:default>
		      	<thead>
		        	<tr>
		        		<th></th>
		          		<th class="text-left">Name</th>
		          		<th class="text-left">Description</th>
		          		<th class="text-left">Type of Dish</th>
		          		<th class="text-left">Amount</th>
		          		<th class="text-left">Status</th>
		          		<th class="text-center"></th>
		        	</tr>
		      	</thead>
		      	<tbody>
		        	<tr
		          		v-for="item in items"
		          		:key="item.id"
		        	>
		        		<td><img :src="item.image ? `/${ item.image }` : `https://via.placeholder.com/50/FFFFFF/000000?text=${ item.name.charAt(0) }`" height="50" /></td>
		          		<td>{{ item.name }}</td>
		          		<td>{{ item.description }}</td>
		          		<td>{{ item.dishValue.name }}</td>
		          		<td>{{ item.amount.toFixed(2) }}</td>
		          		<td>{{ item.status.name }}</td>
		          		<td class="text-center">
		          			<v-btn
		          				v-if="item.status.id == 4 || item.status.id == 5"
		          				@click="openEditItemDialog(item)"
		          				color="primary"
		          				icon
		          			>
		          				<v-icon>mdi-pencil</v-icon>
		          			</v-btn>
		          		</td>
		        	</tr>
		      	</tbody>
		    </template>
		</v-simple-table>


		<v-dialog
	      	v-model="editItemDialog"
	      	@input="v => v || hideEditItemDialog()"
	      	width="700"
	    >
	      	<v-card>
	        	<v-card-title>
	          		Edit Item
	        	</v-card-title>

	        	<v-card-text class="my-4 text-body-1">
	        		<v-form
				    	ref="editItemForm"
				    	lazy-validation
				  	>
				  		<v-file-input
					    	:rules="editImageRules"
					    	accept="image/png, image/jpeg"
					    	placeholder="Pick an image"
					    	prepend-icon="mdi-camera"
					    	label="Image"
					    	v-model="editFile"
					  	></v-file-input>

				    	<v-text-field
				      		v-model="editForm.name"
				      		:rules="rules"
				      		label="Name"
				      		required
				    	></v-text-field>

				    	<v-textarea
				      		v-model="editForm.description"
				      		:rules="rules"
				      		label="Short description about your item"
				      		required
				    	></v-textarea>

				    	<v-select
				      		v-model="editForm.dish"
				      		:items="dishes"
				      		:rules="rules"
				      		item-value="id"
				      		item-text="name"
				      		label="Type of Dish"
				      		required
				    	></v-select>

				    	<v-text-field
				      		v-model="editForm.amount"
				      		:rules="rules"
				      		label="Amount"
				      		type="number"
				      		required
				    	></v-text-field>

				    	<v-select
				      		v-model="editForm.status"
				      		:items="status"
				      		:rules="rules"
				      		item-value="id"
				      		item-text="name"
				      		label="Status"
				      		required
				    	></v-select>
				  	</v-form>
	        	</v-card-text>

	        	<v-divider></v-divider>

	        	<v-card-actions>
	          		<v-spacer></v-spacer>
	          		<v-btn
	          			color="grey"
	          			text
	          			@click="hideEditItemDialog(); editItemDialog = false"
          			>Cancel</v-btn>
	          		<v-btn
	            		color="primary"
	            		text
	            		:disabled="editItemDialogDisable"
	            		@click="editItem"
	          		>Save</v-btn>
	        	</v-card-actions>
	      	</v-card>
	    </v-dialog>

	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import StoreCard from '@components/StoreCardComponent'

	export default {
		components: {
			StoreCard
		},
		data: () => ({
			valid: true,
			loading: false,
			form: {},
			file: null,
			editForm: {},
			editFile: null,
			editItemDialog: false,
			editItemDialogDisable: false,
			rules: [
      			v => !!v || 'Field is required',
      		],
      		imageRules: [
      			v => !!v || 'Field is required',
	        	v => !v || v.size < 2000000 || 'Image size should be less than 2 MB!',
	      	],
	      	editImageRules: [
	        	v => !v || v.size < 2000000 || 'Image size should be less than 2 MB!',
	      	],
	        meals: [
	        	{ id: 1, name: 'Breakfast' },
	        	{ id: 2, name: 'Lunch' },
	        	{ id: 3, name: 'Snack' },
	        	{ id: 4, name: 'Dinner' },
	        ],
	        dishes: [
	        	{ id: 1, name: 'Main Dish' },
	        	{ id: 2, name: 'Appetizer' },
	        	{ id: 3, name: 'Dessert' },
	        	{ id: 4, name: 'Drinks' },
	        ],
	        status: [
	        	{ id: 4, name: 'Available' },
	        	{ id: 5, name: 'Not Available' }
	        ],
		}),
		async created() {
			let vm = this;
			await vm.find(vm.$route.params.id);
			await vm.fetchCuisines();
			await vm.fetchFoodTypes();

			// set params of fetch items to store_id = params.id
            vm.$store.commit('items/setParams', {
                filters: {
                    store_id: vm.$route.params.id
                }
            });
			await vm.fetchItems();
		},
		computed: {
			...mapState('stores', {
				store: state => state.store
			}),
			...mapState('cuisines', {
				cuisines: state => state.cuisines
			}),
			...mapState('foodTypes', {
				foodTypes: state => state.foodTypes
			}),
			...mapState('items', {
				items: state => state.items.data,
				item: state => state.item
			})
		},
		methods: {
			...mapActions({
				'find': 'stores/find',
				'fetchCuisines': 'cuisines/fetch',
				'fetchFoodTypes': 'foodTypes/fetch',
				'fetchItems': 'items/fetch',
				'saveItem': 'items/save',
				'updateItem': 'items/update'
			}),
			openEditItemDialog(item) {
				let vm = this;
				vm.editForm = {
					id: item.id,
					name: item.name,
					description: item.description,
					dish: item.dish,
					amount: item.amount,
					status: item.status.id
				};
				vm.editItemDialog = true;
			},
			hideEditItemDialog() {
				let vm = this;

				// reset validation
				vm.$refs.editItemForm.reset();
			},
			findMeal(data) {
				let vm = this;
				let meal = _.find(vm.meals, ['id', data.meal]);
				return meal ? meal.name : null;
			},
			async submit() {
				let vm = this;

 				let formData = new FormData();
 				for(let index in vm.form) {
 					formData.append(index, vm.form[index]);
 				}
 				formData.append('store_id', vm.$route.params.id);
 				formData.append("image", vm.file);

				let valid = vm.$refs.form.validate();
        		if (valid) {
					vm.form.store_id = vm.$route.params.id;
					await vm.saveItem(formData);

					vm.$refs.form.resetValidation()
					vm.form = {};
					vm.file = null;
					await vm.fetchItems();
				}
			},
			async editItem() {
				let vm = this;
				vm.editItemDialogDisable = true;

				let formData = new FormData();
 				for(let index in vm.editForm) {
 					formData.append(index, vm.editForm[index]);
 				}
 				formData.append("image", vm.editFile);
 				// method spoofing	
 				formData.append("_method", "PUT");

				let valid = vm.$refs.editItemForm.validate();
        		if (valid) {
        			try {
						await vm.updateItem(formData);
						vm.$refs.editItemForm.resetValidation()
						vm.editForm = {};
						vm.editFile = null;
						await vm.fetchItems();
					} catch (err) {
						console.log(err);
					}
				}
				vm.editItemDialog = false;
				vm.editItemDialogDisable = false;
			}
		}
	}
</script>