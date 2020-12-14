<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>
	<v-container>
		<v-row v-if="vendor">
			<v-col cols="12" sm="12" md="7" lg="7">
				<v-card
			    	class="overflow-hidden"
			  	>
			    	<v-toolbar
			      		flat
			      		color="default"
			    	>
			      		<v-toolbar-title>
			        		User Profile
			      		</v-toolbar-title>
			      		<template v-slot:extension>
				            <v-btn
				              fab
				              color="primary"
				              bottom
				              right
				              absolute
				              @click="isEditing = !isEditing"
				            >
				              <v-icon v-if="isEditing">
			          			mdi-close
			        		</v-icon>
			        		<v-icon v-else>
			          			mdi-pencil
			        		</v-icon>
				            </v-btn>
				          </template>
			    	</v-toolbar>
			    	<v-card-text>
			    		<v-form ref="form">
				      		<v-text-field
				      			v-model="vendor.first_name"
				        		:disabled="!isEditing"
				        		color="white"
				        		label="First Name"
				        		required
				                :rules="rules"
				      		></v-text-field>
				      		<v-text-field
				      			v-model="vendor.last_name"
				        		:disabled="!isEditing"
				        		color="white"
				        		label="Last Name"
				        		required
				                :rules="rules"
				      		></v-text-field>
				      		<!-- <v-text-field
				      			v-model="vendor.email"
				        		:disabled="!isEditing"
				        		color="white"
				        		label="Email"
				      		></v-text-field>
				      		<v-text-field
				      			v-model="vendor.password"
				        		:disabled="!isEditing"
				        		color="white"
				        		label="Password"
				      		>&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;</v-text-field> -->
				      		<v-autocomplete
				                v-model="vendor.region"
				                :items="regions"
				                :disabled="!isEditing"
				                autocomplete="new-password"
				                item-value="id"
				                item-text="name"
				                label="Region"
				                @change="findProvincesByRegion(vendor.region)"
				                required
				                :rules="rules"
				            ></v-autocomplete>

				            <v-autocomplete
				                v-model="vendor.province"
				                :items="provinces"
				                :disabled="!isEditing"
				                autocomplete="new-password"
				                label="Province"
				                @change="findCitiesByProvince(vendor.province)"
				                required
				                :rules="rules"
				            ></v-autocomplete>

				            <v-autocomplete
				                v-model="vendor.city"
				                :items="cities"
				                :disabled="!isEditing"
				                autocomplete="new-password"
				                label="City"
				                @change="findBarangaysByProvinceCity({ provinceName: vendor.province, cityName: vendor.city })"
				                required
				                :rules="rules"
				            ></v-autocomplete>

				            <v-autocomplete
				                v-model="vendor.barangay"
				                :items="barangays"
				                :disabled="!isEditing"
				                autocomplete="new-password"
				                label="Barangay"
				                required
				                :rules="rules"
				            ></v-autocomplete>

				            <v-text-field
				                v-model="vendor.street"
				                :disabled="!isEditing"
				                label="House #, Floor #, Street"
				                required
				                :rules="rules"
				            ></v-text-field>
				        </v-form>
			    	</v-card-text>
			    	<v-divider></v-divider>
			    	<v-card-actions>
			      		<v-spacer></v-spacer>
			      		<v-btn
			        		:disabled="!isEditing || disabled"
			        		color="primary"
			        		@click="update"
			      		>
			        		Save
			      		</v-btn>
			    	</v-card-actions>
			  	</v-card>
			</v-col>
			<v-col cols="12" sm="12" md="5" lg="5">
				<v-card>
					<v-card-text>

						<!-- MOBILE NUMBER -->
						<v-row v-if="!isMobileEditing && !isVerifyOtp">
							<v-col cols="12" sm="12" class="d-flex pb-0">
								<div>
									{{ vendor.mobile_number }}
									<div v-if="vendor.mobile_verified_at" class="text-overline green--text">
			          					<v-icon x-small color="green">mdi-check-circle</v-icon>
			          					verified
			          				</div>
									<div v-else class="text-overline red--text">
										<div>
				          					<v-icon x-small color="red">mdi-alert-circle</v-icon>
				          					not yet verified
				          				</div>
			          				</div>
								</div>
								<div class="ml-auto">
									<div
										class="primary--text row-pointer"
										@click="isMobileEditing = !isMobileEditing"
									>change mobile</div>
								</div>
							</v-col>
							<v-col v-if="!vendor.mobile_verified_at" class="text-center pt-0">
			          			<v-btn
			          				text
			          				color="primary"
			          				@click="sendOtp"
			          			>Send OTP</v-btn>

			          			<v-btn
			          				text
			          				color="primary"
			          				@click="isVerifyOtp = !isVerifyOtp"
			          			>Verify OTP</v-btn>
							</v-col>
						</v-row>
						<!-- EDIT MOBILE NUMBER -->
						<v-row v-else-if="isMobileEditing && !isVerifyOtp">
							<v-col cols="12" sm="12" class="pb-0">
								<div>
									<v-form ref="mobileForm" @submit.prevent>
										<!-- <v-text-field
							                type="hidden"
							      		></v-text-field> -->
										<v-text-field
							      			v-model="mobileNumber"
							        		label="Mobile Number"
							        		required
							        		type="text"
							                :rules="rules"
							                :disabled="disableUpdateMobileNumber"
							                :loading="disableUpdateMobileNumber"
							                @keyup.enter.prevent="updateMobile"
							      		></v-text-field>
							      	</v-form>
								</div>
								<v-alert type="warning">
									Once you update your mobile number, you will need to verify it via a code sent to your new mobile number. If you fail to verify it, all of your stores will be unavailable in ENKA.
								</v-alert>
							</v-col>
							<v-col class="text-right pt-0">
			          			<v-btn
			          				text
			          				:disabled="disableUpdateMobileNumber"
			          				color="primary"
			          				@click="updateMobile"
			          			>Save</v-btn>
			          			<v-btn
			          				text
			          				color="default"
			          				:disabled="disableUpdateMobileNumber"
			          				@click="isMobileEditing = !isMobileEditing"
			          			>Cancel</v-btn>
							</v-col>
						</v-row>
						<!-- MOBILE NUMBER UPDATED, FILL OTP -->
						<v-row v-else>
							<v-col cols="12" sm="12" class="pb-0">
								<div>
									<v-form ref="mobileForm">
										<v-text-field
							      			v-model="otp"
							        		label="OTP"
							        		type="number"
							                @paste="onPaste = true"
							                :loading="verifyingOtp"
							                :disabled="verifyingOtp"
							                @keyup="verifyOtp"
							      		></v-text-field>
							      	</v-form>
								</div>
								<v-alert type="warning">
									Your One-time Password (OTP) will expire in 5 minutes. If you didn't receive any SMS, please resend your OTP:
									<v-row no-gutters>
								      	<v-col class="text-right">
											<v-btn
												text
												color="blue darken-4"
												:disabled="sendingOtp"
												@click="sendOtp"
											>Resend OTP</v-btn>
										</v-col>
									</v-row>
								</v-alert>
							</v-col>
						</v-row>

						<!-- EMAIL -->
						<v-row v-if="!isEmailEditing">
							<v-col cols="12" sm="12" class="d-flex pb-0">
								<div>
									{{ vendor.email }}
									<div v-if="vendor.email_verified_at" class="text-overline green--text">
			          					<v-icon x-small color="green">mdi-check-circle</v-icon>
			          					verified
			          				</div>
									<div v-else class="text-overline red--text">
			          					<v-icon x-small color="red">mdi-alert-circle</v-icon>
			          					not yet verified
			          				</div>
								</div>
								<div class="ml-auto">
									<div class="primary--text row-pointer" @click="isEmailEditing = !isEmailEditing">change email</div>
								</div>
							</v-col>
							<v-col v-if="!vendor.email_verified_at" class="text-center pt-0">
			          			<v-btn
			          				text
			          				color="primary"
			          				:disabled="disableSendVerification"
			          				@click="resendVerification"
			          			>Send Verification</v-btn>
							</v-col>
						</v-row>
						<!-- EDIT EMAIL -->
						<v-row v-else>
							<v-col cols="12" sm="12" class="pb-0">
								<div>
									<v-form ref="emailForm" @submit.prevent>
										<v-text-field
							      			v-model="email"
							        		label="Email"
							        		required
							        		type="email"
							                :rules="emailRules"
							                :disabled="disableUpdateEmail"
							                :loading="disableUpdateEmail"
							                @keyup.enter.prevent="updateEmail"
							      		></v-text-field>
							      	</v-form>
								</div>
								<v-alert type="warning">
									Once you update your email, you will need to verify it. If you fail to verify, all of your stores will be unavailable in ENKA.
								</v-alert>
							</v-col>
							<v-col class="text-right pt-0">
			          			<v-btn
			          				text
			          				:disabled="disableUpdateEmail"
			          				color="primary"
			          				@click="updateEmail"
			          			>Save</v-btn>
			          			<v-btn
			          				text
			          				color="default"
			          				:disabled="disableUpdateEmail"
			          				@click="isEmailEditing = !isEmailEditing"
			          			>Cancel</v-btn>
							</v-col>
						</v-row>

						<!-- PASSWORD -->
						<v-row v-if="!isPasswordEditing">
							<v-col cols="12" sm="12" class="d-flex">
								<div>&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;</div>
								<div class="ml-auto">
									<div class="primary--text row-pointer" @click="isPasswordEditing = ! isPasswordEditing">change password</div>
								</div>
							</v-col>
						</v-row>
						<!-- EDIT PASSWORD -->
						<v-row v-else>
							<v-col cols="12" sm="12" class="pb-0">
								<div>
									<v-form ref="passwordForm" @submit.prevent>
										<v-text-field
							      			v-model="password"
							        		label="Password"
							        		type="password"
							        		required
							                :rules="passwordRules"
							                :disabled="disableUpdatePassword"
							                :loading="disableUpdatePassword"
							                @keyup.enter.prevent="updatePassword"
							      		></v-text-field>
							      		<v-text-field
		                                    id="password_confirmation"
		                                    label="Password Confirmation"
		                                    name="password_confirmation"
		                                    type="password"
		                                    v-model="confirmPassword"
		                                    required
		                                    :rules="rules.concat(passwordConfirmationRule)"
		                                    :disabled="disableUpdatePassword"
							                :loading="disableUpdatePassword"
							                @keyup.enter.prevent="updatePassword"
		                                ></v-text-field>
							      	</v-form>
								</div>
							</v-col>
							<v-col class="text-right pt-0">
			          			<v-btn
			          				text
			          				:disabled="disableUpdatePassword"
			          				color="primary"
			          				@click="updatePassword"
			          			>Save</v-btn>
			          			<v-btn
			          				text
			          				color="default"
			          				:disabled="disableUpdatePassword"
			          				@click="isPasswordEditing = !isPasswordEditing"
			          			>Cancel</v-btn>
							</v-col>
						</v-row>

					</v-card-text>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			id: null,
			isEditing: false,
			isMobileEditing: false,
			isVerifyOtp: false,
			isEmailEditing: false,
			isPasswordEditing: false,
			logoutDialog: false,
			logoutDialogDisable: false,
			logoutAllDialog: false,
			logoutAllDialogDisable: false,
			rules: [
      			v => !!v || 'Field is required',
      		],
      		emailRules: [
                v => !!v || 'Field is required',
                v => /.+@.+/.test(v) || 'E-mail must be valid',
            ],
      		passwordRules: [
                v => !!v || 'Field is required',
                v => (v && v.length >= 8) || "Field must be at least 8 characters long"
            ],
      		disabled: false,
      		disableSendVerification: false,
      		disableUpdateMobileNumber: false,
      		disableUpdateEmail: false,
      		disableUpdatePassword: false,
      		mobileNumber: null,
      		otp: null,
      		onPaste: false,
      		verifyingOtp: false,
      		sendingOtp: false,
      		email: null,
      		password: null,
      		confirmPassword: null,
		}),
		async created() {
			let vm = this;
			
			await vm.fetchVendor();

			await vm.fetchRegions();
    		await vm.findProvincesByRegion(vm.vendor.region);
            await vm.findCitiesByProvince(vm.vendor.province);
            await vm.findBarangaysByProvinceCity({ provinceName: vm.vendor.province, cityName: vm.vendor.city });
		},
		computed: {
            ...mapState({
            	tokens: state => state.currentVendor.tokens,
                vendor: state => state.currentVendor.vendor,
                resent: state => state.verification.resent,
                regions: state => state.regions.regions,
    			provinces: state => state.provinces.provincesByRegion,
    			cities: state => state.cities.citiesByProvince,
    			barangays: state => state.barangays.barangaysByProvinceCity
            }),
            passwordConfirmationRule() {
                return () =>
                    this.password === this.confirmPassword || "Password must match";
            }
        },
        methods: {
            ...mapActions({
            	'resend': 'verification/resend',
            	'fetchVendor': 'currentVendor/getVendor',
            	'fetchTokens': 'currentVendor/getVendorTokens',
            	'logoutDevice': 'currentVendor/logoutDevice',
            	'logoutAll': 'currentVendor/logoutAll',
            	'updateVendor': 'currentVendor/updateVendor',
            	'verify': 'otps/verify',
            	'send': 'otps/send',
            	'fetchRegions': 'regions/fetch',
    			'findProvincesByRegion': 'provinces/findByRegion',
    			'findCitiesByProvince': 'cities/findByProvince',
    			'findBarangaysByProvinceCity': 'barangays/findByProvinceCity',
            }),
            openLogoutDialog(id) {
            	let vm = this;

            	vm.id = id;
            	vm.logoutDialog = true;
            },
            async update() {
            	let vm = this;

            	let valid = vm.$refs.form.validate();
            	if (valid) {
	            	try {
	            		vm.disabled = true;

	            		// initialize the form
	        			let form = _.pick(vm.vendor, [
	        				'first_name',
	        				'last_name',
	        				'region',
	        				'province',
	        				'city',
	        				'barangay',
	        				'street'
	        			]);

	            		await vm.updateVendor(form);

	            		vm.isEditing = false;
	            		vm.disabled = false;
	            	} catch (err) {
	            		vm.isEditing = false;
	            		vm.disabled = false;
	            	}
	            }
            },
            async updateMobile() {
            	let vm = this;

            	let valid = vm.$refs.mobileForm.validate();
            	if (valid) {
	            	try {
	            		vm.disableUpdateMobileNumber = true;

	            		let form = {
	            			mobile_number: vm.mobileNumber
	            		}

	            		await vm.updateVendor(form);

	            		vm.mobileNumber = null;
	            		vm.isVerifyOtp = true;
	            		vm.isMobileEditing = false;
	            		vm.disableUpdateMobileNumber = false;
	            	} catch (err) {
	            		vm.isMobileEditing = false;
	            		vm.disableUpdateMobileNumber = false;
	            	}
	            }
            },
            async verifyOtp(evt) {
            	let vm = this;

            	try {
            		var re = new RegExp("^([0-9]{6})$");

            		// if 6 digits, verify
					if (re.test(evt.target.value)) {
						vm.verifyingOtp = true;

	            		let form = {
            				'mobile_number': vm.vendor.mobile_number,
	            			'otp': vm.otp
	            		};
	            		await vm.verify(form);

	            		vm.otp = null;
	            		vm.isVerifyOtp = false;
	            		vm.verifyingOtp = false;

	            		vm.fetchVendor();
            		}
            	} catch (err) {
            		vm.verifyingOtp = false;
            	}
            },
            async sendOtp() {
            	let vm = this;

            	try {
					vm.sendingOtp = true;

            		let form = {
        				'mobile_number': vm.vendor.mobile_number
            		};
            		await vm.send(form);

            		vm.sendingOtp = false;
            	} catch (err) {
            		vm.sendingOtp = false;
            	}
            },
            async updateEmail() {
            	let vm = this;

            	let valid = vm.$refs.emailForm.validate();
            	if (valid) {
	            	try {
	            		vm.disableUpdateEmail = true;

	            		let form = {
	            			email: vm.email
	            		}
	            		await vm.updateVendor(form);

	            		vm.email = null;
	            		vm.disableUpdateEmail = false;
	            		vm.isEmailEditing = false;
	            	} catch (err) {
	            		vm.disableUpdateEmail = false;
	            	}
	            }
            },
            async updatePassword() {
            	let vm = this;

            	let valid = vm.$refs.passwordForm.validate();
            	if (valid) {
	            	try {
	            		vm.disableUpdatePassword = true;

	            		let form = {
	            			password: vm.password,
	            			password_confirmation: vm.confirmPassword
	            		}

	            		await vm.updateVendor(form);

	            		vm.password = null;
	            		vm.confirmPassword = null
						vm.isPasswordEditing = false;
	            		vm.disableUpdatePassword = false;
	            	} catch (err) {
	            		vm.disableUpdatePassword = false;
	            	}
	            }
            },
            async resendVerification() {
				let vm = this;

				try {
					vm.disableSendVerification = true;

					let id = vm.vendor.id;
					let email = vm.vendor.email;

					await vm.resend({ id: id, email: email });

					vm.disableSendVerification = false;
				} catch (err) {
					vm.disableSendVerification = false;
				}
			},
            async logout() {
            	let vm = this;
            	try {
            		vm.logoutDialogDisable = true;

            		await vm.logoutDevice(vm.id);

            		vm.logoutDialogDisable = true;
            		vm.logoutDialog = false;

            		vm.fetchTokens();
            	} catch (err) {
            		console.log(err);
            	}
            },
            async logoutAllDevices() {
            	let vm = this;
            	try {
            		vm.logoutAllDialogDisable = true;

            		await vm.logoutAll(vm.id);

            		vm.logoutAllDialogDisable = true;
            		vm.logoutAllDialog = false;

            		vm.fetchTokens();
            	} catch (err) {
            		console.log(err);
            	}
            }
        }
	}
</script>

<style lang="css" scoped>
	.row-pointer {
	  cursor: pointer;
	}
	.align-center {
		margin: 0px auto !important;
	}
</style>