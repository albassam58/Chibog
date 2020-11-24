<template>
    <div>
        <v-container>
            <v-row>
                <v-col
                    cols="6"
                    class="d-flex flex-row">
                    <div
                        class="text-h4 mb-4">Checkout</div>
                </v-col>
                <v-col
                    cols="6"
                    class="d-flex flex-row-reverse">
                    <v-btn
                        color="secondary"
                        dark
                        @click="$router.back(-1)"
                        text>
                        <v-icon>mdi-arrow-left</v-icon>
                        Back
                    </v-btn>
                </v-col>
            </v-row>
            <v-card>
                <v-form
                    ref="form"
                    v-model="valid">
                    <v-card-title>Personal Information</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col
                                cols="12"
                                md="4">
                                <v-text-field
                                    v-model="form.customer_first_name"
                                    :rules="rules"
                                    label="First Name"
                                    required>
                                </v-text-field>
                            </v-col>
                            <v-col
                                cols="12"
                                md="4">
                                <v-text-field
                                    v-model="form.customer_last_name"
                                    :rules="rules"
                                    label="Last Name"
                                    required>
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col
                                cols="12"
                                md="4">
                                <v-text-field
                                    v-model="form.customer_email"
                                    :rules="emailRules"
                                    label="Email"
                                    required>
                                </v-text-field>
                            </v-col>
                            <v-col
                                cols="12"
                                md="4">
                                <v-text-field
                                    v-model="form.customer_mobile_number"
                                    :rules="rules"
                                    label="Mobile Number"
                                    required>
                                </v-text-field>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <!-- SHIPPING INFORMATION -->
                    <v-card-title>Shipping Information</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col
                                cols="12"
                                md="8">
                                <v-autocomplete
                                    v-model="form.customer_region"
                                    :items="regions"
                                    :rules="rules"
                                    autocomplete="new-password"
                                    item-value="id"
                                    item-text="name"
                                    label="Region"
                                    required
                                    @change="findProvincesByRegion(form.customer_region)"></v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col
                                cols="12"
                                md="8">
                                <v-autocomplete
                                    v-model="form.customer_province"
                                    :items="provinces"
                                    :rules="rules"
                                    autocomplete="new-password"
                                    label="Province"
                                    required
                                    @change="findCitiesByProvince(form.customer_province)"></v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col
                                cols="12"
                                md="8">
                                <v-autocomplete
                                    v-model="form.customer_city"
                                    :items="cities"
                                    :rules="rules"
                                    autocomplete="new-password"
                                    label="City"
                                    required
                                    @change="findBarangaysByProvinceCity({ provinceName: form.customer_province, cityName: form.customer_city })"></v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col
                                cols="12"
                                md="8">
                                <v-autocomplete
                                    v-model="form.customer_barangay"
                                    :items="barangays"
                                    :rules="rules"
                                    autocomplete="new-password"
                                    label="Barangay"
                                    required></v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col
                                cols="12"
                                md="8">
                                <v-text-field
                                    v-model="form.customer_street"
                                    :rules="rules"
                                    label="House No. Floor No. Street"
                                    required></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col
                                cols="12"
                                md="8">
                                <v-btn
                                    @click="submit">Order</v-btn>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-form>
            </v-card>
        </v-container>
    </div>
</template>
<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
	    data: () => ({
	        valid: false,
	        form: {
	        	item_id: []
	        },
	        orders: [],
	        rules: [
	            v => !!v || 'Field is required'
	        ],
	        emailRules: [
		        v => !!v || 'Field is required',
		        v => /.+@.+/.test(v) || 'E-mail must be valid',
		    ],
	    }),
	    async created() {
    		let vm = this;

    		// if no order, redirect to home page
    		if (!(localStorage.getItem('orders') && localStorage.getItem('orders') !== "")) {
    			vm.$router.push('/');
    		}

    		await vm.fetchRegions();

    		vm.orders = JSON.parse(localStorage.getItem('orders'));
    	},
    	computed: {
    		...mapState('orders', {
    			itemOrders: state => state.order
    		}),
    		order: {
	           get(){
	             return this.itemOrders
	           },
	           set(newOrder){
	             return newOrder
	           } 
	        },
    		...mapState('regions', {
    			regions: state => state.regions
    		}),
    		...mapState('provinces', {
    			provinces: state => state.provincesByRegion
    		}),
    		...mapState('cities', {
    			cities: state => state.citiesByProvince
    		}),
    		...mapState('barangays', {
    			barangays: state => state.barangaysByProvinceCity
    		}),
    	},
    	methods: {
    		...mapActions({
    			'save': 'orders/save',
    			'fetchRegions': 'regions/fetch',
    			'findProvincesByRegion': 'provinces/findByRegion',
    			'findCitiesByProvince': 'cities/findByProvince',
    			'findBarangaysByProvinceCity': 'barangays/findByProvinceCity',
    		}),
      		async submit() {
      			let vm = this;

        		let valid = vm.$refs.form.validate();
        		if (valid) {
        			try {
	        			// save store
	        			await vm.save({ form: vm.form, orders: vm.orders });

	        			// update card number and remove orders from local storage
	        			localStorage.removeItem('orders');
	        			vm.$emit('update-cart', null);

	        			vm.$router.push({
	        				path: '/orders/success',
	        				query: {
	        					transactions: JSON.stringify(vm.order)
	        				}
	        			});
	        		} catch (err) {
	        			console.log(err);
	        		}
        		}
      		}
      	}
	}
</script>
