<template>
	<div>
		<v-container v-if="items && items.length">
			<v-row>
				<v-col cols="6" class="d-flex flex-row">
					<div class="text-h4 mb-6">Order Summary</div>
				</v-col>
				<v-col cols="6" class="d-flex flex-row-reverse">
					<v-btn color="secondary" dark @click="$router.back(-1)" text>
						<v-icon>mdi-arrow-left</v-icon>
						Back
					</v-btn>
				</v-col>
			</v-row>
			<v-row>
				<v-col cols="3" sm="12" md="4">
					<store-card :vendorUrl="vendorUrl" :store="items[0].store" :simplified="true"></store-card>
				</v-col>
				<v-col cols="9" sm="12" md="8" class="d-flex">
					<v-card class=" flex-grow-1">
						<v-card-title>
					      	Orders:

					      	<v-spacer></v-spacer>

					      	<v-tooltip
					        	left
					        >
						      	<template v-slot:activator="{ on, attrs }">
						      		<v-btn
							      		icon
							      		color="error"
							      		v-bind="attrs"
							         	v-on="on"
							         	@click="removeOrderDialog = true"
							        >
						      			<v-icon>mdi-window-close</v-icon>
						      		</v-btn>
						      	</template>
						      	<span>Remove Order</span>
						    </v-tooltip>
					      	
					    </v-card-title>
					    <v-card-text>
					    	<v-simple-table>
								<template v-slot:default>
							      	<thead>
							        	<tr>
							          		<th class="text-left" width="40%">
							            		Name
							          		</th>
							          		<th class="text-right" width="15%">
							            		Amount
							          		</th>
							          		<th class="text-center" width="25%">
							            		Quantity
							          		</th>
							          		<th class="text-right" width="20%">
							          			Total
							          		</th>
							        	</tr>
							      	</thead>
							      	<tbody>
							        	<tr
							          		v-for="(item, index) in items"
							          		:key="item.name"
							          		v-if="item.quantity"
							        	>
							          		<td class="text-left">{{ item.name }}</td>
							          		<td class="text-right">
							          			{{ item.amount.toFixed(2) }}
							          		</td>
							          		<td class="text-center">
							          			<v-icon
							          				color="error"
							          				class="mr-4"
							          				@click="decrement(item, index)"
							          			>mdi-minus</v-icon>
							          			{{ item.quantity || 0 }}
							          			<v-icon
							          				color="primary"
							          				class="ml-4"
							          				@click="increment(item, index)"
							          			>mdi-plus</v-icon>
							          		</td>
							          		<td class="text-right">
							          			{{ item.total.toFixed(2) }}
							          		</td>
							        	</tr>
							        	<tr>
							        		<td><strong>Total</strong></td>
							        		<td colspan="3" class="text-right">
							        			<strong>{{ computeTotal() }}</strong>
							        		</td>
							        		<td></td>
							        	</tr>
							      	</tbody>
							    </template>
							</v-simple-table>
							<v-textarea
								v-model="specialInstruction"
						      	counter="300"
						      	label="Special Instruction"
						      	flat
						      	:rules="instructionRule"
						    ></v-textarea>
					    </v-card-text>
					</v-card>
				</v-col>
			</v-row>

			<v-row>
				<v-col cols="12" class="text-right">
					<v-btn @click="checkout">Proceed to Checkout</v-btn>
				</v-col>
			</v-row>

			</v-card>
		</v-container>

		<v-container v-else bg fill-height grid-list-md text-xs-center>
	        <v-layout row wrap align-center class="pt-12">
	          	<v-flex>
	            	<div class="text-h3">No Orders Yet</div>
	          	</v-flex>
	        </v-layout>
      	</v-container>

      	<v-dialog
	      	v-model="removeOrderDialog"
	      	width="500"
	    >
	      	<v-card>
		        <v-card-title>
		          Remove Order
		        </v-card-title>

		        <v-card-text class="mt-4">
		          	Are you sure you want to remove your order?
		        </v-card-text>

		        <v-divider></v-divider>

		        <v-card-actions>
		          	<v-spacer></v-spacer>
		          	<v-btn
		          		color="grey"
		            	text
		            	@click="removeOrderDialog = false"
		          	>
		            	Close
		          	</v-btn>
		          	<v-btn
		            	color="error"
		            	text
		            	@click="removeOrder"
		          	>
		            	Remove
		          	</v-btn>
		        </v-card-actions>
	      	</v-card>
	    </v-dialog>

      	<v-dialog
	      	v-model="removeItemDialog"
	      	width="500"
	    >
	      	<v-card>
	        	<v-card-title>
		          Remove {{ itemToBeRemoved.name }}?
		        </v-card-title>

		        <v-card-text class="mt-4">
		          	Are you sure you want to remove the item?
		        </v-card-text>

		        <v-divider></v-divider>

		        <v-card-actions>
		          	<v-spacer></v-spacer>
		          	<v-btn
		          		color="grey"
		            	text
		            	@click="removeItemDialog = false"
		          	>
		            	Close
		          	</v-btn>
		          	<v-btn
		            	color="error"
		            	text
		            	@click="removeItem"
		          	>
		            	Remove
		          	</v-btn>
		        </v-card-actions>
	      	</v-card>
	    </v-dialog>
    </div>
</template>
<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'
	import StoreCard from '@components/StoreCardComponent'

	export default {
		components: {
			StoreCard
		},
		data: () => ({
			show: false,
			removeOrderDialog: false,
			removeItemDialog: false,
			items: JSON.parse(localStorage.getItem('order')),
			user: null,//JSON.parse(localStorage.getItem('current_user')),
			itemToBeRemoved: {},
			form: {
				status: 1
			},
			vendorUrl: "",
			specialInstruction: "",
			grandTotal: 0,
			instructionRule: [
	        	v => (v ? v.length <= 300 : !v) || 'Special instruction must be less than 300 characters',
		    ]
		}),
		async created() {
			let vm = this;

			vm.vendorUrl = await vm.getVendorUrl();

			if (vm.user) {
				// set parserams of fetch items to store_id = params.id
                vm.$store.commit('orders/setParams', {
                    filters: {
                        status: 1,
                        created_by: vm.user.id
                    }
                });
				await vm.fetch();

				vm.items = vm.orders;

				vm.form = {
					...vm.form,
                    customer_first_name: vm.user.first_name,
                    customer_last_name: vm.user.last_name,
                    customer_mobile_number: vm.user.mobile_number,
                    customer_email: vm.user.email,
                    customer_region: vm.user.region,
                    customer_province: vm.user.province,
                    customer_city: vm.user.city,
                    customer_barangay: vm.user.barangay,
                    customer_street: vm.user.street
                };
			}
		},
		computed: {
			...mapState('orders', {
                orders: state => state.orders,
                order: state => state.order,
                hasOrder: state => state.hasOrder
            })
		},
		methods: {
			...mapActions({
                'fetch': 'orders/fetch',
                'update': 'orders/update',
                'updateByStore': 'orders/updateByStore',
                'destroy': 'orders/destroy',
                'destroyByStore': 'orders/destroyByStore'
			}),
			computeTotal() {
				let vm = this;

				let total = 0;
				total += _.sumBy(vm.items, function (item, index) {
			        return item.amount * item.quantity || 0;
			    });

				return total.toFixed(2);
			},
			async removeOrder() {
				let vm = this;

				if (vm.user) {
					await vm.destroyByStore(vm.items);
				} else {
					localStorage.removeItem("order");
				}

				vm.items = [];

				vm.$emit('update-cart', false);
				vm.removeOrderDialog = false;
			},
			async removeItem() {
				let vm = this;

				let index = vm.itemToBeRemoved.index;

				vm.items[index].quantity = 0;
				vm.items[index].total = 0;

				if (vm.user) {
					await vm.update({ form: vm.form, item: vm.items[index] });
				} else {
					vm.updateItemLocalStorage("order", vm.items[index], index);
				}

				// check if all items are removed, then remove the order
				let checkAllQuantities = vm.items.some(obj => Boolean(obj.quantity));
				if (!checkAllQuantities) {
					vm.removeOrder();
				}

				vm.removeItemDialog = false;
			},
			async increment(item, index) {
				let vm = this;

				if (!_.has(item, 'quantity')) vm.$set(item, 'quantity', 0);
				if (item.quantity < 100) {
					vm.$set(item, 'quantity', item.quantity + 1);
				}

				item.total = item.amount * item.quantity;

				if (vm.user) {
					await vm.update({ form: vm.form, item: item });
				} else {
					vm.updateItemLocalStorage("order", item, index);
				}
			},
			async decrement(item, index) {
				let vm = this;
				
				if (!_.has(item, 'quantity')) vm.$set(item, 'quantity', 0);

				if (item.quantity == 1) {
					vm.itemToBeRemoved = {
						index: index,
						name: item.name
					}

					vm.removeItemDialog = true;
				} else {
					vm.$set(item, 'quantity', item.quantity - 1);

					item.total = item.amount * item.quantity;

					if (vm.user) {
						await vm.update({ form: vm.form, item: item });
					} else {
						vm.updateItemLocalStorage("order", item, index);
					}
				}
			},
			async checkout() {
				let vm = this;

				vm.items = _.map(vm.items, function(object) {
					object.special_instruction = vm.specialInstruction;
					return object;
				});

				if (vm.user) {
					await vm.updateByStore({ form: vm.form, items: vm.items });
				} else {
					localStorage.setItem("order", JSON.stringify(vm.items));
				}

				vm.$router.push('/orders/checkout');
			}
		}
	}
</script>