const state = {
	vendor: {},
	status: {
		loggedIn: false
	}
};
const getters = {
	isAuthenticated: state => {
		return state.status.loggedIn;
	}
};
const actions = {
	async getVendor({ commit }) {
		try {
			// if there is api_token get the logged in vendor
			if (localStorage.getItem('api_token')) {
				let { data } = await axios.get('/v1/vendor/current');

				commit('setVendor', data.data);
			}
		} catch (err) {
			throw err;
		}
	},
	async loginVendor({commit}, vendor) {
		try {
			let { data } = await axios.post('/v1/vendor/login', {
				email: vendor.email,
				password: vendor.password
			});

			localStorage.setItem('api_token', `Bearer ${ data.data.api_token }`);
			localStorage.setItem('current_vendor', JSON.stringify(data.data));
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

			state.status = {
				loggedIn: false
			}

			state.vendor = {};
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	setVendor(state, vendor) {
		state.vendor = vendor;
		state.status.loggedIn = true;
	},
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}