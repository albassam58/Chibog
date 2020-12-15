<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>

	<!-- without v-dialog, container is not centered vertically -->
	<!-- if you know how, feel free to change this -->
	<!-- <v-dialog v-if="loading" v-model="loading" fullscreen full-width :transition="false">
		<v-container fluid fill-height>
		    <v-layout justify-center align-center>
		      	<v-progress-circular
		        	indeterminate
		        	color="primary">
		      	</v-progress-circular>
		    </v-layout>
		</v-container>
	</v-dialog> -->

	<v-container v-if="store || loading">
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4">View Store</div>
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="default" @click="$router.back(-1)" text>
					<v-icon>mdi-arrow-left</v-icon>
					Back
				</v-btn>
			</v-col>
		</v-row>

		<v-row
			v-if="store"
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
					v-if="items.total < limit || itemLoading"
			    	ref="form"
			    	v-model="valid"
			    	lazy-validation
			  	>
				  	<v-row>
			  			<v-col
			  				cols="12"
			  				sm="12"
			  				md="4"
			  				lg="3"
		        			class="flex-grow-0 flex-shrink-0"
		        		>
			  				<v-img
			  					v-if="file"
		                        :src="fileUrl"
		                        width="300"
		                        aspect-ratio="1"
		                    >
		                        <template v-slot:placeholder>
		                            <v-row
		                                class="fill-height ma-0"
		                                align="center"
		                                justify="center"
		                            >
		                                <v-progress-circular
		                                    indeterminate
		                                    color="grey lighten-5"
		                                ></v-progress-circular>
		                            </v-row>
		                        </template>
		                    </v-img>
		                    <v-img
			  					v-else
		                        src="https://via.placeholder.com/150/FFFFFF/000000?text=No Image"
		                        width="150"
		                        aspect-ratio="1"
		                    >
		                        <template v-slot:placeholder>
		                            <v-row
		                                class="fill-height ma-0"
		                                align="center"
		                                justify="center"
		                            >
		                                <v-progress-circular
		                                    indeterminate
		                                    color="grey lighten-5"
		                                ></v-progress-circular>
		                            </v-row>
		                        </template>
		                    </v-img>
			  			</v-col>
			  			<v-col>
			  				<v-row>
			  					<v-col cols="12">
					  				<v-file-input
								    	:rules="imageRules"
								    	show-size
								    	accept="image/png, image/jpeg"
								    	label="Image (JPG, JPEG, TIFF, PNG, WEBP, and BMP) 1MB"
								    	v-model="file"
								    	outlined
							      		hide-details="auto"
								  	></v-file-input>
								</v-col>
								<v-col cols="12">
							    	<v-text-field
							      		v-model="form.name"
							      		:rules="rules"
							      		label="Name"
							      		required
							      		outlined
							      		hide-details="auto"
							    	></v-text-field>
							    </v-col>
							</v-row>
			  			</v-col>
			  		</v-row>

			  		<v-row>
			  			<v-col cols="12">
					    	<v-textarea
					      		v-model="form.description"
					      		:rules="rules"
					      		label="Short description about your item"
					      		required
					      		outlined
							    hide-details="auto"
					    	></v-textarea>
					    </v-col>
					    <v-col cols="12">
					    	<v-select
					      		v-model="form.dish"
					      		:items="dishes"
					      		:rules="rules"
					      		item-value="id"
					      		item-text="name"
					      		label="Type of Dish"
					      		required
					      		outlined
							    hide-details="auto"
					    	></v-select>
					    </v-col>
					    <v-col cols="12">
					    	<v-text-field
					      		v-model="form.amount"
					      		:rules="rules"
					      		label="Amount"
					      		type="number"
					      		required
					      		outlined
							    hide-details="auto"
					    	></v-text-field>
					    </v-col>
					    <v-col cols="12">
					    	<v-select
					      		v-model="form.status"
					      		:items="status"
					      		:rules="rules"
					      		item-value="id"
					      		item-text="name"
					      		label="Status"
					      		required
					      		outlined
							    hide-details="auto"
					    	></v-select>
					    </v-col>
					</v-row>

					<v-divider class="my-4" />

					<v-row>
					    <v-col class="text-right">
					    	<v-btn
					    		rounded
					      		:disabled="disabled"
					      		color="success"
					      		class="px-16 py-6 mb-2"
					      		@click="submit"
					    	>
					      		Submit
					    	</v-btn>
					    </v-col>
					</v-row>
			  	</v-form>

			  	<div fill-height v-else>
					<v-layout row wrap align-center>
						<v-row>
							<v-col cols="12"><div class="text-h2 text-center">Whoops</div></v-col>
							<v-col cols="12"><div class="text-h5 text-center text--secondary">Item limit exceeded. You are only allowed to make 15 items per store.</div></v-col>
						</v-row>
					</v-layout>
				</div>
			</v-col>
		</v-row>

		<custom-data-table
			module="items/fetch"
		    :headers="headers"
		    searchHint="Search by name"
		    :initFilter="filters"
		    :sortBy="sortBy"
      		:sortDesc="sortDesc"
      		@done="itemLoading = false"
      		ref="table"
        >
        	<template v-slot:item="{ item }">
        		<td>
        			<a
	                    :href="imageLink(item)"
	                    target="blank"
	                >
	                    <v-img
	                        :src="imageLink(item)"
	                        aspect-ratio="1"
	                    >
	                        <template v-slot:placeholder>
	                            <v-row
	                                class="fill-height ma-0"
	                                align="center"
	                                justify="center"
	                            >
	                                <v-progress-circular
	                                    indeterminate
	                                    color="grey lighten-5"
	                                ></v-progress-circular>
	                            </v-row>
	                        </template>
	                    </v-img>
	                </a>
        		</td>
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
        	</template>
        </custom-data-table>

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
					  	<v-row>
				  			<v-col
				  				cols="12"
				  				sm="12"
				  				md="4"
				  				lg="3"
			        			class="flex-grow-0 flex-shrink-0"
			        		>
				  				<v-img
				  					v-if="editFile"
			                        :src="editFileUrl"
			                        width="300"
			                        aspect-ratio="1"
			                    >
			                        <template v-slot:placeholder>
			                            <v-row
			                                class="fill-height ma-0"
			                                align="center"
			                                justify="center"
			                            >
			                                <v-progress-circular
			                                    indeterminate
			                                    color="grey lighten-5"
			                                ></v-progress-circular>
			                            </v-row>
			                        </template>
			                    </v-img>
			                    <v-img
				  					v-else
			                        :src="imageLink(editImage)"
			                        width="150"
			                        aspect-ratio="1"
			                    >
			                        <template v-slot:placeholder>
			                            <v-row
			                                class="fill-height ma-0"
			                                align="center"
			                                justify="center"
			                            >
			                                <v-progress-circular
			                                    indeterminate
			                                    color="grey lighten-5"
			                                ></v-progress-circular>
			                            </v-row>
			                        </template>
			                    </v-img>
				  			</v-col>
				  			<v-col>
				  				<v-row>
				  					<v-col cols="12">
						  				<v-file-input
									    	:rules="editImageRules"
									    	show-size
									    	accept="image/png, image/jpeg"
									    	label="Image (JPG, JPEG, TIFF, PNG, WEBP, and BMP) 1MB"
									    	v-model="editFile"
									    	outlined
									    	hide-details="auto"
									  	></v-file-input>
									</v-col>
									<v-col cols="12">
								    	<v-text-field
								      		v-model="editForm.name"
								      		:rules="rules"
								      		label="Name"
								      		required
								      		outlined
									    	hide-details="auto"
								    	></v-text-field>
								    </v-col>
								</v-row>
				  			</v-col>
				  		</v-row>

				  		<v-row>
				  			<v-col cols="12">
						    	<v-textarea
						      		v-model="editForm.description"
						      		:rules="rules"
						      		label="Short description about your item"
						      		required
						      		outlined
									hide-details="auto"
						    	></v-textarea>
						    </v-col>
						    <v-col cols="12">
						    	<v-select
						      		v-model="editForm.dish"
						      		:items="dishes"
						      		:rules="rules"
						      		item-value="id"
						      		item-text="name"
						      		label="Type of Dish"
						      		required
						      		outlined
									hide-details="auto"
						    	></v-select>
						    </v-col>
						    <v-col cols="12">
						    	<v-text-field
						      		v-model="editForm.amount"
						      		:rules="rules"
						      		label="Amount"
						      		type="number"
						      		required
						      		outlined
									hide-details="auto"
						    	></v-text-field>
						    </v-col>
						    <v-col cols="12">
						    	<v-select
						      		v-model="editForm.status"
						      		:items="status"
						      		:rules="rules"
						      		item-value="id"
						      		item-text="name"
						      		label="Status"
						      		required
						      		outlined
									hide-details="auto"
						    	></v-select>
						    </v-col>
						</v-row>
				  	</v-form>
	        	</v-card-text>

	        	<v-divider></v-divider>

	        	<v-card-actions>
	          		<v-spacer></v-spacer>
	          		<v-btn
	          			color="default"
	          			text
	          			@click="hideEditItemDialog(); editItemDialog = false"
          			>Cancel</v-btn>
	          		<v-btn
	            		color="primary"
	            		rounded
	            		:disabled="editItemDialogDisable"
	            		@click="editItem"
	            		class="px-8"
	          		>Save</v-btn>
	        	</v-card-actions>
	      	</v-card>
	    </v-dialog>

	</v-container>

	<v-container v-else>
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4">View Store</div>
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="default" @click="$router.back(-1)" text>
					<v-icon>mdi-arrow-left</v-icon>
					Back
				</v-btn>
			</v-col>
		</v-row>

		<v-layout row wrap align-center>
			<v-row>
				<v-col cols="12"><div class="text-h2 text-center">Whoops</div></v-col>
				<v-col cols="12"><div class="text-h5 text-center text--secondary">The store you were looking for does not exist</div></v-col>
			</v-row>
		</v-layout>
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
			loading: true,
			itemLoading: true,
			limit: 15,
			form: {},
			disabled: false,
			file: null,
			fileUrl: null,
			editForm: {},
			editImage: {},
			editFile: null,
			editFileUrl: null,
			editItemDialog: false,
			editItemDialogDisable: false,
			filters: {},
			headers: [
				{ text: "Picture", value: "image", sortable: false },
				{ text: "Name", value: "name" },
				{ text: "Description", value: "description", sortable: false },
				{ text: "Dish", value: "dish" },
				{ text: "Amount", value: "amount" },
				{ text: "Status", value: "status" },
				{ text: "Action", align: "center", sortable: false }
			],
			sortBy: ['name'],
            sortDesc: [false],
			rules: [
      			v => !!v || 'Field is required',
      		],
      		imageRules: [
      			v => !!v || 'Field is required',
      			v => !v || v.size < 1000000 || 'Image size should be less than 1 MB!',
      			v => {
      				if (v) {
	      				if (!(/\.(jpe?g|tiff?|png|webp|bmp)$/.test(v.name))) {
	      					return 'Image should be a type of JPG, JPEG, TIFF, PNG, WEBP, and BMP'
	      				}
	      			}

	      			return true;
      			}
      		],
	      	editImageRules: [
	        	v => !v || v.size < 1000000 || 'Image size should be less than 1 MB!',
	        	v => {
      				if (v) {
	      				if (!(/\.(jpe?g|tiff?|png|webp|bmp)$/.test(v.name))) {
	      					return 'Image should be a type of JPG, JPEG, TIFF, PNG, WEBP, and BMP'
	      				}
	      			}

	      			return true;
      			}
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

			vm.filters = {
				store_id: vm.$route.params.id
			}

			await vm.find(vm.$route.params.id);
			
			if (!vm.store) return vm.loading = false;

			vm.loading = false;
		},
		computed: {
			...mapState({
				store: state => state.stores.item,
				cuisines: state => state.cuisines.cuisines,
				foodTypes: state => state.foodTypes.foodTypes,
				items: state => state.items.items,
				item: state => state.items.item
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
				// separated so that it's not included in the form when updating
				vm.editImage = {
					image: item.image
				}
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

				try {
					let valid = vm.$refs.form.validate();
	        		if (valid) {
	        			vm.disabled = true;

	        			let formData = new FormData();
		 				for(let index in vm.form) {
		 					formData.append(index, vm.form[index]);
		 				}
		 				formData.append('store_id', vm.$route.params.id);
		 				formData.append("image", vm.file);

						await vm.saveItem(formData);

						vm.$refs.form.resetValidation()
						vm.form = {};
						vm.file = null;
						
						vm.$refs.table.loadItems();

						vm.disabled = false;
					}
				} catch (err) {
					vm.disabled = false;
					vm.$refs.form.resetValidation()
					vm.form = {};
					vm.file = null;
				}
			},
			async editItem() {
				let vm = this;

				let valid = vm.$refs.editItemForm.validate();
        		if (valid) {
					try {
						vm.editItemDialogDisable = true;
				
        				let formData = new FormData();
		 				for(let index in vm.editForm) {
		 					formData.append(index, vm.editForm[index]);
		 				}

		 				if (vm.editFile) {
		 					formData.append("image", vm.editFile);
		 				}

		 				// method spoofing	
		 				formData.append("_method", "PUT");

						await vm.updateItem(formData);
						vm.$refs.editItemForm.resetValidation()
						vm.editForm = {};
						vm.editFile = null;
						vm.$refs.table.loadItems();

						vm.editItemDialog = false;
						vm.editItemDialogDisable = false;
					} catch (err) {
						vm.$refs.editItemForm.resetValidation()
						vm.editForm = {};
						vm.editFile = null;
						vm.editItemDialog = false;
						vm.editItemDialogDisable = false;
					}
				}
			},
			imageLink(item) {
        		let vm = this;
        		let placeholder = "https://via.placeholder.com/300/FFFFFF/000000?text=No Image";

        		return item.image ? `/${ item.image }` : placeholder;
        	}
		},
		watch: {
    		file(val) {
    			if (val)
			      // const file = e.target.files[0];
			      this.fileUrl = URL.createObjectURL(val);
    		},
    		editFile(val) {
    			if (val) {
			      	this.editFileUrl = URL.createObjectURL(val);
			  	}
    		},
    	}
	}
</script>