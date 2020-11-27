const state = {
	authenticated: false,
	user: null
};
const getters = {};
const actions = {
	async getUser({ commit }) {
		try {
			if (localStorage.getItem('api_token')) {
				let { data } = await axios.get('/v1/user/current');

				localStorage.setItem('current_user', JSON.stringify(data.data));

				commit('setUser', data.data);
			}
		} catch (err) {
			throw err;
		}
	},
	async loginUser({commit}, credentials) {
		try {
			let { data } = await axios.post('/v1/user/login', credentials);

			localStorage.setItem('api_token', `Bearer ${ data.data }`);
		} catch (err) {
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
			throw err;
		}
	},
	async logoutUser() {
		try {
			await axios.post('/v1/user/logout');

			localStorage.removeItem('api_token');
			localStorage.removeItem('current_user');
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	setUser(state, user) {
		state.user = user;
		state.authenticated = true;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}