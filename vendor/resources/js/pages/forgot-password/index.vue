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
                <div class="text-h2 primary--text font-weight-bold">FORGOT PASSWORD</div>
                <div class="text-overline">
                    Wag masyadong makakalimutin para tuloy tuloy ang kita.
                </div>
                <v-form ref="form" @submit.prevent>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                label="Email"
                                name="email"
                                type="email"
                                outlined
                                hide-details="auto"
                                v-model="form.email"
                                @keyup.enter="forgot"
                                required
                                autofocus
                                 class="grow"
                                :rules="emailRules"
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
                                @click="forgot"
                                class="py-6"
                            >Send Reset Link</v-btn>
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
			disabled: false,
            form: {},
            emailRules: [
                v => !!v || 'Field is required',
                v => /.+@.+/.test(v) || 'E-mail must be valid',
            ],
        }),
		methods: {
            ...mapActions('currentVendor', ['forgotPassword']),
            async forgot() {
                let vm = this;

                let valid = vm.$refs.form.validate();

                if (valid) {
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