const state = {
	stores: [],
	storesByVendor: [],
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
	async fetchByVendor({ commit }) {
		try {
			let { data } = await axios.get('/v1/stores/vendor');
			commit('setStoresByVendor', data.data);
		} catch (err) {

		}
	},
	async find({ commit }, id) {
		try {
			let { data } = await axios.get(`/v1/stores/${ id }`);
			commit('setStore', data.data);
		} catch (err) {

		}
	},
	async save({ commit }, form) {
		try {
			let { data } = await axios.post('/v1/stores', form);
			commit('setStore', data.data);
		} catch (err) {

		}
	},
	async update({ commit }, form) {
		try {
			let { data } = await axios.put(`/v1/stores/${ form.id }`, form);
			commit('setStore', data.data);
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
	setStoresByVendor(state, storesByVendor) {
		state.storesByVendor = storesByVendor;
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