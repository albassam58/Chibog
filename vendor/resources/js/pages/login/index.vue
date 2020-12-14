<template>
	<div>
		<v-container>
            <v-row
                justify="center"
            >
                <v-col
                    cols="12"
                    sm="8"
                    md="6"
                >
                    <v-card class="elevation-12">
                        <v-toolbar
                            color="primary"
                            dark
                            flat
                        >
                            <v-toolbar-title>Login form</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-card-text>
                            <!-- <v-btn href="/auth/facebook" color="primary">Login with Facebook</v-btn> -->
                            <v-form>
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            label="Email"
                                            name="email"
                                            prepend-icon="mdi-account"
                                            type="email"
                                            v-model="form.email"
                                            @keyup.enter="login"
                                            autofocus
                                        ></v-text-field>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            id="password"
                                            label="Password"
                                            name="password"
                                            prepend-icon="mdi-lock"
                                            type="password"
                                            v-model="form.password"
                                            @keyup.enter="login"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" sm="12" class="d-flex">
                                        <div>
                                            <v-checkbox
                                                class="mt-0"
                                                v-model="form.remember_me"
                                                label="Keep me signed in"
                                            ></v-checkbox>
                                        </div>
                                        <div class="ml-auto">
                                            <router-link to="/forgot-password" color="primary">Forgot Password?</router-link>
                                        </div>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer />
                            <v-btn
                                :disabled="disabled"
                                color="primary"
                                @click="login"
                                class="px-12 mb-2 mr-2"
                            >Login</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
	</div>
</template>

<script type="text/javascript">
    import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
            form: {},
            disabled: false,
            isLoggedIn: true
        }),
        computed: {
            ...mapState('currentVendor', {
                status: state => state.status,
                vendor: state => state.vendor
            })
        },
		methods: {
            ...mapActions('currentVendor', ['loginVendor', 'getVendor']),
            async login() {
                let vm = this;
                try {
                    vm.disabled = true;

                    await vm.loginVendor(vm.form);
                    window.location.href = "/";
                } catch (err) {
                    vm.disabled = false;
                    vm.form.password = "";
                    vm.isLoggedIn = false;
                }
            }
		},
	}
</script>