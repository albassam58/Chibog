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
			<v-col cols="12">
				<div class="text-h4 mb-4 primary--text">Statistics</div>
			</v-col>
		</v-row>
		<v-row>
			<v-col cols="12" sm="12" md="6">
				<v-card>
					<v-card-title>Total Delivered Sales</v-card-title>
					<v-card-text>
						{{ totalSales.toFixed(2) }}
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" sm="12" md="6">
				<v-card>
					<v-card-title>Total Orders</v-card-title>
					<v-card-text>
						{{ totalOrders }}
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>

		<v-row>
			<v-col cols="12" sm="6" md="3" v-for="item in status" :key="item.id">
				<v-card>
					<v-card-title :class="`${ item.color }--text`">{{ item.name }}</v-card-title>
					<v-card-text>
						{{ mapStatusOrders(item) }}
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>

		<v-card class="mt-4">
			<v-card-title>Sales (Delivered)</v-card-title>
			<v-card-text>
				<bar-chart :chartData="salesBarChartData" :options="salesBarOptions"></bar-chart>
			</v-card-text>
		</v-card>

		<v-card class="mt-4">
			<v-card-title>Orders (Delivered)</v-card-title>
			<v-card-text>
				<bar-chart :chartData="ordersBarChartData" :options="ordersBarOptions"></bar-chart>
			</v-card-text>
		</v-card>
	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import LineChart from "@components/LineChartComponent";
	import BarChart from "@components/BarChartComponent";

	export default {
		components: {
			LineChart,
			BarChart
		},
		data: () => ({
			salesBarChartData: {},
	      	salesBarOptions: {
		        scales: {
		          	yAxes: [{
		            	ticks: {
		              		beginAtZero: true
		              	},
		            	gridLines: {
		              		display: true
		              	}
		            }],
		          	xAxes: [{
		            	ticks: {
		              		beginAtZero: true
		              	},
		            	gridLines: {
		              		display: false
		              	}
		             }]
		        },
		        legend: {
		          	display: false
		        },
		        tooltips: {
		          	enabled: true,
		          	mode: 'single',
		          	callbacks: {
		            	label: function (tooltipItems, data) {
		              		return '₱ ' + tooltipItems.yLabel.toFixed(2);
		            	}
		        	}
		        },
		        responsive: true,
		        maintainAspectRatio: false,
		        height: 200
		    },
		    ordersBarChartData: {},
	      	ordersBarOptions: {
		        scales: {
		          	yAxes: [{
		            	ticks: {
		              		beginAtZero: true
		              	},
		            	gridLines: {
		              		display: true
		              	}
		            }],
		          	xAxes: [{
		            	ticks: {
		              		beginAtZero: true
		              	},
		            	gridLines: {
		              		display: false
		              	}
		             }]
		        },
		        legend: {
		          	display: false
		        },
		        tooltips: {
		          	enabled: true,
		          	mode: 'single',
		          	callbacks: {
		            	label: function (tooltipItems, data) {
		              		return tooltipItems.yLabel;
		            	}
		        	}
		        },
		        responsive: true,
		        maintainAspectRatio: false,
		        height: 200
		    },
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
	        		name: 'Cancelled',
	        		color: 'red'
	        	},
	        ]
		}),
		async created() {
			let vm = this;
			await vm.fetchSales();

			vm.salesBarChartData = {
				labels: _.map(this.sales, 'name'),
	      		datasets: [
	        		{
	          			label: 'GitHub Commits',
	          			backgroundColor: '#f87979',
	          			data: _.map(this.sales, 'amount')
	        		}
	      		]
	      	};

	      	await vm.fetchOrders();
	      	vm.ordersBarChartData = {
				labels: _.map(this.orders, 'name'),
	      		datasets: [
	        		{
	          			label: 'GitHub Commits',
	          			backgroundColor: '#f87979',
	          			data: _.map(this.orders, 'volume')
	        		}
	      		]
	      	};

	      	await vm.fetchTotalSales();
	      	await vm.fetchTotalOrders();
	      	await vm.fetchOrdersPerStatus();
		},
		computed: {
			...mapState('statistics', {
				sales: state => state.sales,
				orders: state => state.orders,
				totalSales: state => state.totalSales,
				totalOrders: state => state.totalOrders,
				ordersPerStatus: state => state.ordersPerStatus
			})
		},
		methods: {
			...mapActions({
				'fetchSales': 'statistics/sales',
				'fetchOrders': 'statistics/orders',
				'fetchTotalSales': 'statistics/totalSales',
				'fetchTotalOrders': 'statistics/totalOrders',
				'fetchOrdersPerStatus': 'statistics/ordersPerStatus'
			}),
			mapStatusOrders(status) {
				let vm = this;
				let value = _.find(vm.ordersPerStatus, { status: status.id });
				if (value) return value.total;
				else return 0;
			}
		}
	}
</script>