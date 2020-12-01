<template>
    <v-app>
        <div>
            <v-app-bar
                color="deep-purple accent-4"
                dense
                dark>
                <!-- <v-app-bar-nav-icon></v-app-bar-nav-icon> -->
                <v-toolbar-title>
                    <router-link
                        to="/"
                        class="white--text text-decoration-none"
                    >
                        Chibog
                    </router-link>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <div v-if="authenticated && vendor.email_verified_at">
                    <order-notification ref="orderNotificationRef"></order-notification>
                </div>
                <div>
                    <v-tooltip
                        v-if="!$vuetify.theme.dark"
                        bottom
                    >
                        <template v-slot:activator="{ on }">
                            <v-btn v-on="on" color="info" icon @click="darkMode">
                                <v-icon class="mr-1">mdi-moon-waxing-crescent</v-icon>
                            </v-btn>
                        </template>
                        <span>Dark Mode On</span>
                    </v-tooltip>

                    <v-tooltip
                        v-else
                        bottom
                    >
                        <template v-slot:activator="{ on }">
                            <v-btn v-on="on" color="info" icon @click="darkMode">
                                <v-icon color="yellow">mdi-white-balance-sunny</v-icon>
                            </v-btn>
                        </template>
                        <span>Dark Mode Off</span>
                    </v-tooltip>
                </div>

                <v-menu
                    v-if="authenticated"
                    left
                    bottom
                >
                    <template
                        v-slot:activator="{ on, attrs }">
                        <v-btn
                            icon
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item>
                            <v-list-item-title>
                                <v-icon>mdi-account</v-icon>
                                {{ vendor.first_name }}
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="$router.push('/orders')">
                            <v-list-item-title>
                                <v-icon>mdi-cart</v-icon>
                                Orders
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="$router.push('/statistics')">
                            <v-list-item-title>
                                <v-icon>mdi-chart-box</v-icon>
                                Statistics
                            </v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                        <v-list-item @click="logout">
                            <v-list-item-title>
                                <v-icon class="red--text text-darken-4">mdi-logout</v-icon>
                                <span class="red--text text-darken-4">Logout</span>
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
                <span v-else>
                    <router-link
                        to="/register"
                        class="white--text text-decoration-none"
                    >Register</router-link>
                    <router-link
                        to="/login"
                        class="white--text text-decoration-none"
                    >Login</router-link>
                </span>
            </v-app-bar>

            <!--  v-if="!Object.entries(vendor).length || (Object.entries(vendor).length && vendor.email_verified_at && !$route.query.queryUrl)" -->
            <router-view :key="$route.fullPath" />

            <!-- <div v-else-if="alreadyVerified">
                Already verified!
            </div>

            <div v-else>
                <div v-if="Object.entries(verification).length">
                    Email successfully sent
                    <v-btn @click="resendVerification">Resend</v-btn>
                </div>
                <div v-else>
                    Please verify your email:

                    <v-btn @click="resendVerification">Resend</v-btn>
                </div>
            </div> -->
        </div>
    </v-app>
</template>
<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'
    import OrderNotification from '@components/OrderNotificationComponent'

	export default {
        components: {
            OrderNotification
        },
		data: () => ({
            alreadyVerified: false,
		}),
		async created() {
			let vm = this;
            let darkMode = localStorage.getItem('darkMode');

			await vm.getVendor();

            // check if email is verified, if not, redirect to email verification
            if (vm.vendor && !vm.vendor.email_verified_at) {
                if (vm.$route.path != '/email/verification') {
                    vm.$router.push('/email/verification');
                }
            }

            // set dark mode
            vm.$vuetify.theme.dark = darkMode === 'true' ? true : false;

            if (vm.vendor) {
                // Connect to Socket.io
                let socket = io(`http://localhost:3000`);

                // ... listen for new events/messages
                socket.on(`order-${ vm.vendor.id }:App\\Events\\Order`, data => {
                    vm.fetchNotificationVendor();
                    vm.$refs.orderNotificationRef.updateOrderNotification(data.data);
                    console.log(data.data);
                    // if (this.activeChannel == channel.id) {
                    //     this.messages.push(data.data);
                    // }
                });
            }
		},
		computed: {
			...mapState({
				authenticated: state => state.currentVendor.authenticated,
				vendor: state => state.currentVendor.vendor,
                verification: state => state.verification.verification
			})
		},
		methods: {
			...mapActions({
                'fetchNotificationVendor': 'orderNotifications/fetchVendor',
				'getVendor': 'currentVendor/getVendor',
				'logoutVendor': 'currentVendor/logoutVendor',
                'resend': 'verification/resend'
			}),
            darkMode() {
                let vm = this;
                let darkMode = !vm.$vuetify.theme.dark;

                localStorage.setItem('darkMode', darkMode);
                vm.$vuetify.theme.dark = darkMode;
            },
			async logout() {
				let vm = this;
				await vm.logoutVendor();

                window.location.href = "/login";
			}
		}
	}
</script>
