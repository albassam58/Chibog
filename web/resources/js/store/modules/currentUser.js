const state = {
	user: {},
	status: {
		registered: false,
		loggedIn: false,
		error: ""
	}
};
const getters = {
	isAuthenticated: state => {
		return state.status.loggedIn;
	}
};
const actions = {
	async getUser({ commit }) {
		try {
			// if there is api_token get the logged in user
			if (localStorage.getItem('api_token')) {
				let { data } = await axios.get('/v1/user/current');
				commit('setUser', data.data);
			}
		} catch (err) {
			logoutUser();
		}
	},
	async loginUser({commit}, user) {
		try {
			let { data } = await axios.post('/v1/user/login', {
				email: user.email,
				password: user.password
			});

			localStorage.setItem('api_token', `Bearer ${ data.data.api_token }`);
			localStorage.setItem('current_user', JSON.stringify(data.data));

			commit('loginSuccess', data.data);
		} catch (err) {
			commit('loginFailure', "Invalid username or password");
			throw err;
		}
	},
	async registerUser({commit}, user) {
		try {
			await axios.post('/v1/user/register', {
				first_name: user.first_name,
				last_name: user.last_name,
				email: user.email,
				password: user.password,
				password_confirmation: user.password_confirmation
			});
		} catch (err) {
			commit('registerFailure');
			throw err;
		}
	},
	async logoutUser() {
		try {
			await axios.post('/v1/user/logout');

			localStorage.removeItem('api_token');
			localStorage.removeItem('current_user');

			state.status = {
				registered: false,
				loggedIn: false
			}
			state.user = {};
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	setUser(state, user) {
		state.user = user;
		state.status.loggedIn = true;
	},
	loginSuccess(state, user) {
		state.status.loggedIn = true;
		state.user = user;
	},
	loginFailure(state, message) {
		state.status.loggedIn = false;
		state.status.error = message;
		state.user = {};
	},
	registerSuccess(state) {
		state.status.registered = true;
	},
	registerFailure(state) {
		state.status.registered = false;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}