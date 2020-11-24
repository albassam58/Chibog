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
			<v-row v-for="(items, index) in orders" :key="index">
				<v-col cols="3" sm="12" md="3" class="d-flex">
					<v-card class=" flex-grow-0">
					    <v-img
					      	src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"
					      	height="200px"
					    ></v-img>

					    <v-card-title>
					      	Top western road trips
					    </v-card-title>

					    <v-card-subtitle>
					      	1,000 miles of wonder
					    </v-card-subtitle>

					    <v-card-actions>
					      	<v-btn
					        	color="orange lighten-2"
					        	text
					      	>
					        	Explore
					      	</v-btn>

					      	<v-spacer></v-spacer>

					      	<v-btn
					        	icon
					        	@click="show = !show"
					      	>
					        	<v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
					      	</v-btn>
					    </v-card-actions>

					    <v-expand-transition>
					      	<div v-show="show">
					        	<v-divider></v-divider>

					        	<v-card-text>
					          		I'm a thing. But, like most politicians, he promised more than he could deliver. You won't have time for sleeping, soldier, not with all the bed making you'll be doing. Then we'll go with that data file! Hey, you add a one and two zeros to that or we walk! You're going to do his laundry? I've got to find a way to escape.
					        	</v-card-text>
					      	</div>
					    </v-expand-transition>
					</v-card>
				</v-col>
				<v-col cols="9" sm="12" md="9" class="d-flex">
					<v-card class=" flex-grow-1">
						<v-card-title>
					      	Orders:
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
							          		<th class="text-center" width="10%">
							            		Quantity
							          		</th>
							          		<th class="text-right" width="20%">
							          			Total
							          		</th>
							          		<th class="text-left" width="15%">
							          			Status
							          		</th>
							        	</tr>
							      	</thead>
							      	<tbody>
							        	<tr
							          		v-for="food in items"
							          		:key="food.name"
							          		v-if="food.quantity"
							        	>
							          		<td class="text-left">{{ food.item ? food.item.name  : "" }}</td>
							          		<td class="text-right">
							          			{{ food.amount.toFixed(2) }}
							          		</td>
							          		<td class="text-center">
							          			{{ food.quantity }}
							          		</td>
							          		<td class="text-right">
							          			{{ computeUnitTotal(food) }}
							          		</td>
							          		<td class="text-left">
							          			<v-chip
											      	class="ma-2"
											      	:color="food.status.color"
											      	dark
											    >
											      	{{ food.status.name }}
											    </v-chip>
							          		</td>
							        	</tr>
							        	<tr>
							        		<td><strong>Total</strong></td>
							        		<td colspan="3" class="text-right">
							        			<strong>{{ computeOrderTotal(index) }}</strong>
							        		</td>
							        		<td></td>
							        	</tr>
							      	</tbody>
							    </template>
							</v-simple-table>
					    </v-card-text>
					</v-card>
				</v-col>
			</v-row>

			<v-row v-if="hasSearched && !orders.length">
				<v-cols cols="12">
					asd
				</v-cols>
			</v-row>
    	</v-container>
    </div>
</template>
<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			loading: false,
			show: false,
			hasSearched: false,
			query: ""
		}),
	    computed: {
	    	...mapState('orders', {
	    		orders: state => _.groupBy(state.orders, (item) => {
	    			return item.store_id;
	    		})
	    	})
	    },
	    methods: {
	    	...mapActions({
	    		'search': 'orders/search'
	    	}),
	    	computeUnitTotal(item) {
	    		let vm = this;

	    		return (item.amount * item.quantity).toFixed(2);
	    	},
	    	computeOrderTotal(index) {
				let vm = this;

				let orderTotal = 0;
				orderTotal += _.sumBy(vm.orders[index], function (item) {
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

	    		if(vm.query) vm.hasSearched = true;
	    		else vm.hasSearched = false;
	    		
	    		vm.loading = false;
	    	}
	    }
	}
</script>
