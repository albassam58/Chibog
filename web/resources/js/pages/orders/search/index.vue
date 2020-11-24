<template>
    <div>
        <v-container>
            <v-row>
                <v-col
                    cols="12"
                    class=""
                >
                    <div class="text-h4 mb-4">Track Your Orders</div>
			    </v-col>
    		</v-row>
    		<v-row class="justify-center">
                <v-col
                    cols="6"
                    class=""
                >
				    <v-text-field
				    	v-model="query"
				    	:loading="loading"
				    	label="Start typing..."
				    	@keyup.enter="trackOrder"
				    >
				    	<v-icon
					      slot="append"
					      color="grey"
					      @click="trackOrder"
					    >
					      mdi-magnify
					    </v-icon>
					</v-text-field>
				</v-col>
			</v-row>

			<!-- ORDERS -->
			<v-row v-if="orders.length">
				<v-col cols="12" sm="6" md="4">
					<store-card :vendorUrl="vendorUrl" :store="orders[0].store" :simplified="true"></store-card>
				</v-col>
				<v-col cols="12" sm="6" md="8" class="d-flex">
					<v-card class="flex-grow-1">
						<v-card-title>
					      	Orders:
					      	<v-spacer />
							<v-chip
								:color="orders[0].status.color"
								dark
							>
								{{ orders[0].status.name }}
							</v-chip>
					    </v-card-title>
					    <v-card-text>
					    	<v-simple-table>
								<template v-slot:default>
							      	<thead>
							        	<tr>
							          		<th class="text-left" width="45%">
							            		Name
							          		</th>
							          		<th class="text-right" width="15%">
							            		Amount
							          		</th>
							          		<th class="text-center" width="15%">
							            		Quantity
							          		</th>
							          		<th class="text-right" width="25%">
							          			Total
							          		</th>
							        	</tr>
							      	</thead>
							      	<tbody>
							        	<tr
							          		v-for="item in orders"
							          		:key="item.name"
							          		v-if="item.quantity"
							        	>
							          		<td class="text-left">{{ item.item ? item.item.name  : "" }}</td>
							          		<td class="text-right">
							          			{{ item.amount.toFixed(2) }}
							          		</td>
							          		<td class="text-center">
							          			{{ item.quantity }}
							          		</td>
							          		<td class="text-right">
							          			{{ computeUnitTotal(item) }}
							          		</td>
							        	</tr>
							        	<tr>
							        		<td><strong>Total</strong></td>
							        		<td colspan="3" class="text-right">
							        			<strong>{{ computeOrderTotal() }}</strong>
							        		</td>
							        		<td></td>
							        	</tr>
							      	</tbody>
							    </template>
							</v-simple-table>

							<div><strong>Instruction:</strong></div>
			        		<div>{{ orders[0].special_instruction || "No instruction provided." }}</div>
					    </v-card-text>
					    <v-card-actions>
					    	<v-btn
					    		color="error"
					    		v-if="orders[0].status.id == 2"
					    		@click="openUpdateStatusDialog(orders[0].transaction_id, 6)"
					    	>Cancel</v-btn>
					    </v-card-actions>
					</v-card>
				</v-col>
			</v-row>

			<!-- if delivered -->
			<v-card v-if="orders.length && orders[0].status.id == 5 && !rated">
				<v-card-title>
					Ratings & Reviews
				</v-card-title>
				<v-card-text>
					<v-form
						ref="form"
						v-model="valid"
						lazy-validation
					>
						<!-- RATE -->
						<v-row>
							<v-col cols="12" class="text-center mt-4 py-0">
								<div class="text-h6">
							      Rate Our Store
							    </div>
							    <div class="text-subtitle-1">
							      If you enjoy our services and foods, please take a few seconds to rate your experience with the store. It really helps!
							    </div>
							</v-col>
							<v-col cols="12" class="text-center py-0">
							    <div class="d-inline-block">
							    	<v-input :value="rating.rate" :rules="ratingRule">
										<v-rating
									      v-model="rating.rate"
									      background-color="orange lighten-3"
									      color="orange"
									      required
									    ></v-rating>
									</v-input>
								</div>
								<div class="grey--text text--lighten-1 ml-2 d-inline-block">
							        ({{ rating.rate }})
							    </div>
							</v-col>
						</v-row>
						<v-row>
							<v-col cols="12">
								<v-textarea
									v-model="rating.comment"
							      	counter="300"
							      	label="Leave a comment..."
							      	solo
							      	flat
							      	:rules="commentRule"
							      	@keydown.enter="rate"
							    >
						          	<template v-slot:append>
						            	<v-btn
						              		class="mx-0"
						              		depressed
						              		@click="rate"
						           	 	>
						              		Post
						            	</v-btn>
						          	</template>
						        </v-textarea>
						    </v-col>
					    </v-row>
					    <!-- END RATE -->
					</v-form>
				</v-card-text>
			</v-card>

			<div class="text-h4 my-4 text-center" v-if="rated">
				Thank you and come again!
			</div>

			<v-dialog
		      v-model="updateStatusDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title class="headline grey lighten-2">
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
		            		@click="update"
		          		>Update</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>
    	</v-container>
    </div>
</template>
<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import StoreCard from '@components/StoreCardComponent'

	export default {
		components: {
			StoreCard
		},
		data() {
			return {
				valid: true,
				loading: false,
				show: false,
				updateStatusDialog: false,
				updateStatusDialogDisable: false,
				selectedTransaction: {},
				query: "",
				vendorUrl: "",
				rating: {
			      	rate: 0
			    },
			    ratingRule: [
			    	v => !!v || 'Stars are between 1 to 5',
			    ],
			    commentRule: [
		        	v => (v ? v.length <= 300 : !v) || 'Comment must be less than 300 characters',
			    ],
			    rated: 0,
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
			}
		},
		async created() {
			let vm = this;
			let query = vm.$route.query ? vm.$route.query.query : null;
			if (query) {
				vm.query = query;
				vm.loading = true;
	    		await vm.search(vm.query);
	    		vm.loading = false;

	    		vm.rated = vm.orders[0].rated;
			}

			vm.vendorUrl = await vm.getVendorUrl();
		},
	    computed: {
	    	...mapState('orders', {
	    		orders: state => state.searchedOrders
	    	})
	    },
	    methods: {
	    	...mapActions({
	    		'search': 'orders/search',
	    		'updateStatus': 'orders/updateStatus',
	    		'save': 'storeReviews/save' 
	    	}),
	    	computeUnitTotal(item) {
	    		let vm = this;

	    		return (item.amount * item.quantity).toFixed(2);
	    	},
	    	computeOrderTotal() {
				let vm = this;

				let orderTotal = 0;
				orderTotal += _.sumBy(vm.orders, function (item) {
			        return item.amount * item.quantity;
			    });

				return orderTotal.toFixed(2);
			},
			// computeGrandTotal() {
			// 	let vm = this;

			// 	let grandTotal = 0;
			// 	for (let index in vm.items) {
			// 		grandTotal += _.sumBy(vm.items[index], function (item) {
			// 	        return item.total;
			// 	    });
			// 	}

			// 	return grandTotal.toFixed(2);
			// },
	    	async trackOrder() {
	    		let vm = this;

	    		vm.loading = true;
	    		await vm.search(vm.query);
	    		if (vm.$route.query.query !== vm.query) {
		    		vm.$router.replace({
		    			name: 'orders-search',
		    			query: { ...vm.$route.query, query: vm.query }
		    		});
		    	}
	    		vm.loading = false;
	    	},
	    	async rate() {
	    		try {
	    			let vm = this;
	    			let valid = vm.$refs.form.validate();
        			if (valid) {
        				vm.rating['store_id'] = vm.orders[0].store_id;
        				vm.rating['orders'] = _.map(vm.orders, 'id');
	    				await vm.save(vm.rating);
	    				vm.rated = 1;
	    			}
	    		} catch (err) {
	    			console.log(err);
	    		}
	    	},
	    	openUpdateStatusDialog(transactionId, status) {
				let vm = this;
				vm.selectedTransaction = {
					transactionId: transactionId,
					status: status
				};
				vm.updateStatusDialog = true;
			},
	    	async update() {
				let vm = this;

				vm.updateStatusDialogDisable = true;
				await vm.updateStatus(vm.selectedTransaction);

				vm.loading = true;
	    		await vm.search(vm.query);
	    		vm.loading = false;

				vm.updateStatusDialog = false;
				vm.updateStatusDialogDisable = false;
			},
	    }
	}
</script>
