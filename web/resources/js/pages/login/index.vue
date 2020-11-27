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
                    <v-alert type="error" v-if="!isLoggedIn">
                        {{ status.error }}
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
                            <v-btn @click="socialiteLogin('facebook')" color="primary">Login with Facebook</v-btn>
                            <v-btn @click="socialiteLogin('google')" color="primary">Login with Google</v-btn>
                            <v-form>
                                <v-text-field
                                    label="Login"
                                    name="login"
                                    prepend-icon="mdi-account"
                                    type="text"
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
            ...mapState('currentUser', {
                status: state => state.status,
                user: state => state.user
            })
        },
        // Waiting for the callback.blade.php message... (token and username).
        mounted () {
            window.addEventListener('message', this.onMessage, false)
        },
        beforeDestroy () {
            window.removeEventListener('message', this.onMessage)
        },
		methods: {
            ...mapActions('currentUser', ['loginUser', 'getUser']),
            async login() {
                try {
                    let vm = this;

                    await vm.loginUser(vm.form);
                    window.location.href = "/";
                } catch (err) {
                    let vm = this;
                    vm.isLoggedIn = vm.status.loggedIn;
                }
            },
            // This method call the function to launch the popup and makes the request to the controller. 
            async socialiteLogin(provider) {
                let vm = this;
                
                const newWindow = vm.openWindow('', 'message')
                axios.defaults.baseURL = '';

                try {
                    let { data } = await axios.get(`/auth/${ provider }`);
                    newWindow.location.href = data;
                } catch (err) {
                    console.log(err);
                }
            },
            openWindow (url, title, options = {}) {
                if (typeof url === 'object') {
                    options = url
                    url = ''
                }

                options = { url, title, width: 600, height: 720, ...options }

                const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screen.left
                const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screen.top
                const width = window.innerWidth || document.documentElement.clientWidth || window.screen.width
                const height = window.innerHeight || document.documentElement.clientHeight || window.screen.height

                options.left = ((width / 2) - (options.width / 2)) + dualScreenLeft
                options.top = ((height / 2) - (options.height / 2)) + dualScreenTop

                const optionsStr = Object.keys(options).reduce((acc, key) => {
                    acc.push(`${key}=${options[key]}`)
                    return acc
                }, []).join(',')

                const newWindow = window.open(url, title, optionsStr)

                if (window.focus) {
                    newWindow.focus()
                }

                return newWindow
            },
            // This method save the new token and username
            onMessage (e) {
                let vm = this;

                if (e.data.error) {
                    vm.$store.commit('currentUser/loginFailure', e.data.error);
                    return;
                }

                if (e.origin !== window.origin || !e.data.api_token) {
                    return;
                }

                let user = JSON.parse(e.data.current_user.replace(/&quot;/g,'"'));

                localStorage.setItem('api_token', `Bearer ${ e.data.api_token }`);
                localStorage.setItem('current_user', JSON.stringify(user));

                window.location.href = "/";
            }
		},
        watch: {
            'isLoggedIn': function (value) {
                let vm = this;
                if (!value) {
                    vm.form.password = "";
                }
            }
        }
	}
</script>