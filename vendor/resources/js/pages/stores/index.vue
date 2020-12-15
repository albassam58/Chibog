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
			<v-col
				cols="6"
				class="d-flex flex-row"
			>
				<div class="text-h4 mb-4 primary--text">Stores</div>
			</v-col>
			<v-col
				cols="6"
				class="d-flex flex-row-reverse"
			>
				<v-btn
					rounded
					v-if="countStores < limit"
					color="primary"
					@click="$router.push('/stores/add')"
				>
					<v-icon>mdi-plus</v-icon>
					Add Store
				</v-btn>
			</v-col>
		</v-row>

		<custom-data-table
			module="stores/fetch"
		    :headers="headers"
		    searchHint="Search by name"
		    :sortBy="sortBy"
      		:sortDesc="sortDesc"
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
        		<td>
        			<router-link :to="`/stores/${ item.id }`">
        				{{ item.name }}
        			</router-link>
        		</td>
        		<td>{{ item.description }}</td>
        		<td>
        			<div>{{ capitalize(item.street) }}</div>
        			<div>Brgy. {{ capitalize(item.barangay) }}</div>
        			<div>{{ capitalize(item.city) }}</div>
        		</td>
        		<td>{{ item.type == 1 ? "Same Day" : "Next Day" }}</td>
        		<td>
        			<v-chip
						v-for="(day, index) in scheduleDay(item)"
						x-small
						:key="day.short"
				      	:color="day.color"
				      	:dark="day.short == 'Mon' || day.short == 'Thu' ? false : true"
				    >
				      	{{ day.short }}
				    </v-chip>
				    <div class="text-caption mt-1">
				    	<v-icon x-small>mdi-calendar-clock</v-icon>
				    	{{ new Date(`2019-05-05 ${ item.schedule_time_in }`) | moment("hh:mm A") }} - {{ new Date(`2019-05-05 ${ item.schedule_time_out }`) | moment("hh:mm A") | moment("hh:mm A") }}
				    </div>
        		</td>
        		<td>
        			<div :class="
	        			{
		        			'grey--text': item.status == 1,
		        			'green--text': item.status == 2,
		        			'red--text': item.status == 3
		        		}
		        	">{{ item.status_value }}</div>
        			<div class="text-caption">{{ item.remarks }}</div>
        		</td>
        		<td class="text-center">
        			<v-tooltip bottom>
	                    <template v-slot:activator="{ on, attrs }">
	                        <v-btn
	                            icon
	                            color="primary"
	                            v-bind="attrs"
	                            v-on="on"
	                            @click="$router.push(`/stores/${ item.id }/edit`)">
	                            <v-icon>mdi-pencil</v-icon>
	                        </v-btn>
	                    </template>
	                    <span>Edit</span>
	                </v-tooltip>
        		</td>
        	</template>
        </custom-data-table>

	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			limit: 1,
			countStores: 0,
			headers: [
				{ text: "Picture", value: "logo", sortable: false },
				{ text: "Name", value: "name" },
				{ text: "Description", value: "description", sortable: false },
				{ text: "Address", value: "", sortable: false },
				{ text: "Delivery", value: "type", sortable: false },
				{ text: "Schedule", value: "", sortable: false },
				{ text: "Status", value: "status" },
				{ text: "Action", align: "center", sortable: false }
			],
			sortBy: ['status', 'name'],
            sortDesc: [false, false],
		}),
		async created() {
			let vm = this;

			if (!localStorage.getItem('authenticated')) {
				vm.$router.push('/login');
			}

			await vm.getVendor();

            // check if email is verified, if not, redirect to email verification
            if (vm.vendor && (!vm.vendor.email_verified_at || !vm.vendor.mobile_verified_at)) {
                if (vm.$route.path != '/verification' && vm.$route.path != "/vendor") {
                    vm.$router.push('/verification');
                }
            }
		},
		computed: {
			...mapState({
				stores: state => state.stores.items,
				vendor: state => state.currentVendor.vendor,
			})
		},
		methods: {
			...mapActions({
				'getVendor': 'currentVendor/getVendor'
			}),
			imageLink(item) {
        		let vm = this;
        		let placeholder = "https://via.placeholder.com/300/FFFFFF/000000?text=No Image";

        		return item.logo ? `/${ item.logo }` : placeholder;
        	}
		},
		watch: {
			stores: {
				deep: true,
				handler(val) {
					let vm = this;

					vm.countStores = _.filter(val.data, function(object) {
						// rejected is not counted
						if (object.status != 3) return object
					}).length;
				}
			}
		}
	}
</script>