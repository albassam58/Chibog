<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>
	<div>
		<v-container>
			<v-row>
				<v-col cols="12">
					<div class="text-h4 mb-4">Verification</div>
				</v-col>
			</v-row>
			<v-stepper v-model="step" v-if="vendor">
            	<v-stepper-header>
              		<v-stepper-step step="1" :complete="step > 1 || vendor.mobile_verified_at !== null">
              			Mobile Verification
              			<!-- <small v-if="step > 1" class="green--text">Verified</small> -->
              		</v-stepper-step>

              		<v-divider></v-divider>

              		<v-stepper-step step="2" :complete="step > 2 || vendor.email_verified_at !== null">
              			Email Verification
              		</v-stepper-step>

              		<v-divider></v-divider>

              		<!-- <v-stepper-step step="3">Success</v-stepper-step> -->
            	</v-stepper-header>

            	<v-stepper-items>
		            <v-stepper-content step="1">
		            	<div class="text-center">
		                	<v-form ref="otpForm" @submit.prevent>
		                		<v-row>
		                			<v-col>
		                				To ensure that you provide a valid mobile number, we need you to verify your mobile number. We sent you an SMS.
		                			</v-col>
		                		</v-row>
		                		<v-row class="mt-4">
		                			<v-col cols="12" sm="6" md="4" offset-md="4">
										<v-text-field
										 	height="60"
							      			v-model="otp"
							        		label="OTP"
							        		outlined
							        		type="text"
							                @paste="onPaste = true"
							                :loading="verifyingOtp"
							                :disabled="verifyingOtp"
							                @keyup="verifyOtp"
							      		></v-text-field>
							      		<div>
		                					Didn't receive any code?
		                				</div>
		                				<v-btn
											color="primary"
											:disabled="sendingOtp"
											@click="sendOtp"
										>Resend OTP</v-btn>
							      	</v-col>
							    </v-row>
					      	</v-form>
		                </div>
		            </v-stepper-content>

            		<v-stepper-content step="2">
            			<div class="text-center" v-if="verificationError && !alreadyVerified">
		                	<v-row>
	                			<v-col>
	                				<div>
	                					Verification Error
	                				</div>
	                				<div>
	                					Either invalid or expired link.
	                				</div>
	                			</v-col>
	                		</v-row>
	                		<v-row class="mt-4">
	                			<v-col>
	                				<v-btn
										color="primary"
										:disabled="sendingEmail"
										@click="resendVerification"
									>Resend Email</v-btn>
						      	</v-col>
						    </v-row>
		                </div>
		                <div class="text-center" v-else-if="verificationSuccess">
		                	<v-row>
	                			<v-col>
	                				<div>
	                					Successfully Verified
	                				</div>
	                				<div>
	                					You are now set. Enjoy using ENKA!
	                				</div>
	                				<v-btn
	                					class="mt-12"
										color="primary"
										@click="$router.push('/')"
									>Return to Home</v-btn>
	                			</v-col>
	                		</v-row>
		                </div>
                  		<div class="text-center" v-else-if="!alreadyVerified">
	                		<v-row>
	                			<v-col>
	                				To ensure that you provide a valid email, you need to verify your email. We sent you an email verification.
	                			</v-col>
	                		</v-row>
	                		<v-row class="mt-4">
	                			<v-col>
						      		<div>
	                					Didn't receive any email?
	                				</div>
	                				<v-btn
										color="primary"
										:disabled="sendingEmail"
										@click="resendVerification"
									>Resend Email</v-btn>
						      	</v-col>
						    </v-row>
		                </div>
		                <div class="text-center" v-else>
		                	<v-row>
	                			<v-col>
	                				<div>
	                					Email is already verified.
	                				</div>
	                				<div>
	                					You are now set. Enjoy using ENKA!
	                				</div>
	                				<v-btn
	                					class="mt-12"
										color="primary"
										@click="$router.push('/')"
									>Return to Home</v-btn>
	                			</v-col>
	                		</v-row>
		                </div>
              		</v-stepper-content>

              		<!-- <v-stepper-content step="3">
                		<div>
		                	step 3
		                </div>

                		<v-btn text @click.native="step = 2">Previous</v-btn>
              		</v-stepper-content> -->
            	</v-stepper-items>
          	</v-stepper>
      	</v-container>


		<!-- resent verification -->
		<!-- <v-container
			fill-height
			v-if="resent"
		>
			<v-layout row wrap align-center>
				<v-row>
					<v-col cols="12">
						<div class="text-h2 text-center">Email Verification</div>
					</v-col>
					<v-col cols="12">
						<div class="text-h5 text-center text--secondary">
							{{ resent }}
						</div>
					</v-col>
					<v-col cols="12">
						<v-btn
							@click="resendVerification"
							color="primary"
						>Resend</v-btn>
					</v-col>
				</v-row>
			</v-layout>
		</v-container> -->

		<!-- logged out but verified thru email link -->
		<!-- <v-container
			fill-height
			v-else-if="Object.entries(verification).length"
		>
			<v-layout row wrap align-center>
				<v-row v-if="verification.success">
					<v-col cols="12">
						<div class="text-h2 text-center">Email Verification</div>
					</v-col>
					<v-col cols="12">
						<div class="text-h5 text-center text--secondary">
							{{ verification.message }}
						</div>
					</v-col>
					<v-col cols="12">
						<v-btn
							v-if="!vendor"
							color="primary"
							@click="$router.push('/login')"
						>Login</v-btn>
						<v-btn
							v-else
							color="primary"
							@click="$router.push('/')"
						>Home</v-btn>
					</v-col>
				</v-row>
				<v-row v-else>
					<v-col cols="12">
						<div class="text-h2 text-center">Email Verification</div>
					</v-col>
					<v-col cols="12">
						<div class="text-h5 text-center text--secondary">
							{{ verification.message }}
						</div>
					</v-col>
					<v-col cols="12" v-if="alreadyVerified">
						<v-btn
							v-if="!vendor"
							color="primary"
							@click="$router.push('/login')"
						>Login</v-btn>
						<v-btn
							v-else
							color="primary"
							@click="$router.push('/')"
						>Home</v-btn>
					</v-col>
					<v-col cols="12" v-else-if="verificationError">
						<v-btn
							@click="resendVerification"
							color="primary"
						>Resend</v-btn>
					</v-col>
					<v-col cols="12" v-else>
						<div class="text-h5 text-center text--secondary">
							Invalid link provided.
						</div>
					</v-col>
				</v-row>
			</v-layout>
		</v-container> -->

		<!-- logged in -->
		<!-- <v-container
			fill-height
			v-else-if="vendor"
		>
			<v-layout row wrap align-center>
				<v-row v-if="vendor.email_verified_at">
					<v-col cols="12">
						<div class="text-h2 text-center">Email Verification</div>
					</v-col>
					<v-col cols="12">
						<div class="text-h5 text-center text--secondary">
							Already verified
						</div>
					</v-col>
					<v-col cols="12">
						<v-btn
							color="primary"
							@click="$router.push('/')"
						>Home</v-btn>
					</v-col>
				</v-row>
				<v-row v-else>
					<v-col cols="12">
						<div class="text-h2 text-center">Email Verification</div>
					</v-col>
					<v-col cols="12">
						<div class="text-h5 text-center text--secondary">
							Please verify your email
						</div>
					</v-col>
					<v-col cols="12">
						<v-btn
							@click="resendVerification"
							color="primary"
						>Resend</v-btn>
					</v-col>
				</v-row>
			</v-layout>
		</v-container> -->

		<!-- invalid link -->
		<!-- <v-container
			fill-height
			v-else
		>
			<v-layout row wrap align-center>
				<v-row>
					<v-col cols="12">
						<div class="text-h2 text-center">Email Verification</div>
					</v-col>
					<v-col cols="12">
						<div class="text-h5 text-center text--secondary">
							Invalid link provided
						</div>
					</v-col>
				</v-row>
			</v-layout>
		</v-container> -->

	</div>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			step: 1,
			onPaste: false,
			otp: null,
			verifyingOtp: false,
			sendingOtp: false,
			sendingEmail: false,
			alreadyVerified: false,
			verificationSuccess: false,
			verificationError: false
		}),
		async created() {
			let vm = this;

			await vm.fetchVendor();

			if (vm.$route.query.queryUrl && vm.$route.query.email) {
				try {
					let url = vm.$route.query.queryUrl;
					await vm.verify(url);
					vm.verificationSuccess = true;
				} catch (err) {
					if (err.response.status == 403) {
						// verification error
						vm.verificationError = true;
					} else if (err.response.status == 422) {
						// already verified
						vm.alreadyVerified = true;
					}
				}
			} else if (!vm.vendor) {
				vm.$router.push('/login');
			}

			// mobile is not yet verified
			if (!vm.vendor.mobile_verified_at) {
				vm.step = 1;
			} else {
				vm.step = 2;
			}

			if (vm.vendor.email_verified_at) vm.alreadyVerified = true;
		},
		computed: {
			...mapState({
				verification: state => state.verification.verification,
				resent: state => state.verification.resent,
				vendor: state => state.currentVendor.vendor
			}),
		},
		methods: {
			...mapActions({
				'fetchVendor': 'currentVendor/getVendor',
				'verify': 'verification/verify',
				'resend': 'verification/resend',
				'otpVerify': 'otps/verify',
            	'otpSendSms': 'otps/send',
			}),
			async resendVerification() {
				let vm = this;

				try {
					vm.sendingEmail = true;

					let id, email;
					if (vm.vendor) {
						id = vm.vendor.id;
						email = vm.vendor.email;
					} else {
						email = vm.$route.query.email;
						let url = vm.$route.query.queryUrl;
						let splitUrl = url.split("/");
						id = splitUrl[splitUrl.length - 1];
						id = id.split("?")[0];
					}

					await vm.resend({ id: id, email: email });

					vm.sendingEmail = false;
				} catch (err) {
					vm.sendingEmail = false;
					if (err.response && err.response.status == 422) {
						vm.alreadyVerified = true;
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
	            		await vm.otpVerify(form);

	            		vm.otp = null;
	            		vm.verifyingOtp = false;

	            		vm.step = 2;
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
            		await vm.otpSendSms(form);

            		vm.sendingOtp = false;
            	} catch (err) {
            		vm.sendingOtp = false;
            	}
			}
		}
	}
</script>

<style scoped>
  .v-text-field input {
    font-size: 3em !important;
  }
</style>