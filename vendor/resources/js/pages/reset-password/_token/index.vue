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
                <div class="text-h2 primary--text font-weight-bold">RESET PASSWORD</div>
                <div class="text-overline">
                    Wag mo ulit kakalimutan ang iyong password para tuloy tuloy ang kita.
                </div>
                <v-form ref="form">
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                id="password"
                                label="New Password"
                                name="password"
                                outlined
                                hide-details="auto"
                                type="password"
                                v-model="form.password"
                                required
                                @keyup.enter="resetPassword"
                                :rules="passwordRules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                id="password_confirmation"
                                label="Password Confirmation"
                                name="password_confirmation"
                                outlined
                                hide-details="auto"
                                type="password"
                                v-model="form.password_confirmation"
                                required
                                @keyup.enter="resetPassword"
                                :rules="rules.concat(passwordConfirmationRule)"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" class="mt-2">
                            <v-btn
                                text
                                color="primary"
                                @click="$router.push('/login')"
                            >
                                <v-icon>mdi-arrow-left</v-icon>
                                Login
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="6" class="text-right">
                            <v-btn
                                rounded
                                :disabled="disabled"
                                color="primary"
                                @click="resetPassword"
                                class="py-6"
                            >Reset Password</v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>
        </v-row>
    </v-container>
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

<style scoped>
    .vertical-center {
        position: relative;top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
    }
</style>