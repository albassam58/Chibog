<template>
	<v-container>
		<!-- SEARCH STORES -->
		<v-autocomplete
		    no-filter
		    v-model="selectedStore"
		    item-value="id"
		    item-text="name"
		    :items="searchedStores"
		    :search-input.sync="searchStore"
		    placeholder="Start typing..."
		    required
		>
		    <template slot="item" slot-scope="data">
		    	<v-list-item-content>
                    <v-list-item-title v-html="data.item.name"></v-list-item-title>
                    <v-list-item-subtitle>
                    	<v-icon small>mdi-map-marker</v-icon>
                    	{{ completeAddress(data.item) }}
                    </v-list-item-subtitle>
                </v-list-item-content>
		    </template>
		</v-autocomplete>

		<!-- WITHIN YOUR CITY -->
    	<div
    		v-if="storesByCity && storesByCity.length"
    		class="text-overline"
    	>
    		{{ currentLocation.address ? currentLocation.address.city : "Within your location" }}
    	</div>
    	<v-row>
			<v-col
				cols="12"
				sm="6"
				md="4"
				v-for="item in storesByCity"
				:key="`${ item.name }city`"
			>
				<store-card
					:vendorUrl="vendorUrl"
					:store="item"
					:hasView="true"
				></store-card>
			</v-col>
		</v-row>
		<!-- END WITHIN YOUR CITY -->

		<!-- ALL STORES -->
		<div
			v-if="stores && stores.length"
			class="text-overline"
		>Stores</div>
		<v-row>
			<v-col
				cols="12"
				sm="6"
				md="4"
				v-for="item in stores"
				:key="item.name"
			>
				<store-card
					:vendorUrl="vendorUrl"
					:store="item"
					:hasView="true"
				></store-card>
			</v-col>
		</v-row>
		<!-- END ALL STORES -->
	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import StoreCard from '@components/StoreCardComponent'

	export default {
		components: {
			StoreCard
		},
		data: () => ({
			loading: false,
			searchStore: null,
			selectedStore: {},
			currentLocation: {},
			vendorUrl: "",
			latitude: null,
			longitude: null
		}),
		async created() {
			let vm = this;
			await vm.allowGeolocation();
		},
		computed: {
			...mapState('stores', {
				stores: state => state.stores.data,
				searchedStores: state => state.searchedStores.data,
				storesByCity: state => state.storesByCity.data
			})
		},
		methods: {
			...mapActions({
				'fetchStores': 'stores/fetch',
				'searchStores': 'stores/search',
				'fetchStoresByCity': 'stores/fetchByCity'
			}),
			async getStores() {
				let vm = this;

				await vm.reverseGeocoding();

				let exclude = null;
				if (vm.currentLocation.address) {
					exclude = vm.currentLocation.address.city;
				}
				await vm.fetchStores({ exclude: exclude });
				vm.vendorUrl = await vm.getVendorUrl();
			},
			allowGeolocation() {
                let vm = this;

                if (navigator.geolocation) {
                    // timeout at 60000 milliseconds (60 seconds)
                    var options = { timeout: 60000 };
                    navigator.geolocation.getCurrentPosition(vm.showPosition, vm.errorHandler, options);
                } else { 
                    console.log("Geolocation is not supported by this browser.");
                }
            },
            async showPosition(position) {
            	let vm = this;

                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;

                vm.latitude = latitude;
                vm.longitude = longitude;

                await vm.getStores();
            },
            async errorHandler(err) {
            	let vm = this;

                if (err.code == 1) {
                   // alert("Location Error: Access is denied!");
                } else if (err.code == 2) {
                   // alert("Location Error: Position is unavailable!");
                }

                await vm.getStores();
            },
			async reverseGeocoding() {
				let vm = this;

				// if there is current location of user
				if (vm.latitude && vm.latitude != "" && vm.longitude && vm.longitude != "") {
					// get location based on latitude and longitude
					
					let { data } = await axios.get(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${ vm.latitude }&lon=${ vm.longitude }`);
					vm.currentLocation = data;
					if (data.address) {
						await vm.fetchStoresByCity(data.address.city);
					}
				}
			}
		},
		watch: {
		    async searchStore(val) {
		    	let vm = this;
		        if (val !== null && val !== '') {
		            await vm.searchStores(val);
		        }
		    },
		    selectedStore(val) {
		    	let vm = this;
		    	vm.$router.push(`/stores/${ val }`);
		    }
		}
	}
</script>