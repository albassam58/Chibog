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
	        	status: 2
	        },
	        items: JSON.parse(localStorage.getItem('order')),
            user: JSON.parse(localStorage.getItem('current_user')),
	        rules: [
	            v => !!v || 'Field is required'
	        ],
	        emailRules: [
		        v => !!v || 'Field is required',
		        v => /.+@.+/.test(v) || 'E-mail must be valid',
		    ]
	    }),
	    async created() {
    		let vm = this;

            await vm.fetchRegions();

            if (vm.user) {
                // // set params of fetch items to store_id = params.id
                // vm.$store.commit('orders/setParams', {
                //     filters: {
                //         status: 1,
                //         created_by: vm.user.id
                //     }
                // });
                // await vm.fetch();
                // vm.items = vm.orders;
                
                await vm.findProvincesByRegion(vm.user.region);
                await vm.findCitiesByProvince(vm.user.province);
                await vm.findBarangaysByProvinceCity({
                    provinceName: vm.user.province,
                    cityName: vm.user.city
                });

                vm.form = {
                    ...vm.form,
                    customer_first_name: vm.user.first_name,
                    customer_last_name: vm.user.last_name,
                    customer_mobile_number: vm.user.mobile_number,
                    customer_email: vm.user.email,
                    customer_region: vm.user.region,
                    customer_province: vm.user.province,
                    customer_city: vm.user.city,
                    customer_barangay: vm.user.barangay,
                    customer_street: vm.user.street
                };
            }

            if (!vm.items) {
                // if no order, redirect to home page
                vm.$router.push('/');
            }
    	},
    	computed: {
    		...mapState('orders', {
                orders: state => state.orders
    		}),
    		...mapState('regions', {
    			regions: state => state.regions.data
    		}),
    		...mapState('provinces', {
    			provinces: state => state.provincesByRegion.data
    		}),
    		...mapState('cities', {
    			cities: state => state.citiesByProvince.data
    		}),
    		...mapState('barangays', {
    			barangays: state => state.barangaysByProvinceCity.data
    		}),
    	},
    	methods: {
    		...mapActions({
                'fetch': 'orders/fetch',
                'saveByStore': 'orders/saveByStore',
                'updateByStore': 'orders/updateByStore',
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
                        // if (vm.user) {
                        //     await vm.updateByStore({ form: vm.form, items: vm.items });
                        // } else {
                        //     await vm.saveByStore({ form: vm.form, items: vm.items });
                        //     // update card number and remove orders from local storage
                        //     localStorage.removeItem('order');
                        // }
                        await vm.saveByStore({ form: vm.form, items: vm.items });
                        // update card number and remove orders from local storage
                        localStorage.removeItem('order');

	        			vm.$emit('update-cart', false);

	        			vm.$router.push({
	        				path: '/orders/success',
	        				query: {
	        					transaction_id: vm.orders[0].transaction_id
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
