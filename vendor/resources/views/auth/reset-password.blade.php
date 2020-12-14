<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <div id="app">
    <v-app>
      <v-main>
        <v-container>
            <v-row
                justify="center"
            >
                <v-col
                    cols="12"
                    sm="8"
                    md="6"
                >
                    <v-alert type="error" v-if="error">
                        @{{ error }}
                    </v-alert>
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
                            <v-form>
                                <input type="hidden" id="token" name="token" value="{{ $token }}">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            label="Email"
                                            name="email"
                                            prepend-icon="mdi-account"
                                            type="email"
                                            v-model="form.email"
                                            required
                                            autocomplete="email"
                                            autofocus
                                            @keyup.enter="resetPassword"
                                        ></v-text-field>

                                        <v-text-field
                                            id="password"
                                            label="Password"
                                            name="password"
                                            prepend-icon="mdi-lock"
                                            type="password"
                                            v-model="form.password"
                                            required
                                            @keyup.enter="resetPassword"
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
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer />
                            <v-btn color="primary" @click="resetPassword" class="px-12 mb-2 mr-2">Reset Password</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script>
    new Vue({
      el: '#app',
      vuetify: new Vuetify(),
      data: {
        error: null,
        form: {}
      },
      created() {
        let vm = this;
      },
      methods: {
        async resetPassword() {
            const token = document.getElementById('token').value;
            this.form.token = token;
            // await axios.post('/api/v1/vendor/reset-password', this.form);

            // var xhttp = new XMLHttpRequest();
            // xhttp.onreadystatechange = function() {
            //     if (this.readyState == 4 && this.status == 200) {
            //        // Typical action to be performed when the document is ready:
                   
            //     } else {
            //         console.log(this)
            //     }
            // };
            // xhttp.open("POST", "/api/v1/vendor/reset-password");
            // xhttp.send(this.form);
            
            const headers = new Headers({
                'X-CSRF-TOKEN': document.getElementsByName('csrf-token')[0].getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            });
            const url = "/api/v1/vendor/reset-password";
            fetch(url, {
                method : "POST",
                headers,
                // body: new FormData(document.getElementById("inputform")),
                // -- or --
                body : JSON.stringify(this.form)
            })
            .then((response) => response.json())
            .then((json) => {
                console.log('Gotcha');
            }).catch((err) => {
                console.log(err);
            });
        }
      }
    })
  </script>
</body>
</html>

