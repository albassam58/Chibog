<template>
    <div>
        <v-container>
            <v-row>
                <v-col
                    cols="12"
                    class=""
                >
                    <div class="text-h4 mb-4">Orders</div>
			    </v-col>
    		</v-row>
 
			<!-- ORDERS -->
			<v-row v-for="(order, index) in userOrders" :key="index">
				<v-col cols="12" sm="6" md="4">
					<store-card :vendorUrl="vendorUrl" :store="order[0].store" :simplified="true"></store-card>
				</v-col>
				<v-col cols="12" sm="6" md="8" class="d-flex">
					<v-card class="flex-grow-1">
						<v-card-title>
					      	{{ index }}
					      	<v-spacer />
							<v-chip
								:color="order[0].status.color"
								dark
							>
								{{ order[0].status.name }}
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
							          		v-for="item in order"
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
					    </v-card-text>
					    <v-card-actions>
					    	<v-btn
					    		color="error"
					    		v-if="order[0].status.id == 2"
					    		@click="openUpdateStatusDialog(index, 6)"
					    	>Cancel</v-btn>
					    </v-card-actions>
					</v-card>
				</v-col>
			</v-row>
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
				userOrders: [],
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
			await vm.fetchByUser();
			vm.userOrders = vm.orders;
			vm.vendorUrl = await vm.getVendorUrl();
		},
	    computed: {
	    	...mapState('orders', {
	    		orders: state => _.groupBy(state.orders.data, function(object) {
	    			return object.transaction_id;
	    		})
	    	})
	    },
	    methods: {
	    	...mapActions({
	    		'fetchByUser': 'orders/user',
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
