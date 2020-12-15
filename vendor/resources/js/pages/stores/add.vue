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
	<!-- <v-dialog v-if="loading" v-model="loading" fullscreen full-width :transition="false">
		<v-container fluid fill-height>
		    <v-layout justify-center align-center>
		      	<v-progress-circular
		        	indeterminate
		        	color="primary">
		      	</v-progress-circular>
		    </v-layout>
		</v-container>
	</v-dialog> -->

	<v-container v-if="countStores < limit">
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4 primary--text">Add Store</div>
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
                        width="300"
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
                        src="https://via.placeholder.com/300/FFFFFF/000000?text=No Image"
                        width="300"
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
	  				<v-row>
	  					<v-col cols="12">
			  				<v-file-input
						    	:rules="imageRules"
						    	show-size
						    	outlined
						    	accept="image/png, image/jpeg"
						    	label="Image (JPG, JPEG, TIFF, PNG, WEBP, and BMP) 2MB"
						    	v-model="file"
						    	hide-details="auto"
						  	></v-file-input>
						</v-col>
						<v-col cols="12">
						  	<v-text-field
					      		v-model="form.name"
					      		:rules="rules"
					      		outlined
					      		label="Name"
					      		hide-details="auto"
					      		required
					    	></v-text-field>
			  			</v-col>
			  		</v-row>
			  	</v-col>
	  		</v-row>

	  		<v-row>
	  			<v-col cols="12">
			    	<v-textarea
			      		v-model="form.description"
			      		:rules="rules"
			      		outlined
			      		label="Short description about your store"
			      		required
			      		hide-details="auto"
			    	></v-textarea>
			    </v-col>
			    <v-col cols="12">
			    	<v-text-field
			      		v-model="form.social_media"
			      		label="Social Media"
			      		:rules="urlRules"
			      		outlined
			      		required
			      		hide-details="auto"
			    	></v-text-field>
			    </v-col>
			    <v-col cols="6">
			    	<v-autocomplete
			    		v-model="form.region"
				        :items="regions"
				        :rules="rules"
				        autocomplete="new-password"
				        item-value="id"
				        item-text="name"
				        label="Region"
				        outlined
				        required
				        hide-details="auto"
				        @change="findProvincesByRegion(form.region)"
				    ></v-autocomplete>
				</v-col>
				<v-col cols="6">
			    	<v-autocomplete
			    		v-model="form.province"
				        :items="provinces"
				        :rules="rules"
				        autocomplete="new-password"
				        label="Province"
				        outlined
				        required
				        hide-details="auto"
				        @change="findCitiesByProvince(form.province)"
				    ></v-autocomplete>
				</v-col>
				<v-col cols="6">
				    <v-autocomplete
			    		v-model="form.city"
				        :items="cities"
				        :rules="rules"
				        autocomplete="new-password"
				        label="City"
				        outlined
				        required
				        hide-details="auto"
				        @change="findBarangaysByProvinceCity({ provinceName: form.province, cityName: form.city })"
				    ></v-autocomplete>
				</v-col>
				<v-col cols="6">
				    <v-autocomplete
			    		v-model="form.barangay"
				        :items="barangays"
				        :rules="rules"
				        autocomplete="new-password"
				        label="Barangay"
				        outlined
				        required
				        hide-details="auto"
				    ></v-autocomplete>
				</v-col>
				<v-col cols="12">
				    <v-text-field
			      		v-model="form.street"
			      		:rules="rules"
			      		label="House #, Floor #, Street"
			      		outlined
			      		required
			      		hide-details="auto"
			    	></v-text-field>
			    </v-col>
			    <v-col cols="12">
			    	<v-select
			      		v-model="form.type"
			      		:items="deliveryTypes"
			      		:rules="rules"
			      		outlined
			      		item-value="id"
			      		item-text="name"
			      		label="Delivery Type"
			      		required
			      		hide-details="auto"
			    	></v-select>
			    </v-col>
			</v-row>

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
		            	:rules="rules"
		            	outlined
		            	hide-details="auto"
		            	required
		          	></v-text-field>
      			</v-col>
      			<v-col cols="11" sm="4">
      				<v-text-field
		            	v-model="form.schedule_time_out"
		            	label="Closing"
		            	prepend-icon="mdi-clock-time-four-outline"
		            	type="time"
		            	:rules="rules"
		            	outlined
		            	hide-details="auto"
		            	required
		          	></v-text-field>
      			</v-col>
    		</v-row>

    		<v-divider class="my-4" />

    		<v-row>
    			<v-col class="text-right">
			    	<v-btn
			      		:disabled="disabled"
			      		color="success"
			      		rounded
			      		class="px-16 py-6 mb-2"
			      		@click="submit"
			    	>
			      		Submit
			    	</v-btn>
			    </v-col>
			</v-row>
	  	</v-form>
	</v-container>

	<v-container v-else>
		<v-row>
			<v-col cols="6" class="d-flex flex-row">
				<div class="text-h4 mb-4 primary--text">Add Store</div>
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
					<v-col cols="12"><div class="text-h5 text-center text--secondary">Store limit exceeded. You are only allowed to make 1 store per vendor.</div></v-col>
			</v-row>
		</v-layout>
	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
    	data: () => ({
      		valid: true,
      		limit: 1,
      		countStores: 0,
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
      		emailRules: [
        		v => !!v || 'Field is required',
        		v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
      		],
      		checkboxRules: [
      			v => !!v || 'Atleast one schedule day is checked',
      		],
      		urlRules: [
                v => !!v || 'Field is required',
                v => /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/.test(v) || 'Url must be valid'
            ],
      		imageRules: [
      			v => !!v || 'Field is required',
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
	        deliveryTypes: [
	        	{ id: 1, name: 'Same Day Delivery' },
	        	{ id: 2, name: 'Next Day Delivery' }
	        ]
    	}),
    	async created() {
    		let vm = this;

    		await vm.fetch();

    		vm.countStores = _.filter(vm.stores, function(object) {
    			// rejected is not counted
				if (object.status != 3) return object
			}).length;

    		await vm.fetchRegions();

    		vm.loading = false;
    	},
    	computed: {
    		...mapState({
    			stores: state => state.stores.items,
    			store: state => state.stores.item,
    			regions: state => state.regions.regions,
    			provinces: state => state.provinces.provincesByRegion,
    			cities: state => state.cities.citiesByProvince,
    			barangays: state => state.barangays.barangaysByProvinceCity
    		}),
    	},
    	methods: {
    		...mapActions({
    			'fetch': 'stores/fetch',
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
        			let oldScheduleDay = vm.form.schedule_day;
        			try {
        				vm.disabled = true;

	        			// blank array returns false when joined
	        			// need to replace all false string with empty string
	        			vm.form.schedule_day = vm.form.schedule_day.join().replaceAll("false", "");
	        			vm.form.schedule_day = vm.form.schedule_day.replaceAll("true", "1");

	        			let formData = new FormData();
		 				for(let index in vm.form) {
		 					formData.append(index, vm.form[index]);
		 				}
		 				formData.append("logo", vm.file);

	        			// save store
	        			await vm.save(formData);

	        			vm.$router.push(`/stores/${ vm.store.id }`);

	        			vm.disabled = false;
	        		} catch (err) {
	        			vm.disabled = false;
	        			vm.form.schedule_day = oldScheduleDay;
	        		}
        		}
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