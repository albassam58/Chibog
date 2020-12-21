const state = {
	items: [],
	customers: [],
	item: null
};
const getters = {
    // customers: (state) => {
    //     return state.customers;
    // }
};
const actions = {
	async fetch({ commit }, params) {
		try {
			let { data } = await axios.get(`/v1/chats?${ params }`);
			commit('SET_ITEMS', data.data);
		} catch (err) {
			throw err;
		}
	},
	async fetchCustomers({ commit }, params) {
		try {
			let { data } = await axios.get(`/v1/chats/customers?${ params }`);
			commit('SET_CUSTOMERS', data.data);
		} catch (err) {
			throw err;
		}
	},
	async find({ commit }, id) {
		try {
			let { data } = await axios.get(`/v1/chats/${ id }`);
			commit('SET_ITEM', data.data);
		} catch (err) {
			throw err;
		}
	},
	async save({ commit }, form) {
		try {
			let headers = {
				'X-CSRF-TOKEN': document.getElementsByName("csrf-token")[0].getAttribute('content')
			}
			let { data } = await axios.post('/v1/chats', form, { headers: headers, toasterSuccess: false });
			commit('SET_ITEM', data.data);
		} catch (err) {
			throw err;
		}
	},
	async update({ commit }, form) {
		try {
			let headers = {
				'X-CSRF-TOKEN': document.getElementsByName("csrf-token")[0].getAttribute('content')
			}
			let { data } = await axios.put(`/v1/chats/${ form.id }`, form, { headers: headers, toasterSuccess: false });
			commit('SET_ITEM', data.data);
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	SET_CUSTOMERS(state, customers) {
		state.customers = customers;
	},
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