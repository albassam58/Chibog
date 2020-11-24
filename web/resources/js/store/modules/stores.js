const state = {
	stores: [],
	storesByUser: [],
	storesByCity: [],
	searchedStores: [],
	store: {}
};
const getters = {};
const actions = {
	async fetch({ commit }, params = null) {
		try {
			let url = '/v1/stores';

			if (params) {
				var esc = encodeURIComponent;
				var query = Object.keys(params)
				    .map(k => esc(k) + '=' + esc(params[k]))
				    .join('&');
				url += '?' + query;
			}

			let { data } = await axios.get(url);

			commit('setStores', data.data);
		} catch (err) {

		}
	},
	async search({ commit }, query) {
		try {
			let { data } = await axios.get(`/v1/stores/search/${ query }`);
			commit('setSearchedStores', data.data);
		} catch (err) {

		}
	},
	async fetchByCity({ commit }, city) {
		try {
			let { data } = await axios.get(`/v1/stores/city/${ city }`);
			commit('setStoresByCity', data.data);
		} catch (err) {

		}
	},
	async fetchByUser({ commit }) {
		try {
			let { data } = await axios.get('/v1/stores/user');
			commit('setStoresByUser', data.data);
		} catch (err) {

		}
	},
	async find({ commit }, id) {
		try {
			let { data } = await axios.get(`/v1/stores/${ id }`);
			commit('setStore', data);
		} catch (err) {

		}
	},
	async save({ commit }, form) {
		try {
			await axios.post('/v1/stores', form);
		} catch (err) {

		}
	}
};
const mutations = {
	setStores(state, stores) {
		state.stores = stores;
	},
	setSearchedStores(state, searchedStores) {
		state.searchedStores = searchedStores;
	},
	setStoresByUser(state, storesByUser) {
		state.storesByUser = storesByUser;
	},
	setStoresByCity(state, storesByCity) {
		state.storesByCity = storesByCity;
	},
	setStore(state, store) {
		state.store = store;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}