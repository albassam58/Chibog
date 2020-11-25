<template>
	<v-container>

		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<!-- <div class="text-h4 mb-4">View Store</div> -->
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="secondary" dark @click="$router.back(-1)" text>
					<v-icon>mdi-arrow-left</v-icon>
					Back
				</v-btn>
			</v-col>
		</v-row>

		<v-row v-if="store && !(Object.keys(store).length === 0 && store.constructor === Object)">
			<v-col cols="12" sm="6" md="4">
				<store-card v-on:is-available="availability" :vendorUrl="vendorUrl" :store="store" :hasRating="true"></store-card>
			</v-col>

			<!-- MENU -->
			<v-col cols="12" sm="6" md="8">
				<div class="text-h5">Menu</div>
				<v-divider class="my-4"></v-divider>
				<v-row v-for="(items, index) in groupedItems" :key="index">
					<v-col cols="12">
						<div class="text-h6">{{ meals[index - 1] ? meals[index - 1].name : "" }}</div>
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
						          		v-for="item in items"
						          		:key="item.name"
						          		v-if="item.name"
						          		:class="isAvailable ? '' : 'text--disabled'"
						        	>
						          		<td class="text-left">{{ item ? item.name : "" }}</td>
						          		<td class="text-right">
						          			{{ parseFloat(item.amount).toFixed(2) }}
						          		</td>
						          		<td class="text-center">
						          			<v-icon
						          				:color="isAvailable ? 'error' : 'grey'"
						          				class="mr-4"
						          				@click="decrement(item)"
						          			>mdi-minus</v-icon>
						          			{{ item.quantity || 0 }}
						          			<v-icon
						          				:color="isAvailable ? 'primary' : 'grey'"
						          				class="ml-4"
						          				@click="increment(item)"
						          			>mdi-plus</v-icon>
						          		</td>
						          		<td class="text-right">
						          			{{ unitTotal(item) }}
						          		</td>
						        	</tr>
						      	</tbody>
						    </template>
						</v-simple-table>
					</v-col>
				</v-row>
				<v-row :class="isAvailable ? '' : 'text--disabled'">
					<v-col cols="12" sm="12" md="4" class="text-left">
						<strong>Total</strong>
					</v-col>
					<v-col cols="12" sm="12" md="8" class="text-right">
						<strong>{{ grandTotal.toFixed(2) }}</strong>
					</v-col>
				</v-row>
				<v-row>
					<v-col cols="12" class="text-right">
						<v-btn @click="addToCart" :disabled="!isAvailable" v-if="isUpdate">Update Cart</v-btn>
						<v-btn @click="addToCart" :disabled="!isAvailable" v-else>Add to Cart</v-btn>
					</v-col>
				</v-row>
			</v-col>
		</v-row>

		<v-row>
			<v-col cols="12">
				<store-review :store="store" :reviews="reviews" :can-rate="false" ref="reviews"></store-review>
			</v-col>
		</v-row>

		<v-dialog
	      	v-model="replaceCartDialog"
	      	width="500"
	    >
	      	<v-card>
	        	<v-card-title>
		          Replace Order
		        </v-card-title>

		        <v-card-text class="mt-4">
		          	Are you sure you want to replace your order with the new items?
		        </v-card-text>

		        <v-divider></v-divider>

		        <v-card-actions>
		          	<v-spacer></v-spacer>
		          	<v-btn
		          		color="grey"
		            	text
		            	@click="replaceCartDialog = false"
		          	>
		            	Close
		          	</v-btn>
		          	<v-btn
		            	color="primary"
		            	text
		            	@click="saveOrUpdateItems"
		          	>
		            	Replace
		          	</v-btn>
		        </v-card-actions>
	      	</v-card>
	    </v-dialog>

	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import StoreCard from '@components/StoreCardComponent'
	import StoreReview from '@components/StoreReviewComponent'

	export default {
		components: {
			StoreCard,
			StoreReview
		},
		data() {
			return {
				id: this.$route.params.id,
				user: null,//JSON.parse(localStorage.getItem('current_user')),
				cartItems: JSON.parse(localStorage.getItem('order')) || [],
				groupedItems: [],
				form: {},
				grandTotal: 0,
				isAvailable: true,
				vendorUrl: "",
	      		isUpdate: false,
	      		replaceCartDialog: false,
		        meals: [
		        	{ id: 1, name: 'Breakfast' },
		        	{ id: 2, name: 'Lunch' },
		        	{ id: 3, name: 'Snack' },
		        	{ id: 4, name: 'Dinner' },
		        ],
	      		orderParams: {
					filters: {
						status: 1,
						created_by: null
					}
				},
				itemParams: {
					filters: {
						store_id: this.$route.params.id
					}
				},
				storeReviewParams: {
					filters: {
						store_id: this.$route.params.id
					}
				}
	      	}
		},
		async created() {
			let vm = this;

			await vm.find(vm.id);
			await vm.fetchCuisines();
			await vm.fetchFoodTypes();

			vm.setParams('storeReviews', vm.storeReviewParams);
			await vm.fetchReviews();

			vm.setParams('items', vm.itemParams);
			await vm.fetchItems();

			vm.vendorUrl = await vm.getVendorUrl();

			await vm.manageItemList();
		},
		computed: {
			...mapState('stores', {
				store: state => state.store.data
			}),
			...mapState('cuisines', {
				cuisines: state => state.cuisines
			}),
			...mapState('foodTypes', {
				foodTypes: state => state.foodTypes
			}),
			...mapState('items', {
				items: state => state.items
			}),
			...mapState('storeReviews', {
				reviews: state => state.reviews.data
			}),
			...mapState('orders', {
                orders: state => state.orders,
                hasOrder: state => state.hasOrder
            })
		},
		methods: {
			...mapActions({
				'find': 'stores/find',
				'fetchCuisines': 'cuisines/fetch',
				'fetchFoodTypes': 'foodTypes/fetch',
				'fetchItems': 'items/fetch',
				'fetchReviews': 'storeReviews/fetch',
				'fetchOrders': 'orders/fetch',
				'saveByStore': 'orders/saveByStore',
				'updateByStore': 'orders/updateByStore'
			}),
			availability(val) {
				let vm = this;
				vm.isAvailable = val;
			},
			async addToCart() {
				let vm = this;

				if (!vm.isAvailable) return; // don't do anything if not available

				if (vm.hasOrder && !vm.isUpdate) {
					// already has order but will make a new order, add a warning message
					vm.replaceCartDialog = true;
					return;
				}

				await vm.saveOrUpdateItems();
			},
			async saveOrUpdateItems() {
				let vm = this;

				// remove grouped by meals (breakfash, lunch, snacks, dinner)
				const ungroupedItems = _.flatMap(vm.groupedItems);

				if (vm.user) {
					vm.form = {
						store_id: vm.id,
		                customer_first_name: vm.user ? vm.user.first_name : null,
		                customer_last_name: vm.user ? vm.user.last_name : null,
		                customer_mobile_number: vm.user ? vm.user.mobile_number : null,
		                customer_email: vm.user ? vm.user.email : null,
		                customer_region: vm.user ? vm.user.region : null,
		                customer_province: vm.user ? vm.user.province : null,
		                customer_city: vm.user ? vm.user.city : null,
		                customer_barangay: vm.user ? vm.user.barangay : null,
		                customer_street: vm.user ? vm.user.street : null,
		                status: 1
		            };

					if (vm.isUpdate) {
						await vm.updateByStore({ form: vm.form, items: ungroupedItems });
					} else {
	                    await vm.saveByStore({ form: vm.form, items: ungroupedItems });
	                }
				} else {
					localStorage.setItem('order', JSON.stringify(ungroupedItems));
				}

				vm.$emit('update-cart', true);
				vm.$router.push('/orders/cart');
			},
			async manageItemList() {
				let vm = this;

				if (vm.user) {
					// get the orders from db
					// set params of fetch items
	                vm.orderParams['filters']['created_by'] = vm.user.id;
	                vm.setParams('orders', vm.params);

					await vm.fetchOrders();

					vm.cartItems = vm.orders;
				}

				if (vm.cartItems.length) {
					if (vm.cartItems[0].store_id == vm.id) {
						// check if same store, then make the add to cart => update cart
						vm.isUpdate = true;

						await vm.updateItemList();
					}
				}

				vm.groupedItems = _.groupBy(vm.items, function(object) {
					return object.meal;
				});

				for (let index in vm.groupedItems) {
					if (index === "undefined") {
						delete vm.groupedItems[index];
					}
				}
			},
			updateItemList() {
				let vm = this;

				let index;
				let updatedItemList = [];
				for (let i = 0; i < vm.cartItems.length; i++) {
					let item = vm.cartItems[i];

					index = false;
					if (vm.user) {
						// add meal for grouping of items (breakfast, lunch, etc...)
						item.meal = item.item ? item.item.meal : null
						updatedItemList.push(item);
					} else {
						// find item index using _.findIndex
						index = _.findIndex(vm.items, { id : item.id });

						if (index !== false) {
							// replace item at index using native splice
							updatedItemList.push({
								...vm.items[index],
								...{ quantity: item.quantity }
							});
						}
					}
				}

				vm.$store.commit('items/setItems', updatedItemList);
			},
			unitTotal(item) {
				let vm = this;
				let total = 0;

				item.total = parseFloat(item.amount) * item.quantity || 0;

				return item.total.toFixed(2);
			},
			increment(item) {
				let vm = this;

				if (!vm.isAvailable) return; // don't do anything if not available

				if (!_.has(item, 'quantity')) vm.$set(item, 'quantity', 0);
				if (item.quantity < 100) {
					vm.$set(item, 'quantity', item.quantity + 1);
				}
			},
			decrement(item) {
				let vm = this;

				if (!vm.isAvailable) return; // don't do anything if not available
				
				if (!_.has(item, 'quantity')) vm.$set(item, 'quantity', 0);
				if (item.quantity > 0) {
					vm.$set(item, 'quantity', item.quantity - 1);
				}
			}
		},
		watch: {
			groupedItems: {
				deep: true,
				handler(val) {
					let vm = this;

					vm.grandTotal = 0;
					for(let index in val) {
						vm.grandTotal += _.sumBy(val[index], function (item) {
					        return item.amount * item.quantity || 0;
					    });
					}
				}
			}
		}
	}
</script>