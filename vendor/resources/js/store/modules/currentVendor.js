const state = {
	vendor: {},
	status: {
		registered: false,
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
			console.log(err);
			throw err;
			// logoutVendor();
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

			commit('loginSuccess', data.data);
		} catch (err) {
			commit('loginFailure');
			throw err;
		}
	},
	async registerVendor({commit}, vendor) {
		try {
			let { data } = await axios.post('/v1/vendor/register', vendor);
			commit('setVendor', data.data);
		} catch (err) {
			commit('registerFailure');
		}
	},
	async logoutVendor() {
		try {
			await axios.post('/v1/vendor/logout');

			localStorage.removeItem('api_token');
			localStorage.removeItem('current_vendor');

			state.status = {
				registered: false,
				loggedIn: false
			}
			state.vendor = {};
		} catch (err) {
			console.log(err);
		}
	}
};
const mutations = {
	setVendor(state, vendor) {
		state.vendor = vendor;
		state.status.loggedIn = true;
	},
	loginSuccess(state, vendor) {
		state.status.loggedIn = true;
		state.vendor = vendor;
	},
	loginFailure(state) {
		state.status.loggedIn = false;
		state.vendor = {};
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