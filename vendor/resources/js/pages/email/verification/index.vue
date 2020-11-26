<template>
	<v-container>
		<!-- resent verification -->
		<div v-if="resent">
			{{ resent }}
			<v-btn @click="resendVerification">Resend</v-btn>
		</div>

		<!-- logged out but verified thru email link -->
		<div v-else-if="Object.entries(verification).length">
			<div v-if="verification.success">
				{{ verification.message }}
				<router-link v-if="!vendor" to="/login">Login</router-link>
				<router-link v-else to="/">Home</router-link>
			</div>
			<div v-else>
				{{ verification.message }}
				<div v-if="alreadyVerified">
					<router-link v-if="!vendor" to="/login">Login</router-link>
					<router-link v-else to="/">Home</router-link>
				</div>
				<div v-else-if="verificationError">
					<v-btn @click="resendVerification">Resend</v-btn>
				</div>
				<div v-else>
					Invalid link provided.
				</div>
			</div>
		</div>

		<!-- logged in -->
		<div v-else-if="vendor">
			<div v-if="vendor.email_verified_at">
				Already verified
				<router-link to="/">Home</router-link>
			</div>
			<div v-else>
				Please verify your email:
				<v-btn @click="resendVerification">Resend</v-btn>
			</div>
		</div>

		<!-- invalid link -->
		<div v-else>
			Invalid link provided
		</div>

	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			alreadyVerified: false,
			verificationError: false,
			vendor: JSON.parse(localStorage.getItem('current_vendor'))
		}),
		async created() {
			let vm = this;

			if (vm.$route.query.queryUrl && vm.$route.query.email) {
				try {
					let url = vm.$route.query.queryUrl;
					await vm.verify(url);
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
		},
		computed: {
			...mapState('verification', {
				verification: state => state.verification,
				resent: state => state.resent
			}),
		},
		methods: {
			...mapActions('verification', ['verify', 'resend']),
			async resendVerification() {
				let vm = this;

				try {
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
				} catch (err) {
					if (err.response && err.response.status == 422) {
						vm.alreadyVerified = true;
					}
				}
			}
		}
	}
</script>