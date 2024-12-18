const state = {
	items: [],
	item: {},
	params: {
		page: 1,
		search: '',
		filters: {}
	}
};
const getters = {
	items: (state) => {
        return state.items;
    }
};
const actions = {
	async fetch({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/items?${ params }`);
			commit('setItems', data.data);
		} catch (err) {
			throw err;
		}
	},
	async find({ commit }, id) {
		try {
			let { data } = await axios.get('/v1/items', id);
			commit('setItem', data.data);
		} catch (err) {

		}
	},
	async save({ commit }, form) {
		try {
			await axios.post('/v1/items', form);
		} catch (err) {

		}
	}
};
const mutations = {
	setItems(state, items) {
		state.items = items;
	},
	setItem(state, item) {
		state.item = item;
	},
	setCurrentPage(state, page) {
        state.orders.current_page = page;
    },
    setParams(state, params) {
        state.params = { ...state.params, ...params };
    },
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}