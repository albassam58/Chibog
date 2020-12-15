<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>
	<v-container v-if="store || loading">
		<v-row>
			<v-col
				cols="6"
				class="d-flex flex-row"
			>
				<div class="text-h4 mb-4 primary--text">Reviews on {{ store.name }}</div>
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="default" @click="$router.back(-1)" text>
					<v-icon>mdi-arrow-left</v-icon>
					Back
				</v-btn>
			</v-col>
		</v-row>

		<custom-data-table
			module="storeReviews/fetch"
		    :headers="headers"
		    searchHint="Search by first name or last name"
		    :sortBy="sortBy"
      		:sortDesc="sortDesc"
      		:initFilter="filter"
      		ref="table"
        >
        	<template v-slot:item="{ item }">
        		<td>{{ item.first_name }}</td>
        		<td>{{ item.last_name }}</td>
        		<td>{{ item.comment }}</td>
        		<td>
        			<v-spacer></v-spacer>
        			<v-rating
		          		:value="item.rate"
		          		background-color="orange lighten-3"
				         color="orange"
		          		dense
		          		half-increments
		          		readonly
		          		size="14"
		        	></v-rating>
        		</td>
        	</template>
        </custom-data-table>
	</v-container>

	<v-container v-else>
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4 primary--text">Reviews</div>
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
				<v-col cols="12"><div class="text-h5 text-center text--secondary">The reviews you were looking for does not exist</div></v-col>
			</v-row>
		</v-layout>
	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			loading: true,
			headers: [
				{ text: "First Name", value: "first_name" },
				{ text: "Last Name", value: "last_name" },
				{ text: "Review", value: "comment", sortable: false },
				{ text: "Rating", value: "rate" }
			],
			sortBy: ['rate', 'first_name', 'last_name'],
            sortDesc: [false, false, false],
            filter: {}
		}),
		computed: {
			...mapState({
				store: state => state.stores.item,
				reviews: state => state.storeReviews.items
			})
		},
		async created() {
			let vm = this;

			vm.filter = {
				store_id: vm.$route.params.id
			}

			await vm.find(vm.$route.params.id);

			vm.loading = false;
		},
		methods: {
			...mapActions({
				'find': 'stores/find'
			})
		}
	}
</script>