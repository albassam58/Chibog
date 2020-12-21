<template>
    <v-app>
        <v-app-bar
            v-if="!excludeApp.includes($route.name)"
            app
            class="d-print-none"
        >
            <!-- <v-app-bar-nav-icon></v-app-bar-nav-icon> -->
            <v-toolbar-title>
                <router-link
                    to="/"
                    class="text-decoration-none"
                >
                    <strong>ENKA</strong>
                </router-link>
            </v-toolbar-title>
            <v-spacer />
            <div v-if="authenticated && vendor && vendor.email_verified_at && vendor.mobile_verified_at">
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
        </v-app-bar>
        <v-navigation-drawer
            app
            permanent
            color="background"
            dark
            :mini-variant="$vuetify.breakpoint.mdAndDown"
            v-if="!excludeApp.includes($route.name)"
            class="d-print-none"
        >
            <v-list v-if="vendor">
                <v-list-item link>
                    <v-list-item-content>
                        <v-list-item-title class="title">
                            {{ vendor.full_name }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            {{ vendor.email }}
                        </v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-list
                dense
                shaped
                v-if="vendor"
            >
                <v-list-item link to="/statistics" v-if="vendor.email_verified_at && vendor.mobile_verified_at">
                    <v-list-item-icon>
                        <v-icon>mdi-chart-box</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Statistics</v-list-item-title>
                </v-list-item>
                <v-list-item link to="/orders" v-if="vendor.email_verified_at && vendor.mobile_verified_at">
                    <v-list-item-icon>
                        <v-icon>mdi-cart</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Orders</v-list-item-title>
                </v-list-item>
                <v-list-item link to="/stores" v-if="vendor.email_verified_at && vendor.mobile_verified_at">
                    <v-list-item-icon>
                        <v-icon>mdi-store</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Stores</v-list-item-title>
                </v-list-item>
                <v-list-item link to="/chats" v-if="vendor.email_verified_at && vendor.mobile_verified_at">
                    <v-list-item-icon>
                        <v-badge
                            v-if="hasUnreadMessage"
                            color="blue"
                            dot
                            offset-x="7"
                            offset-y="7"
                        >
                            <v-icon>mdi-message-text</v-icon>
                        </v-badge>
                        <v-icon v-else>mdi-message-text</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>
                        Messages
                    </v-list-item-title>
                </v-list-item>
                <v-list-item link to="/vendor">
                    <v-list-item-icon>
                        <v-icon>mdi-account</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Profile</v-list-item-title>
                </v-list-item>
            </v-list>

            <template v-slot:append>
                <v-list
                    dense
                    shaped
                >
                    <v-list-item @click="logout">
                        <v-list-item-icon>
                            <v-icon>mdi-logout</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item>
                </v-list>
                <!-- <div class="pa-2">
                    <v-btn text @click="logout">
                        <v-icon>mdi-logout</v-icon>
                        Logout
                    </v-btn>
                </div> -->
             </template>
        </v-navigation-drawer>

        <v-content>
            <router-view ref="router" :key="$route.fullPath" />
        </v-content>
    </v-app>
</template>
<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'
    import Chat from '@components/ChatComponent'
    import OrderNotification from '@components/OrderNotificationComponent'

	export default {
        components: {
            Chat,
            OrderNotification
        },
		data: () => ({
            hasUnreadMessage: false,
            alreadyVerified: false,
            excludeApp: [
                'login',
                'register',
                'forgot-password',
                'reset-password-token'
            ]
		}),
		async created() {
			let vm = this;
            let darkMode = localStorage.getItem('darkMode');

            await vm.getVendor();

            // check if email is verified, if not, redirect to email verification
            if (vm.vendor && (!vm.vendor.email_verified_at || !vm.vendor.mobile_verified_at)) {
                if (vm.$route.path != '/verification' && vm.$route.path != "/vendor") {
                    vm.$router.push('/verification');
                }
            }

            // set dark mode
            if (!vm.excludeApp.includes(vm.$route.name))
                vm.$vuetify.theme.dark = darkMode === 'true' ? true : false;

            if (vm.vendor) {
                // Connect to Socket.io
                let socket = io(env.consumer.socket);

                // ... listen for new events/messages
                socket.on(`order-${ vm.vendor.id }:App\\Events\\Order`, data => {
                    vm.fetchNotificationVendor();
                    vm.$refs.orderNotificationRef.updateOrderNotification(data.data);
                    // if (this.activeChannel == channel.id) {
                    //     this.messages.push(data.data);
                    // }
                });

                // ... listen for new events/messages
                socket.on(`chat-from-customer-${ vm.vendor.id }:App\\Events\\Chat`, data => {
                    // push to messages
                    vm.$refs.router.$refs.chat.pushData(data.data);
                    // add blue color to sidebar
                    vm.hasUnreadMessage = true;
                });
            }
		},
		computed: {
			...mapState({
				authenticated: state => state.currentVendor.authenticated,
				vendor: state => state.currentVendor.vendor,
                verification: state => state.verification.verification,
                customers: state => state.chats.customers
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
		},
        watch: {
            customers: {
                deep: true,
                handler(val) {
                    let vm = this;
                    // check if there's unread messages
                    if (val && val.data.length) {
                        let index = _.findIndex(val.data, { 'status': 1, 'model': 'User' });
                        if (index >= 0) {
                            vm.hasUnreadMessage = true;
                        } else {
                            vm.hasUnreadMessage = false;
                        }
                    }
                }
            }
        }
	}
</script>
