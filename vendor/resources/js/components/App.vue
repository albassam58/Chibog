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
                <div>
                    <v-tooltip
                        v-if="status.loggedIn"
                        bottom
                    >
                        <template v-slot:activator="{ on }">
                            <v-badge
                                v-on="on"
                                color="red"
                                content="6"
                                overlap
                                offset-x="24"
                                offset-y="25"
                                class="mr-2"
                            >
                                <v-btn icon>
                                    <v-icon>mdi-email</v-icon>
                                </v-btn>
                            </v-badge>
                        </template>
                        <span>Notifications</span>
                    </v-tooltip>
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
                    v-if="status.loggedIn"
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
            <router-view />

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
	import { mapGetters, mapState, mapActions } from 'vuex';

	export default {
		data: () => ({
            alreadyVerified: false,
		}),
		async created() {
			let vm = this;
            let darkMode = localStorage.getItem('darkMode');

			await vm.getVendor();

            // set dark mode
            vm.$vuetify.theme.dark = darkMode === 'true' ? true : false;
		},
		computed: {
			...mapState('currentVendor', {
				status: state => state.status,
				vendor: state => state.vendor
			}),
            ...mapState('verification', {
                verification: state => state.verification
            })
		},
		methods: {
			...mapActions({
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
            // async resendVerification() {
            //     let vm = this;

            //     try {
            //         let id = vm.vendor.id;
            //         let email = vm.vendor.email;

            //         await vm.resend({ id: id, email: email });
            //     } catch (err) {
            //         console.log(err);
            //         if (err.response.status == 422) {
            //             vm.alreadyVerified = true;
            //         }
            //     }
            // },
			logout() {
				let vm = this;
				vm.logoutVendor();

                window.location.href = "/login";
			}
		}
	}
</script>
