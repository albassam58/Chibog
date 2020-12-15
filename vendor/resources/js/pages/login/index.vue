<template>
		<v-container style="height: 100%">
            <v-row
                class="vertical-center"
                align="center"
            >
                <v-col
                    cols="12"
                    sm="6"
                    md="7"
                >
                    <v-img
                        src="/images/Food Collage.jpg"
                        :aspect-ratio="16/9"
                        width="600"
                        gradient="to top right, rgba(255,255,255,.3), rgba(255,255,255,.4)"
                    >
                    </v-img>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="5"
                    class="text-left"
                >
                    <div class="text-h1 primary--text font-weight-bold">ENKA</div>
                    <div class="text-overline">
                        Tara na at kumita ng limpak limpak na salapi!
                    </div>
                    <v-form ref="form" class="mt-4">
                        <v-row>
                            <v-col cols="12">
                                <v-text-field
                                    label="Email"
                                    name="email"
                                    type="email"
                                    outlined
                                    hide-details="auto"
                                    v-model="form.email"
                                    @keyup.enter="login"
                                    :rules="emailRules"
                                    autofocus
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    id="password"
                                    label="Password"
                                    name="password"
                                    type="password"
                                    outlined
                                    hide-details="auto"
                                    v-model="form.password"
                                    :rules="rules"
                                    @keyup.enter="login"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="12" class="d-flex">
                                <div>
                                    <v-checkbox
                                        class="mt-2"
                                        v-model="form.remember_me"
                                        label="Keep me signed in"
                                    ></v-checkbox>
                                </div>
                                <div class="ml-auto">
                                    <v-btn
                                        :disabled="disabled"
                                        rounded
                                        color="primary"
                                        @click="login"
                                        class="px-16 py-6 mb-2"
                                    >Sign In</v-btn>
                                    
                                </div>
                            </v-col>
                            <v-col cols="12" sm="12" class="mt-4 text-overline text-center">
                                <div>
                                    Don't have an account? <router-link to="/register">Sign up</router-link>
                                </div>
                                <router-link to="/forgot-password" color="primary">Forgot your password?</router-link>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-col>
            </v-row>
        </v-container>
</template>

<script type="text/javascript">
    import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
            form: {},
            disabled: false,
            isLoggedIn: true,
            rules: [
                v => !!v || 'Field is required'
            ],
            emailRules: [
                v => !!v || 'Field is required',
                v => /.+@.+/.test(v) || 'E-mail must be valid',
            ]
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

                let valid = vm.$refs.form.validate();

                if (valid) {
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
            }
		},
	}
</script>

<style scoped>
    .vertical-center {
        position: relative;top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
    }
</style>