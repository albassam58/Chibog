const state = {
	authenticated: false,
	vendor: null,
	tokens: []
};
const getters = {};
const actions = {
	async getVendor({ commit }) {
		try {
			// if there is api_token get the logged in vendor
			if (localStorage.getItem('api_token')) {
				let { data } = await axios.get('/v1/vendor/current');

				localStorage.setItem('current_vendor', JSON.stringify(data.data));

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
			let { data } = await axios.post('/v1/vendor/login', credentials);

			localStorage.setItem('api_token', `Bearer ${ data.data }`);
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
			await axios.post('/v1/vendor/logout');

			localStorage.removeItem('api_token');
			localStorage.removeItem('current_vendor');
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