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
                <div class="text-h2 primary--text font-weight-bold">SIGN UP</div>
                <div class="text-overline">
                    Dito magsisimula ang iyong pag angat!
                </div>
                <v-form
                    ref="form"
                    v-model="valid"
                >
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                label="First Name"
                                name="first_name"
                                type="text"
                                outlined
                                autofocus
                                hide-details="auto"
                                v-model="form.first_name"
                                required
                                :rules="rules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                label="Last Name"
                                name="last_name"
                                type="text"
                                outlined
                                hide-details="auto"
                                v-model="form.last_name"
                                required
                                :rules="rules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="7">
                            <v-text-field
                                label="Email"
                                name="email"
                                type="email"
                                outlined
                                hide-details="auto"
                                v-model="form.email"
                                required
                                :rules="emailRules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="5">
                            <v-text-field
                                label="Mobile Number"
                                name="mobile_number"
                                type="text"
                                outlined
                                hide-details="auto"
                                v-model="form.mobile_number"
                                required
                                :rules="rules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                id="password"
                                label="Password"
                                name="password"
                                type="password"
                                outlined
                                hide-details="auto"
                                v-model="form.password"
                                required
                                :rules="passwordRules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                id="password_confirmation"
                                label="Password Confirmation"
                                name="password_confirmation"
                                type="password"
                                outlined
                                hide-details="auto"
                                v-model="form.password_confirmation"
                                required
                                :rules="rules.concat(passwordConfirmationRule)"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-autocomplete
                                v-model="form.region"
                                :items="regions"
                                autocomplete="new-password"
                                item-value="id"
                                item-text="name"
                                outlined
                                hide-details="auto"
                                label="Region"
                                @change="findProvincesByRegion(form.region)"
                                required
                                :rules="rules"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="6">
                            <v-autocomplete
                                v-model="form.province"
                                :items="provinces"
                                autocomplete="new-password"
                                label="Province"
                                outlined
                                hide-details="auto"
                                @change="findCitiesByProvince(form.province)"
                                required
                                :rules="rules"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="6">
                            <v-autocomplete
                                v-model="form.city"
                                :items="cities"
                                autocomplete="new-password"
                                label="City"
                                outlined
                                hide-details="auto"
                                @change="findBarangaysByProvinceCity({ provinceName: form.province, cityName: form.city })"
                                required
                                :rules="rules"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="6">
                            <v-autocomplete
                                v-model="form.barangay"
                                :items="barangays"
                                autocomplete="new-password"
                                label="Barangay"
                                required
                                outlined
                                hide-details="auto"
                                :rules="rules"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="form.street"
                                label="House #, Floor #, Street"
                                required
                                outlined
                                hide-details="auto"
                                :rules="rules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" class="mt-2 text-overline">
                            Have an account? <router-link to="/login">Sign in</router-link>
                        </v-col>
                        <v-col cols="12" sm="6" class="text-right">
                            <v-btn
                                rounded
                                color="primary"
                                :disabled="disabled"
                                @click="register"
                                class="px-16 py-6 mb-2"
                            >Sign Up</v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>
        </v-row>
    </v-container>
</template>

<script type="text/javascript">
    import { mapState, mapActions } from 'vuex';

    export default {
        data: () => ({
            valid: false,
            disabled: false,
            form: {},
            rules: [
                v => !!v || 'Field is required'
            ],
            emailRules: [
                v => !!v || 'Field is required',
                v => /.+@.+/.test(v) || 'E-mail must be valid',
            ],
            urlRules: [
                v => !!v || 'Field is required',
                v => /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/.test(v) || 'Url must be valid'
            ],
            passwordRules: [
                v => !!v || 'Field is required',
                v => (v && v.length >= 8) || "Field must be at least 8 characters long"
            ]
        }),
        async created() {
            let vm = this;
            await vm.fetchRegions();
        },
        computed: {
            ...mapState('currentVendor', {
                status: state => state.status
            }),
            ...mapState('regions', {
                regions: state => state.regions
            }),
            ...mapState('provinces', {
                provinces: state => state.provincesByRegion
            }),
            ...mapState('cities', {
                cities: state => state.citiesByProvince
            }),
            ...mapState('barangays', {
                barangays: state => state.barangaysByProvinceCity
            }),
            passwordConfirmationRule() {
                return () =>
                    this.form.password === this.form.password_confirmation || "Password must match";
            }
        },
        methods: {
            ...mapActions({
                'registerVendor': 'currentVendor/registerVendor',
                'fetchRegions': 'regions/fetch',
                'findProvincesByRegion': 'provinces/findByRegion',
                'findCitiesByProvince': 'cities/findByProvince',
                'findBarangaysByProvinceCity': 'barangays/findByProvinceCity',
            }),
            async register() {
                let vm = this;
                let valid = vm.$refs.form.validate();

                if (valid) {
                    try {
                        vm.disabled = true;

                        await vm.registerVendor(vm.form);
                        vm.$router.push('/login');

                        vm.disabled = false;
                    } catch(err) {
                        vm.disabled = false;
                    }
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