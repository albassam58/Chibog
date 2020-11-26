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
                        class="white--text text-decoration-none">
                        Chibog
                    </router-link>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn
                    icon
                    @click="$router.push('/orders/search')">
                    <v-icon>mdi-shopping-search</v-icon>
                </v-btn>
                <v-btn
                    v-if="!hasOrder"
                    icon>
                    <v-icon>mdi-cart</v-icon>
                </v-btn>
                <v-badge
                    v-else
                    color="error"
                    dot
                    overlap
                    offset-x="20"
                    offset-y="20">
                    <v-btn
                        icon
                        @click="$router.push('/orders/cart')">
                        <v-icon>mdi-cart</v-icon>
                    </v-btn>
                </v-badge>
                <!-- <v-badge
                    v-if="status.loggedIn"
                    color="red"
                    content="6"
                    overlap
                    offset-x="24"
                    offset-y="25"
                    class="mr-2">
                    <v-btn
                        icon>
                        <v-icon>mdi-email</v-icon>
                    </v-btn>
                </v-badge> -->
                <v-menu
                    v-if="status.loggedIn"
                    left
                    bottom>
                    <template
                        v-slot:activator="{ on, attrs }">
                        <v-btn
                            icon
                            v-bind="attrs"
                            v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item>
                            <v-list-item-title>
                                <v-icon>mdi-account</v-icon>
                                {{ currentUser.first_name }}
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item
                            @click="$router.push('/orders')">
                            <v-list-item-title>
                                <v-icon>mdi-shopping</v-icon>
                                <span>Orders</span>
                            </v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                        <v-list-item
                            @click="logout">
                            <v-list-item-title>
                                <v-icon
                                    class="red--text text-darken-4">mdi-logout</v-icon>
                                <span
                                    class="red--text text-darken-4">Logout</span>
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
                <span
                    v-else>
                    <router-link
                        to="/register"
                        class="white--text text-decoration-none">Register</router-link>
                    <router-link
                        to="/login"
                        class="white--text text-decoration-none">Login</router-link>
                </span>
            </v-app-bar>
            <router-view v-on:update-cart="updateCart" />
        </div>
    </v-app>
</template>
<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex';

	export default {
		data: () => ({
            carts: JSON.parse(localStorage.getItem("order")),
            user: null,//JSON.parse(localStorage.getItem('current_user')),
            form: {
                status: 1
            },
            params: {
                filters: {
                    status: 1,
                    created_by: null
                }
            }
		}),
		async created() {
			let vm = this;
            await vm.getUser();
            await vm.manageCarts();
		},
		computed: {
			...mapState('currentUser', {
                currentUser: state => state.user,
				status: state => state.status
			}),
            ...mapState('orders', {
                hasOrder: state => state.hasOrder,
            })
		},
		methods: {
			...mapActions({
                'check': 'orders/check',
                'saveByStore': 'orders/saveByStore',
				'getUser': 'currentUser/getUser',
				'logoutUser': 'currentUser/logoutUser'
			}),
            async manageCarts() {
                let vm = this;

                if (vm.user) {
                    // logged in
                    // store to database if with localStorage(orders) with status = cart
                    if (vm.carts) {
                        await vm.saveCart();
                    }

                    vm.params['filters']['created_by'] = vm.user.id;
                    vm.setParams('orders', vm.params);
                    await vm.check();
                    vm.$store.commit('orders/setHasOrder', vm.hasOrder);
                } else {
                    // logged out
                    if (vm.carts) vm.$store.commit('orders/setHasOrder', true);
                }
            },
			updateCart(cart) {
				let vm = this;
                vm.$store.commit('orders/setHasOrder', cart);
			},
            async saveCart() {
                let vm = this;

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
                    customer_street: vm.user.street,
                };

                try {
                    await vm.saveByStore({ form: vm.form, items: vm.carts });
                    localStorage.removeItem("order");
                } catch (err) {
                    console.log(err);
                }
            },
			async logout() {
                try {
    				let vm = this;
    				await vm.logoutUser();
                    window.location.href = "/";
                } catch (err) {
                    console.log(err);
                }
			}
		}
	}
</script>
