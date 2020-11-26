<template>
	<div>
		<v-container>
			<!-- 
				FILTER BY:
					CITY
					ITEM
					STATUS

				TOTAL QUANTITY PER ITEM
			-->
			<v-row>
				<v-col cols="6" class="d-flex flex-row">
					<div class="text-h4 mb-4">Orders</div>
				</v-col>
				<v-col cols="6" class="d-flex flex-row-reverse">
					<v-btn color="secondary" dark @click="$router.back(-1)" text>
						<v-icon>mdi-arrow-left</v-icon>
						Back
					</v-btn>
				</v-col>
			</v-row>
			<v-row>
				<v-col cols="12" sm="12" md="4">
					<v-text-field	
			            v-model="search"
			            label="Search"
			            hint="Search transaction, name, mobile number, and email"
			            persistent-hint
			            :loading="searchLoading"
			        ></v-text-field>
				</v-col>
				<v-col cols="12" sm="12" md="4">
					<v-select
			            v-model="selectedStatus"
			            :items="status"
			            item-value="id"
			            item-text="name"
			            label="Filter by Status"
			            multiple
			        ></v-select>
				</v-col>
			</v-row>
			<div class="text-subtitle-1 grey--text mb-2">
				Result: {{ orders ? orders.length : 0 }}
			</div>
			<v-row wrap>
				<v-col
					cols="12"
					sm="12"
					md="6"
					lg="4"
					v-for="(order, index) in orders"
					:key="index"
				>
					<v-card>
						<v-card-title>
							{{ order.transaction_id }}
							<v-spacer />
							<v-chip
								:color="order.status.color"
								dark
							>
								{{ order.status.name }}
							</v-chip>
						</v-card-title>
						<v-divider />
						<v-card-text>
							<v-row
				          		v-for="(item, index) in order.item_name.split(',')"
				          		:key="item[index]"
				          		class=""
				        	>
				          		<v-col cols="6" class="py-0">
				          			<span class="text-subtitle-2">
				          				{{ Math.round(order.quantity.split(',')[index]) }}x&nbsp;
				          			</span>
				          			{{ item }}
				          		</v-col>
				          		<v-col cols="3" class="py-0 text-right">
				          			{{ computeUnitTotal(order, index) }}
				          		</v-col>
				          		<v-col cols="3" class="py-0 text-right">
				          			<v-tooltip bottom>
								      	<template v-slot:activator="{ on, attrs }">
						          			<v-btn
						          				icon
						          				color="primary"
						          				x-small
						          				v-bind="attrs"
          										v-on="on"
						          				@click="openEditDialog(order, index)"
						          			>
						          				<v-icon>mdi-pencil</v-icon>
						          			</v-btn>
						          		</template>
						          		<span>Edit Quantity</span>
						          	</v-tooltip>
						          	<v-tooltip bottom v-if="order.item_name.split(',').length > 1">
								      	<template v-slot:activator="{ on, attrs }">
						          			<v-btn
						          				icon
						          				color="error"
						          				x-small
						          				v-bind="attrs"
          										v-on="on"
						          				@click="openDeleteDialog(order.order_id.split(',')[index])"
						          			>
						          				<v-icon>mdi-trash-can</v-icon>
						          			</v-btn>
						          		</template>
						          		<span>Remove Item</span>
						          	</v-tooltip>
				          		</v-col>
			        		</v-row>
			        		<v-row>
			        			<v-col cols="12" class="text-center">
			        				<v-tooltip bottom>
								      	<template v-slot:activator="{ on, attrs }">
								        	<v-btn
								        		icon
								        		color="primary"
								        		v-bind="attrs"
          										v-on="on"
          										@click="openAddItemDialog(order)"
								        	>
					        					<v-icon>mdi-plus</v-icon>
					        				</v-btn>
								      	</template>
								      	<span>Add Item</span>
								    </v-tooltip>
			        			</v-col>
			        		</v-row>
			        		<v-row>
			        			<v-col cols="8">
			        				<strong>Total:</strong>
			        			</v-col>
			        			<v-col cols="4" class="text-right">
			        				<strong>{{ computeGrandTotal(order) }}</strong>
			        			</v-col>
			        		</v-row>
			        		<v-row>
			        			<v-col cols="12">
			        				<div><strong>Instruction:</strong></div>
			        				<div>{{ order.special_instruction || "No instruction provided." }}</div>
			        			</v-col>
			        		</v-row>
			        		<v-divider class="my-2" />
			        		<v-row>
			        			<v-col cols="12">
			        				<strong>Personal Info:</strong>
			        			</v-col>
			        		</v-row>
			        		<v-row>
			        			<v-col
			        				class="py-0"
			        				cols="3"
			        			>
			        				Name:
			        			</v-col>
			        			<v-col
			        				class="py-0"
			        				cols="9"
			        			>
			        				{{ order.reversed_full_name }}
			        			</v-col>
			        		</v-row>
			        		<v-row>
			        			<v-col
			        				class="py-0"
			        				cols="3"
			        			>
			        				Mobile #:
			        			</v-col>
			        			<v-col
			        				class="py-0"
			        				cols="9"
			        			>
			        				<v-tooltip bottom>
								      	<template v-slot:activator="{ on, attrs }">
								      		<a
								      			v-bind="attrs"
								          		v-on="on"
								      			:href="`tel:${ order.customer_mobile_number }`"
								      		>
					        					{{ order.customer_mobile_number }}
					        				</a>
								      	</template>
								      	<span>Contact</span>
								    </v-tooltip>
			        			</v-col>
			        		</v-row>
			        		<v-row>
			        			<v-col
			        				class="py-0"
			        				cols="3"
			        			>
			        				Email:
			        			</v-col>
			        			<v-col
			        				class="py-0"
			        				cols="9"
			        			>
			        				<v-tooltip bottom>
								      	<template v-slot:activator="{ on, attrs }">
								      		<a
								      			v-bind="attrs"
								          		v-on="on"
								      			:href="`mailto:${ order.customer_email }`"
								      		>
					        					{{ order.customer_email }}
					        				</a>
								      	</template>
								      	<span>Compose Email</span>
								    </v-tooltip>
			        			</v-col>
			        		</v-row>
			        		<v-row>
			        			<v-col
			        				class="py-0"
			        				cols="3"
			        			>
			        				Delivery:
			        			</v-col>
			        			<v-col
			        				class="py-0"
			        				cols="9"
			        			>
			        				{{ completeAddress(order) }}
			        			</v-col>
			        		</v-row>
						</v-card-text>
						<v-divider />
						<v-card-actions>
							<v-btn
								v-if="order.status.id != 6 && order.status.id != 5"
								color="red"
								dark
								@click="openUpdateStatusDialog(order.transaction_id, 6)"
							>Cancel</v-btn>
							<v-spacer />
							<v-btn
								v-if="order.status.id == 2"
								color="orange"
								dark
								@click="openUpdateStatusDialog(order.transaction_id, 3)"
							>Process</v-btn>
							<v-btn
								v-if="order.status.id == 3"
								color="blue"
								dark
								@click="openUpdateStatusDialog(order.transaction_id, 4)"
							>Deliver</v-btn>
							<v-btn
								v-if="order.status.id == 4"
								color="green"
								dark
								@click="openUpdateStatusDialog(order.transaction_id, 5)"
							>Received</v-btn>
						</v-card-actions>
					</v-card>
				</v-col>
			</v-row>

			<v-divider class="my-8" />

			<v-row>
				<v-spacer />
				<paginate action="orders/fetchByVendor" collection="orders"></paginate>
			</v-row>

			<v-dialog
		      v-model="updateStatusDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Update status of {{ selectedTransaction.transactionId }}
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
		          		Are you sure to update the status to {{ status[selectedTransaction.status - 2] ? status[selectedTransaction.status - 2].name : "" }}?
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="updateStatusDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		            		color="primary"
		            		text
		            		:disabled="updateStatusDialogDisable"
		            		@click="updateOrderStatus"
		          		>Update</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>

		    <v-dialog
		      v-model="addItemDialog"
		      @input="v => v || hideAddItemDialog()"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Add Item
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
		        		<v-form ref="addItemForm">
			        		<v-row>
			        			<v-col cols="12">
					          		<v-autocomplete
					          			v-model="additionalItem.item"
							            :items="items"
							            item-value="id"
							            item-text="name"
							            label="Items"
							            return-object
							            :rules="[v => !!v || 'Item is required']"
					          		></v-autocomplete>
					          	</v-col>
					        </v-row>
					        <v-row>
					        	<v-col cols="12" class="text-center">
					        		<v-icon
				          				color="error"
				          				class="mr-4"
				          				@click="decrement(additionalItem)"
				          			>mdi-minus</v-icon>
				          			{{ additionalItem.quantity || 0 }}
				          			<v-icon
				          				color="primary"
				          				class="ml-4"
				          				@click="increment(additionalItem)"
				          			>mdi-plus</v-icon>
					        	</v-col>
					        </v-row>
					    </v-form>
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="hideAddItemDialog(); addItemDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		            		color="primary"
		            		text
		            		:disabled="addItemDialogDisable"
		            		@click="addItem"
		          		>Save</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>

		    <v-dialog
		      v-model="editItemDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Edit Item Quantity
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
				        <v-row>
				        	<v-col cols="12" class="text-center">
				        		<v-icon
			          				color="error"
			          				class="mr-4"
			          				@click="decrement(existingItem)"
			          			>mdi-minus</v-icon>
			          			{{ existingItem.quantity || 0 }}
			          			<v-icon
			          				color="primary"
			          				class="ml-4"
			          				@click="increment(existingItem)"
			          			>mdi-plus</v-icon>
				        	</v-col>
				        </v-row>
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="editItemDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		            		color="primary"
		            		text
		            		:disabled="editItemDialogDisable"
		            		@click="editItem"
		          		>Update</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>

		    <v-dialog
		      v-model="deleteItemDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Remove Item
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
				        Are you sure to remove the item?
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="deleteItemDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		            		color="primary"
		            		text
		            		:disabled="deleteItemDialogDisable"
		            		@click="deleteItem"
		          		>Remove</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>
			
		</v-container>
    </div>
</template>
<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import Paginate from '@components/PaginateComponent'
	import _ from 'lodash'

	export default {
		components: {
			Paginate
		},
		data: () => ({
			search: "",
			searchLoading: false,
			updateStatusDialog: false,
			updateStatusDialogDisable: false,
			addItemDialog: false,
			addItemDialogDisable: false,
			editItemDialog: false,
			editItemDialogDisable: false,
			deleteItemDialog: false,
			deleteItemDialogDisable: false,
			selectedTransaction: {},
			selectedStatus: [],
			additionalItem: {},
			existingItem: {},
			itemToBeRemoved: {},
			items: [],
			headers: [
				{ text: 'Transaction ID', value: 'transaction_id' },
	          	{ text: 'Name', value: 'full_name' },
	          	{ text: 'Mobile Number', value: 'customer_mobile_number' },
	          	{ text: 'Email', value: 'customer_email' },
	          	{ text: 'Item', value: 'item.name' },
	          	{ text: 'Quantity', value: 'quantity' },
	          	{ text: 'Status', value: 'status.name' },
	        ],
	        status: [
	        	{
	        		id: 2,
	        		name: 'Pending',
	        		color: 'grey'
	        	},
	        	{
	        		id: 3,
	        		name: 'Processing',
	        		color: 'orange'
	        	},
	        	{
	        		id: 4,
	        		name: 'For Delivery',
	        		color: 'blue'
	        	},
	        	{
	        		id: 5,
	        		name: 'Delivered',
	        		color: 'green'
	        	},
	        	{
	        		id: 6,
	        		name: 'Cancel',
	        		color: 'red'
	        	},
	        ]
		}),
		async created() {
			let vm = this;

			if (vm.$route.query && vm.$route.query.search) {
				// vm.$store.commit('orders/setParams', {
				// 	page: 1,
				// 	search: vm.$route.query.search
				// });

				vm.search = vm.$route.query.search
			}

			await vm.fetchByVendor();
		},
		computed: {
			...mapState('orders', {
				orders: state => state.orders.data,
				order: state => state.order.data
			}),
			...mapState('items', {
				storeItems: state => state.items.data
			})
		},
		methods: {
			...mapActions({
				'fetchItems': 'items/fetch',
				'fetchByVendor': 'orders/fetchByVendor',
				'save': 'orders/save',
				'update': 'orders/update',
				'updateStatus': 'orders/updateStatus',
				'destroy': 'orders/destroy'
			}),
			computeUnitTotal(order, index) {
				let amount = order.amount.split(',')[index];
				let quantity = order.quantity.split(',')[index];
				return (amount * quantity).toFixed(2)
			},
			computeGrandTotal(order) {
				let amount = order.amount.split(',');
				let quantity = order.quantity.split(',');

				let grandTotal = 0;
				for (let i = 0; i < amount.length; i++) {
					grandTotal += amount[i] * quantity[i];
				}

				return grandTotal.toFixed(2);
			},
			openUpdateStatusDialog(transactionId, status) {
				let vm = this;
				vm.selectedTransaction = {
					transactionId: transactionId,
					status: status
				};
				vm.updateStatusDialog = true;
			},
			hideAddItemDialog() {
				let vm = this;

				// reset validation
				vm.$refs.addItemForm.reset();
			},
			async openAddItemDialog(order) {
				let vm = this;
				let item_ids = order.item_id.split(",");

				// set params of fetch items to store_id = params.id
	            vm.$store.commit('items/setParams', {
	                filters: {
	                    store_id: order.store_id,
	                    status: 2
	                }
	            });
				await vm.fetchItems();

				vm.items = _.filter(vm.storeItems, function(object) {
					if (!item_ids.includes(object.id.toString())) {
						return object;
					}
				});

				vm.additionalItem = {
					item_id: null,
					quantity: 1,
					transaction_id: order.transaction_id,
					store_id: order.store_id,
					special_instruction: order.special_instruction,
					customer_first_name: order.customer_first_name,
					customer_last_name: order.customer_last_name,
					customer_region: order.customer_region,
					customer_province: order.customer_province,
					customer_city: order.customer_city,
					customer_barangay: order.customer_barangay,
					customer_mobile_number: order.customer_mobile_number,
					customer_email: order.customer_email,
					status: order.status.id
				}

				vm.addItemDialog = true;
			},
			async addItem() {
				let vm = this;

				let valid = vm.$refs.addItemForm.validate();
				if (valid) {
					vm.additionalItem.amount = vm.additionalItem.item.amount;
					vm.additionalItem.item_id = vm.additionalItem.item.id;

					vm.addItemDialogDisable = true;
					await vm.save(vm.additionalItem);

					vm.additionalItem = {};
					vm.addItemDialogDisable = false;
					vm.addItemDialog = false;

					await vm.fetchByVendor();
				}
			},
			async openEditDialog(order, index) {
				let vm = this;
				let id = order.order_id.split(",")[index];
				let quantity = order.quantity.split(",")[index];

				vm.existingItem = {
					id: id,
					quantity: parseInt(quantity)
				};

				vm.editItemDialog = true;
			},
			async editItem() {
				let vm = this;
				let id = vm.existingItem.id;
				let quantity = vm.existingItem.quantity;

				vm.editItemDialogDisable = true;
				await vm.update({ id: id, form: { quantity: quantity } });

				await vm.fetchByVendor();

				vm.editItemDialogDisable = false;
				vm.editItemDialog = false;
			},
			async openDeleteDialog(id) {
				let vm = this;

				vm.itemToBeRemoved = {
					id: id
				};

				vm.deleteItemDialog = true;
			},
			async deleteItem() {
				let vm = this;
				let id = vm.itemToBeRemoved.id;

				vm.deleteItemDialogDisable = false;
				await vm.destroy(id);

				await vm.fetchByVendor();

				vm.deleteItemDialogDisable = false;
				vm.deleteItemDialog = false;
			},
			async updateOrderStatus() {
				let vm = this;

				vm.updateStatusDialogDisable = true;
				await vm.updateStatus(vm.selectedTransaction);

				await vm.fetchByVendor();

				vm.updateStatusDialog = false;
				vm.updateStatusDialogDisable = false;
			},
			searchList: _.debounce(async function(query) {
	        	let vm = this;
	        	vm.searchLoading = true;
				vm.$store.commit('orders/setParams', { page: 1, search: query });
				await vm.fetchByVendor();
				vm.searchLoading = false;

				let url = `/orders?search=${ query }`;
				if (vm.$route.fullPath !== url)
				vm.$router.push(`/orders?search=${ query }`);
		    }, 500),
		    increment(food) {
				let vm = this;

				if (!_.has(food, 'quantity')) vm.$set(food, 'quantity', 0);
				if (food.quantity < 100) {
					vm.$set(food, 'quantity', food.quantity + 1);
				}
			},
			decrement(food) {
				let vm = this;
				
				if (!_.has(food, 'quantity')) vm.$set(food, 'quantity', 0);
				if (food.quantity > 1) {
					vm.$set(food, 'quantity', food.quantity - 1);
				}
			}
		},
		watch: {
			search(newVal, oldVal) {
				let vm = this;
	            vm.searchList(newVal);
	        },
			async selectedStatus(val) {
				let vm = this;

				vm.$store.commit('orders/setParams', {
					filters: {
						"orders.status": val
					}
				});

				await vm.fetchByVendor();
			}
		}
	}
</script>