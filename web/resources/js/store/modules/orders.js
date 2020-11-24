const state = {
    orders: [],
    searchedOrders: [],
    countStoreInOrders: 0,
    order: {},
    hasOrder: false,
    params: {
        page: 1,
        search: '',
        filters: {}
    }
};
const getters = {
    order: (state) => {
        return state.order;
    }
};
const actions = {
    async fetch({ commit }) {
        try {
            let params = this._vm.$serialize(state.params);
            let { data } = await axios.get(`/v1/orders?${ params }`);
            commit('setOrders', data.data);
        } catch(err) {
            throw err;
        }
    },
    async check({ commit }) {
        try {
            let params = this._vm.$serialize(state.params);
            let { data } = await axios.get(`/v1/orders/check?${ params }`);
            commit('setHasOrder', data.data);
        } catch(err) {
            throw err;
        }
    },
    async user({ commit }) {
        try {
            let params = this._vm.$serialize(state.params);
            let { data } = await axios.get(`/v1/orders/user?${ params }`);
            commit('setOrders', data.data);
        } catch(err) {
            throw err;
        }
    },
	async search({ commit }, query) {
		try {
			let { data } = await axios.get(`/v1/orders/search/${ query }`);
			commit('setSearchedOrders', data.data);
		} catch (err) {
            throw err;
		}
	},
    async save({ commit }, { form, item }) {
        try {
            let request = {
                form: form,
                item: item
            };
            let { data } = await axios.post('/v1/orders', request);
            commit('setOrder', data.data);
        } catch (err) {
            throw err;
        }
    },
    async saveByStore({ commit }, { form, items }) {
        try {
            let { data } = await axios.post('/v1/orders/store', {
                form: form,
                items: items
            });
            commit('setOrders', data.data);
        } catch (err) {
            throw err;
        }
    },
    async update({ commit }, { form, item }) {
        try {
            let request = {
                form: form,
                item: item
            };
            let { data } = await axios.put(`/v1/orders/${ item.id }`, request);
            commit('setOrder', data.data);
        } catch (err) {
            throw err;
        }
    },
    async updateStatus({ commit }, { transactionId, status }) {
        try {
            let request = {
                transactionId: transactionId,
                status: status
            };
            let { data } = await axios.put('/v1/orders/update/status', request);
        } catch (err) {
            throw err;
        }
    },
    async updateByStore({ commit }, { form, items }) {
        try {
            let { data } = await axios.put(`/v1/orders/store`, {
                form: form,
                items: items
            });
            commit('setOrders', data.data);
        } catch (err) {
            throw err;
        }
    },
    async destroy({ commit }, id) {
        // destroy a single row in orders table
        try {
            let { data } = await axios.delete(`/v1/orders/${ id }`);
            commit('setOrder', data.data);
        } catch (err) {
            throw err;
        }
    },
    async destroyByStore({ commit }, items) {
        // destroy an orders with specified store_id
        try {
            let ids = _.map(items, function(object) {
                return 'id[]=' + object.id
            }).join('&');
            let { data } = await axios.delete(`/v1/orders/store?${ ids }`);
            commit('setOrders', []);
        } catch (err) {
            throw err;
        }
    }
};
const mutations = {
	setOrders(state, orders) {
		state.orders = orders;
	},
    setSearchedOrders(state, orders) {
        state.searchedOrders = orders;
    },
    setOrder(state, order) {
        state.order = order;
    },
    setHasOrder(state, hasOrder) {
        state.hasOrder = hasOrder;
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
