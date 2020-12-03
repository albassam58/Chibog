const state = {
	authenticated: false,
	vendor: null,
	tokens: []
};
const getters = {};
const actions = {
	async getVendor({ commit }) {
		try {
			if (localStorage.getItem('authenticated')) {
				let { data } = await axios.get('/v1/vendor/current');
				commit('setVendor', data.data);
			}
		} catch (err) {
			throw err;
		}
	},
	async getVendorTokens({ commit }) {
		try {
			let { data } = await axios.get('/v1/vendor/tokens');
			commit('setTokens', data.data);
		} catch (err) {
			throw err;
		}
	},
	async loginVendor({commit}, credentials) {
		try {
			axios.defaults.baseURL = '/';
			await axios.post('/login', credentials);
			localStorage.setItem('authenticated', true);
		} catch (err) {
			throw err;
		}
	},
	async registerVendor({commit}, vendor) {
		try {
			await axios.post('/v1/vendor/register', vendor);
		} catch (err) {
			throw err;
		}
	},
	async logoutVendor() {
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
			await axios.post(`/v1/vendor/logout/device/${ id }`);
		} catch (err) {
			throw err;
		}
	},
	// logout all devices except current access
	async logoutAll({ commit }) {
		try {
			await axios.post(`/v1/vendor/logout/all`);
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	setVendor(state, vendor) {
		state.vendor = vendor;
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