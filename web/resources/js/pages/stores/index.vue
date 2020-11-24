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
				<div class="text-h4 mb-4">Stores</div>
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="primary" dark @click="$router.push('/stores/add')">
					<v-icon>mdi-plus</v-icon>
					Add Store
				</v-btn>
			</v-col>
		</v-row>

		<v-row>
			<v-col cols="12" sm="6" md="4" v-for="item in storesByUser" :key="`user${ item.id }`">
				<v-card
			    	:loading="loading"
			    	class=""
			  	>
			    	<template slot="progress">
			      		<v-progress-linear
			        		color="deep-purple"
			        		height="10"
			        		indeterminate
			      		></v-progress-linear>
			    	</template>

			    	<v-img
			      		height="250"
			      		src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
			    	></v-img>

			    	<v-card-title>{{ capitalize(item.name) }}</v-card-title>

			    	<v-card-text>
			      		<v-row
			        		align="center"
			        		class="mx-0"
			      		>
				        	<v-rating
				          		:value="4.5"
				          		color="amber"
				          		dense
				          		half-increments
				          		readonly
				          		size="14"
				        	></v-rating>

				        	<div class="grey--text ml-4">
				          		4.5 (413)
				        	</div>
			      		</v-row>

			      		<div class="my-4 subtitle-1">
			        		<!-- $ • Italian, Cafe -->
			        		<v-icon>mdi-map-marker</v-icon>
			        		{{ completeAddress(item) }}
			      		</div>

			      		<div>{{ item.description }}</div>
			    	</v-card-text>

			    	<v-divider class="mx-4"></v-divider>

			    	<v-card-title>Availability</v-card-title>

			    	<v-card-text>
						<div>
					    	<v-icon>mdi-calendar-clock</v-icon>
					    	{{ new Date(`2019-05-05 ${ item.schedule_time_in }`) | moment("hh:mm A") }} - {{ new Date(`2019-05-05 ${ item.schedule_time_out }`) | moment("hh:mm A") | moment("hh:mm A") }}
					    </div>
			    	</v-card-text>

			    	<v-card-text>
		    			<v-chip
		    				v-for="(day, index) in scheduleDay(item)"
    						:key="day.short"
					      	:color="day.color"
					      	:dark="day.short == 'Mon' || day.short == 'Thu' ? false : true"
					    >
					      	{{ day.short }}
					    </v-chip>
					    
			    	</v-card-text>

			    	<v-card-actions>
			    		<v-btn
			        		color="deep-purple lighten-2"
			        		text
			        		@click="$router.push(`/stores/${ item.id }`)"
			      		>
			        		View
			      		</v-btn>
			      		<v-btn
			        		color="deep-purple lighten-2"
			        		text
			      		>
			        		Edit
			      		</v-btn>
			    	</v-card-actions>
			 	</v-card>
			</v-col>
		</v-row>

	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			loading: false,
		}),
		async created() {
			let vm = this;
			await vm.fetchByUser();
		},
		computed: {
			...mapState('stores', {
				storesByUser: state => state.storesByUser
			})
		},
		methods: {
			...mapActions('stores', ['fetchByUser'])
		}
	}
</script>