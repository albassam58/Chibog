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
                            <v-toolbar-title>Forgot Password</v-toolbar-title>
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
                                            @keyup.enter="forgot"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn
                                color="primary"
                                text
                                @click="$router.push('/login')"
                                class="px-12 mb-2 mr-2"
                            >Login</v-btn>
                            <v-spacer />
                            <v-btn
                            	:disabled="disabled"
                            	color="primary"
                            	@click="forgot"
                            	class="px-12 mb-2 mr-2"
                            >Send Password Reset Link</v-btn>
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
			disabled: false,
            form: {},
        }),
		methods: {
            ...mapActions('currentVendor', ['forgotPassword']),
            async forgot() {
                let vm = this;
                try {
                	vm.disabled = true;

                    await vm.forgotPassword(vm.form);
                    vm.$router.push("/login");

                    vm.disabled = false;
                } catch (err) {
                	vm.disabled = false;
                    vm.form.email = "";
                }
            }
		},
	}
</script>