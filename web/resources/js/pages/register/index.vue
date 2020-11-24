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
            md="4"
          >
            <v-alert type="error" v-if="!isRegistered">
              Register Error
            </v-alert>
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
                <!-- <v-btn href="/auth/facebook" color="primary">Register with Facebook</v-btn> -->
                <v-form ref="form">
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
      form: {},
      isRegistered: true,
      rules: [
              v => !!v || 'Field is required'
            ],
            emailRules: [
              v => !!v || 'Field is required',
              v => /.+@.+/.test(v) || 'E-mail must be valid',
            ],
    }),
    computed: {
      ...mapState('currentUser', {
        status: state => state.status
      })
    },
    methods: {
      ...mapActions('currentUser', ['registerUser']),
      async register() {
        try {
          let vm = this;

          let valid = vm.$refs.form.validate();

          if (valid) {
            await vm.registerUser(vm.form);
            vm.$router.push('/login');
          }
        } catch(err) {
          let vm = this;
          vm.isRegistered = vm.status.registered;
        }
      }
    }
  }
</script>