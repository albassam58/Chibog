<template>
	<div>
		<v-container>
            <v-row
                justify="center"
            >
                <v-col
                    cols="12"
                    sm="8"
                    md="4"
                >
                    <v-alert type="error" v-if="!isLoggedIn">
                        Invalid username or password
                    </v-alert>
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
                                <v-text-field
                                    label="Login"
                                    name="login"
                                    prepend-icon="mdi-account"
                                    type="email"
                                    v-model="form.email"
                                    @keyup.enter="login"
                                ></v-text-field>

                                <v-text-field
                                    id="password"
                                    label="Password"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    v-model="form.password"
                                    @keyup.enter="login"
                                ></v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="primary" @click="login">Login</v-btn>
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
            isLoggedIn: true
        }),
        computed: {
            ...mapState('currentAdmin', {
                status: state => state.status,
                admin: state => state.admin
            })
        },
		methods: {
            ...mapActions('currentAdmin', ['loginAdmin', 'getAdmin']),
            async login() {
                let vm = this;
                try {
                    await vm.loginAdmin(vm.form);
                    window.location.href = "/";
                } catch (err) {
                    vm.form.password = "";
                    vm.isLoggedIn = false;
                }
            }
		},
	}
</script>