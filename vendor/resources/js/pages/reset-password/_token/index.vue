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
                            <v-form ref="form">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            id="password"
                                            label="Password"
                                            name="password"
                                            prepend-icon="mdi-lock"
                                            type="password"
                                            v-model="form.password"
                                            required
                                            @keyup.enter="resetPassword"
                                            :rules="passwordRules"
                                        ></v-text-field>

                                        <v-text-field
                                            id="password_confirmation"
                                            label="Password Confirmation"
                                            name="password_confirmation"
                                            prepend-icon="mdi-lock"
                                            type="password"
                                            v-model="form.password_confirmation"
                                            required
                                            @keyup.enter="resetPassword"
                                            :rules="rules.concat(passwordConfirmationRule)"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer />
                            <v-btn
                                :disabled="disabled"
                                color="primary"
                                @click="resetPassword"
                                class="px-12 mb-2 mr-2"
                            >Reset Password</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
	</div>
</template>

<script type="text/javascript">
	export default {
	       data: () => ({
            error: null,
            disabled: false,
            form: {},
            rules: [
                v => !!v || 'Field is required'
            ],
            passwordRules: [
                v => !!v || 'Field is required',
                v => (v && v.length >= 8) || "Field must be at least 8 characters long"
            ]
        }),
        created() {
            let vm = this;
        },
        computed: {
            passwordConfirmationRule() {
                return () =>
                    this.form.password === this.form.password_confirmation || "Password must match";
            }
        },
        methods: {
            async resetPassword() {
                let vm = this;

                try {
                    let valid = vm.$refs.form.validate();

                    if (valid) {
                        vm.disabled = true;

                        vm.form.email = vm.$route.query.email;
                        vm.form.token = vm.$route.params.token;
                        await axios.post('/v1/vendor/reset-password', this.form, { toasterMessage: "Password successfully updated!" });

                        vm.$router.push('/login');

                        vm.disabled = false;
                    }
                } catch (err) {
                    vm.disabled = false;
                }
            }
        }
	}
</script>