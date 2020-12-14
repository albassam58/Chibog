const state = {
	items: [],
	item: null
};
const getters = {};
const actions = {
	async fetch({ commit }, params) {
		try {
			let { data } = await axios.get(`/v1/stores?${ params }`);
			commit('SET_ITEMS', data.data);
		} catch (err) {
			throw err;
		}
	},
	async find({ commit }, id) {
		try {
			let { data } = await axios.get(`/v1/stores/${ id }`);
			commit('SET_ITEM', data.data);
		} catch (err) {
			throw err;
		}
	},
	async save({ commit }, form) {
		try {
			let { data } = await axios.post('/v1/stores', form);
			commit('SET_ITEM', data.data);
		} catch (err) {
			throw err;
		}
	},
	async update({ commit }, form) {
		try {
			let id = form.id;
			if (!id) {
				// display the key/value pairs
				for (var pair of form.entries()) {
					if (pair[0] == "id") id = pair[1];
				}
			}

			let { data } = await axios.post(`/v1/stores/${ id }`, form, { toasterMessage: 'Successfully updated!' });
			commit('SET_ITEM', data.data);
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	SET_ITEMS(state, items) {
		state.items = items;
	},
	SET_ITEM(state, item) {
		state.item = item;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}