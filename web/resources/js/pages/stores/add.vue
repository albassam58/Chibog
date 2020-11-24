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
				<div class="text-h4 mb-4">Add Store</div>
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="secondary" dark @click="$router.back(-1)" text>
					<v-icon>mdi-arrow-left</v-icon>
					Back
				</v-btn>
			</v-col>
		</v-row>
	  	<v-form
	    	ref="form"
	    	v-model="valid"
	    	lazy-validation
	  	>
	  		<DropImage />

	    	<v-text-field
	      		v-model="form.name"
	      		:rules="rules"
	      		label="Name"
	      		required
	    	></v-text-field>

	    	<v-textarea
	      		v-model="form.description"
	      		:rules="rules"
	      		label="Short description about your store"
	      		required
	    	></v-textarea>

	    	<v-autocomplete
	    		v-model="form.region"
		        :items="regions"
		        :rules="rules"
		        autocomplete="new-password"
		        item-value="id"
		        item-text="name"
		        label="Region"
		        required
		        @change="findProvincesByRegion(form.region)"
		    ></v-autocomplete>

	    	<v-autocomplete
	    		v-model="form.province"
		        :items="provinces"
		        :rules="rules"
		        autocomplete="new-password"
		        label="Province"
		        required
		        @change="findCitiesByProvince(form.province)"
		    ></v-autocomplete>

		    <v-autocomplete
	    		v-model="form.city"
		        :items="cities"
		        :rules="rules"
		        autocomplete="new-password"
		        label="City"
		        required
		        @change="findBarangaysByProvinceCity({ provinceName: form.province, cityName: form.city })"
		    ></v-autocomplete>

		    <v-autocomplete
	    		v-model="form.barangay"
		        :items="barangays"
		        :rules="rules"
		        autocomplete="new-password"
		        label="Barangay"
		        required
		    ></v-autocomplete>

		    <v-text-field
	      		v-model="form.street"
	      		:rules="rules"
	      		label="House #, Floor #, Street"
	      		required
	    	></v-text-field>

	    	<v-select
	      		v-model="form.type"
	      		:items="deliveryTypes"
	      		:rules="rules"
	      		item-value="id"
	      		item-text="name"
	      		label="Delivery Type"
	      		required
	    	></v-select>

	    	<label>Schedule Day</label>

	    	<v-row>
	    		<v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[0]"
			      		label="Mon"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[1]"
			      		label="Tue"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[2]"
			      		label="Wed"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[3]"
			      		label="Thu"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[4]"
			      		label="Fri"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[5]"
			      		label="Sat"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[6]"
			      		label="Sun"
			      		required
			    	></v-checkbox>
			    </v-col>
			</v-row>

			<label>Schedule Time</label>

			<v-row>
      			<v-col cols="11" sm="4">
        			<v-menu
				        ref="in"
				        v-model="timeInMenu"
				        :close-on-content-click="false"
				        :nudge-right="40"
				        :return-value.sync="form.schedule_time_in"
				        transition="scale-transition"
				        offset-y
				        max-width="290px"
				        min-width="290px"
				     >
				        <template v-slot:activator="{ on, attrs }">
				          <v-text-field
				            v-model="form.schedule_time_in"
				            label="Opening"
				            prepend-icon="mdi-clock-time-four-outline"
				            readonly
				            v-bind="attrs"
				            v-on="on"
				          ></v-text-field>
				        </template>
				        <v-time-picker
				          v-if="timeInMenu"
				          v-model="form.schedule_time_in"
				          full-width
				          @click:minute="$refs.in.save(form.schedule_time_in)"
				        ></v-time-picker>
				     </v-menu>
      			</v-col>
      			<v-col cols="11" sm="4">
        			<v-menu
				        ref="out"
				        v-model="timeOutMenu"
				        :close-on-content-click="false"
				        :nudge-right="40"
				        :return-value.sync="form.schedule_time_out"
				        transition="scale-transition"
				        offset-y
				        max-width="290px"
				        min-width="290px"
				     >
				        <template v-slot:activator="{ on, attrs }">
				          <v-text-field
				            v-model="form.schedule_time_out"
				            label="Closing"
				            prepend-icon="mdi-clock-time-four-outline"
				            readonly
				            v-bind="attrs"
				            v-on="on"
				          ></v-text-field>
				        </template>
				        <v-time-picker
				          v-if="timeOutMenu"
				          v-model="form.schedule_time_out"
				          full-width
				          @click:minute="$refs.out.save(form.schedule_time_out)"
				        ></v-time-picker>
				     </v-menu>
      			</v-col>
    		</v-row>

	    	<v-btn
	      		:disabled="!valid"
	      		color="success"
	      		class="mr-4"
	      		@click="submit"
	    	>
	      		Submit
	    	</v-btn>
	  	</v-form>
	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import DropImage from '../../components/DropImageComponent.vue'

	export default {
		components: { DropImage },
    	data: () => ({
      		valid: true,
      		form: {
      			schedule_day: []
      		},
      		rules: [
      			v => !!v || 'Field is required',
      		],
      		// nameRules: [
         	//	v => !!v || 'Name is required',
         	//	v => (v && v.length <= 10) || 'Name must be less than 10 characters',
      		// ],
      		emailRules: [
        		v => !!v || 'Field is required',
        		v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
      		],
      		checkboxRules: [v => !!v || 'You must agree to continue!'],
	        deliveryTypes: [
	        	{ id: 1, name: 'Same Day Delivery' },
	        	{ id: 2, name: 'Next Day Delivery' }
	        ],
      		checkbox: false,
      		timeInMenu: false,
      		timeOutMenu: false
    	}),
    	async created() {
    		let vm = this;

    		await vm.fetchRegions();
    	},
    	computed: {
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
    			'save': 'stores/save',
    			'fetchRegions': 'regions/fetch',
    			'findProvincesByRegion': 'provinces/findByRegion',
    			'findCitiesByProvince': 'cities/findByProvince',
    			'findBarangaysByProvinceCity': 'barangays/findByProvinceCity',
    		}),
      		async submit () {
      			let vm = this;

        		let valid = vm.$refs.form.validate();
        		if (valid) {
        			// save store
        			await vm.save(vm.form);
        		}
      		},
      		// reset () {
        // 		this.$refs.form.reset()
      		// },
      		// resetValidation () {
        // 		this.$refs.form.resetValidation()
      		// }
    	}
  	}
</script>