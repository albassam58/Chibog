<template>
  <div>
    <v-container
        class="fill-height"
        fluid
      >
        <v-row
          align="center"
          justify="center"
        >
          <v-col
            cols="12"
            sm="8"
            md="6"
          >
            <!-- <v-alert type="error" v-if="!status.registered">
              Register Error
            </v-alert> -->
            <v-card class="elevation-12">
              <v-toolbar
                color="primary"
                dark
                flat
              >
                <v-toolbar-title>Register form</v-toolbar-title>
                <v-spacer></v-spacer>
                
              </v-toolbar>
              <v-card-text>
                <!-- <v-btn href="/auth/facebook" color="primary">Login with Facebook</v-btn> -->
                <v-form
                  ref="form"
                  v-model="valid"
                >
                  <v-text-field
                    label="First Name"
                    name="first_name"
                    prepend-icon="mdi-account"
                    type="text"
                    v-model="form.first_name"
                    required
                    :rules="rules"
                  ></v-text-field>

                  <v-text-field
                    label="Last Name"
                    name="last_name"
                    prepend-icon="mdi-account"
                    type="text"
                    v-model="form.last_name"
                    required
                    :rules="rules"
                  ></v-text-field>

                  <v-text-field
                    label="Email"
                    name="email"
                    prepend-icon="mdi-account"
                    type="email"
                    v-model="form.email"
                    required
                    :rules="emailRules"
                  ></v-text-field>

                  <v-text-field
                    label="Mobile Number"
                    name="mobile_number"
                    prepend-icon="mdi-account"
                    type="text"
                    v-model="form.mobile_number"
                    required
                    :rules="rules"
                  ></v-text-field>

                  <v-text-field
                    id="password"
                    label="Password"
                    name="password"
                    prepend-icon="mdi-lock"
                    type="password"
                    v-model="form.password"
                    required
                    :rules="rules"
                  ></v-text-field>

                  <v-text-field
                    id="password_confirmation"
                    label="Password Confirmation"
                    name="password_confirmation"
                    prepend-icon="mdi-lock"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    :rules="rules"
                  ></v-text-field>

                  <v-autocomplete
                      v-model="form.region"
                      :items="regions"
                      autocomplete="new-password"
                      item-value="id"
                      item-text="name"
                      label="Region"
                      required
                      @change="findProvincesByRegion(form.region)"
                      required
                      :rules="rules"
                  ></v-autocomplete>

                  <v-autocomplete
                    v-model="form.province"
                      :items="provinces"
                      autocomplete="new-password"
                      label="Province"
                      required
                      @change="findCitiesByProvince(form.province)"
                      required
                      :rules="rules"
                  ></v-autocomplete>

                  <v-autocomplete
                    v-model="form.city"
                      :items="cities"
                      autocomplete="new-password"
                      label="City"
                      required
                      @change="findBarangaysByProvinceCity({ provinceName: form.province, cityName: form.city })"
                      required
                      :rules="rules"
                  ></v-autocomplete>

                  <v-autocomplete
                    v-model="form.barangay"
                      :items="barangays"
                      autocomplete="new-password"
                      label="Barangay"
                      required
                      :rules="rules"
                  ></v-autocomplete>

                  <v-text-field
                      v-model="form.street"
                      label="House #, Floor #, Street"
                      required
                      :rules="rules"
                  ></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="register">Register</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
  </div>
</template>

<script type="text/javascript">
    import { mapState, mapActions } from 'vuex';

    export default {
        data: () => ({
            valid: false,
            form: {},
            rules: [
              v => !!v || 'Field is required'
            ],
            emailRules: [
              v => !!v || 'Field is required',
              v => /.+@.+/.test(v) || 'E-mail must be valid',
            ],
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
                      let data = await vm.registerVendor(vm.form);
                      // vm.$router.push('/login');
                  } catch(err) {
                      let vm = this;
                      vm.status.registered = false;
                  }
              }
            }
        }
    }
</script>