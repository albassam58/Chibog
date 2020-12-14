<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>
	<!-- without v-dialog, container is not centered vertically -->
	<!-- if you know how, feel free to change this -->
	<v-dialog v-if="loading" v-model="loading" fullscreen full-width :transition="false">
		<v-container fluid fill-height>
		    <v-layout justify-center align-center>
		      	<v-progress-circular
		        	indeterminate
		        	color="primary">
		      	</v-progress-circular>
		    </v-layout>
		</v-container>
	</v-dialog>

	<v-container v-else-if="store">
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4">Edit Store</div>
			</v-col>
			<v-col cols="6" class="d-flex flex-row-reverse">
				<v-btn color="default" @click="$router.back(-1)" text>
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
	  		<v-row>
	  			<v-col
	  				cols="12"
	  				sm="4"
	  				md="3"
	  				lg="2"
        			class="flex-grow-0 flex-shrink-0"
        		>
	  				<v-img
	  					v-if="file"
                        :src="fileUrl"
                        width="150"
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
                    <v-img
	  					v-else
                        :src="imageLink(form)"
                        width="150"
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
	  			</v-col>
	  			<v-col>
	  				<v-file-input
				    	:rules="imageRules"
				    	show-size
				    	accept="image/png, image/jpeg"
				    	label="Image (JPG, JPEG, TIFF, PNG, WEBP, and BMP) 2MB"
				    	v-model="file"
				  	></v-file-input>
				  	<v-text-field
			      		v-model="form.name"
			      		:rules="rules"
			      		label="Name"
			      		required
			    	></v-text-field>
	  			</v-col>
	  		</v-row>
	    	
	    	<v-textarea
	      		v-model="form.description"
	      		:rules="rules"
	      		label="Short description about your store"
	      		required
	    	></v-textarea>

	    	<v-text-field
	      		v-model="form.social_media"
	      		label="Social Media"
	      		:rules="urlRules"
	      		required
	    	></v-text-field>

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
			      		:rules="checkboxRules"
			      		label="Mon"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[1]"
			      		:rules="checkboxRules"
			      		label="Tue"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[2]"
			      		:rules="checkboxRules"
			      		label="Wed"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[3]"
			      		:rules="checkboxRules"
			      		label="Thu"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[4]"
			      		:rules="checkboxRules"
			      		label="Fri"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[5]"
			      		:rules="checkboxRules"
			      		label="Sat"
			      		required
			    	></v-checkbox>
			    </v-col>
			    <v-col cols="12" sm="2" md="1">
			    	<v-checkbox
			      		v-model="form.schedule_day[6]"
			      		:rules="checkboxRules"
			      		label="Sun"
			      		required
			    	></v-checkbox>
			    </v-col>
			</v-row>

			<label>Schedule Time</label>

			<v-row>
      			<v-col cols="11" sm="4">
		          	<v-text-field
		            	v-model="form.schedule_time_in"
		            	label="Opening"
		            	prepend-icon="mdi-clock-time-four-outline"
		            	type="time"
		          	></v-text-field>
      			</v-col>
      			<v-col cols="11" sm="4">
      				<v-text-field
		            	v-model="form.schedule_time_out"
		            	label="Closing"
		            	prepend-icon="mdi-clock-time-four-outline"
		            	type="time"
		          	></v-text-field>
      			</v-col>
    		</v-row>

	    	<v-btn
	      		:disabled="disabled"
	      		color="success"
	      		class="mr-4"
	      		@click="submit"
	    	>
	      		Submit
	    	</v-btn>
	  	</v-form>
	</v-container>

	<v-container fill-height v-else>
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4">Edit Store</div>
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
				<v-col cols="12"><div class="text-h5 text-center text--secondary">The store you were looking for does not exist</div></v-col>
			</v-row>
		</v-layout>
	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
    	data: () => ({
      		valid: true,
      		loading: true,
      		disabled: false,
      		form: {
      			schedule_day: []
      		},
      		file: null,
      		fileUrl: null,
      		rules: [
      			v => !!v || 'Field is required',
      		],
      		urlRules: [
                v => !!v || 'Field is required',
                v => /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/.test(v) || 'Url must be valid'
            ],
      		imageRules: [
      			v => !v || v.size < 2000000 || 'Image size should be less than 2 MB!',
      			v => {
      				if (v) {
	      				if (!(/\.(jpe?g|tiff?|png|webp|bmp)$/.test(v.name))) {
	      					return 'Image should be a type of JPG, JPEG, TIFF, PNG, WEBP, and BMP'
	      				}
	      			}

	      			return true;
      			}
      		],
      		checkboxRules: [
      			v => !!v || 'Atleast one schedule day is checked',
      		],
	        deliveryTypes: [
	        	{ id: 1, name: 'Same Day Delivery' },
	        	{ id: 2, name: 'Next Day Delivery' }
	        ],
    	}),
    	async created() {
    		let vm = this;

    		await vm.find(vm.$route.params.id);

    		if (!vm.store) return vm.loading = false;

    		await vm.fetchRegions();
    		await vm.findProvincesByRegion(vm.store.region);
            await vm.findCitiesByProvince(vm.store.province);
            await vm.findBarangaysByProvinceCity({ provinceName: vm.store.province, cityName: vm.store.city });

    		vm.form = vm.store;

    		let timeIn = vm.form.schedule_time_in.split(":");
    		let timeOut = vm.form.schedule_time_out.split(":");

    		vm.form.schedule_day = vm.form.schedule_day.split(",");
    		vm.form.schedule_time_in = timeIn[0] + ":" + timeIn[1];
    		vm.form.schedule_time_out = timeOut[0] + ":" + timeOut[1];

    		vm.loading = false;
    	},
    	computed: {
    		...mapState({
    			store: state => state.stores.item,
    			regions: state => state.regions.regions,
    			provinces: state => state.provinces.provincesByRegion,
    			cities: state => state.cities.citiesByProvince,
    			barangays: state => state.barangays.barangaysByProvinceCity
    		}),
    	},
    	methods: {
    		...mapActions({
    			'update': 'stores/update',
    			'find': 'stores/find',
    			'fetchRegions': 'regions/fetch',
    			'findProvincesByRegion': 'provinces/findByRegion',
    			'findCitiesByProvince': 'cities/findByProvince',
    			'findBarangaysByProvinceCity': 'barangays/findByProvinceCity',
    		}),
      		async submit () {
      			let vm = this;

        		let valid = vm.$refs.form.validate();
        		if (valid) {
        			let oldScheduleDay = vm.form.schedule_day;
        			try {
        				vm.disabled = true;

	        			// blank array returns false when joined
	        			// need to replace all false string with empty string
	        			vm.form.schedule_day = vm.form.schedule_day.join().replaceAll("false", "");
	        			vm.form.schedule_day = vm.form.schedule_day.replaceAll("true", "1");

	        			// initialize the form
	        			let form = _.pick(vm.form, [
	        				'id',
	        				'name',
	        				'description',
	        				'social_media',
	        				'region',
	        				'province',
	        				'city',
	        				'barangay',
	        				'street',
	        				'type',
	        				'schedule_day',
	        				'schedule_time_in',
	        				'schedule_time_out'
	        			]);

	        			let formData = new FormData();
		 				for(let index in form) {
		 					formData.append(index, form[index]);
		 				}

		 				if (vm.file) {
		 					formData.append("logo", vm.file);
		 				}

		 				// method spoofing	
		 				formData.append("_method", "PUT");

	        			await vm.update(formData);

	        			vm.$router.push(`/stores/${ vm.store.id }`);

	        			vm.disabled = false;
	        		} catch (err) {
	        			vm.disabled = false;
	        			vm.form.schedule_day = oldScheduleDay;
	        		}
        		}
      		},
      		imageLink(item) {
        		let vm = this;
        		let placeholder = "https://via.placeholder.com/300/FFFFFF/000000?text=No Image";

        		return item.logo ? `/${ item.logo }` : placeholder;
        	}
    	},
    	watch: {
    		file(val) {
    			if (val)
			      // const file = e.target.files[0];
			      this.fileUrl = URL.createObjectURL(val);
    		},
    		'form.schedule_day'(val) {
    			let vm = this;

    			if (typeof val == "object") {
	    			if (val.filter(Boolean).length) {
	    				vm.checkboxRules = [];
	    			} else {
	    				vm.checkboxRules = [
			      			v => !!v || 'Atleast one schedule day is checked',
			      		];
	    			}
	    		}
    		}
    	}
  	}
</script>