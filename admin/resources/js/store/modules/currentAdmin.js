const state = {
	authenticated: false,
	admin: null,
	tokens: []
};
const getters = {};
const actions = {
	async getAdmin({ commit }) {
		try {
			if (localStorage.getItem('authenticated')) {
				let { data } = await axios.get('/v1/admin/current');
				commit('setAdmin', data.data);
			}
		} catch (err) {
			throw err;
		}
	},
	async getAdminTokens({ commit }) {
		try {
			let { data } = await axios.get('/v1/admin/tokens');
			commit('setTokens', data.data);
		} catch (err) {
			throw err;
		}
	},
	async loginAdmin({commit}, credentials) {
		try {
			axios.defaults.baseURL = '/';
			await axios.post('/login', credentials);
			localStorage.setItem('authenticated', true);
		} catch (err) {
			throw err;
		}
	},
	async registerAdmin({commit}, admin) {
		try {
			await axios.post('/v1/admin/register', admin);
		} catch (err) {
			throw err;
		}
	},
	async logoutAdmin() {
		try {
			axios.defaults.baseURL = '/';
			await axios.post('/logout');
			localStorage.setItem('authenticated', false);
		} catch (err) {
			throw err;
		}
	},
	async logoutDevice({ commit }, id) {
		try {
			await axios.post(`/v1/admin/logout/device/${ id }`);
		} catch (err) {
			throw err;
		}
	},
	// logout all devices except current access
	async logoutAll({ commit }) {
		try {
			await axios.post(`/v1/admin/logout/all`);
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	setAdmin(state, admin) {
		state.admin = admin;
		state.authenticated = true;
	},
	setTokens(state, tokens) {
		state.tokens = tokens;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}