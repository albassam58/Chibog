const state = {
    orders: {},
    order: {},
    params: {
        page: 1,
        search: '',
        filters: {}
    },
};
const getters = {
    order: (state) => {
        return state.order;
    }
};
const actions = {
    async fetchByVendor({ commit }) {
        try {
            let params = this._vm.$serialize(state.params);
            let { data } = await axios.get(`/v1/orders/vendor?${ params }`);
            commit('setOrders', data.data);
        } catch (err) {
            console.log(err)
        }
    },
    async save({ commit }, form) {
        try {
            let { data } = await axios.post('/v1/orders', form);
            commit('setOrder', data.data);
        } catch (err) {

        }
    },
    async update({ commit }, { id, form }) {
        try {
            let { data } = await axios.put(`/v1/orders/${ id }`, form);
            commit('setOrder', data.data);
        } catch (err) {

        }
    },
    async updateStatus({ commit }, { transactionId, status }) {
        try {
            let request = {
                transactionId: transactionId,
                status: status
            };
            let { data } = await axios.post('/v1/orders/update/status', request);
        } catch (err) {

        }
    },
    async destroy({ commit }, id) {
        try {
            let { data } = await axios.delete(`/v1/orders/${ id }`);
        } catch (err) {

        }
    }
};
const mutations = {
	setOrders(state, orders) {
		state.orders = orders;
	},
    setCurrentPage(state, page) {
        state.orders.current_page = page;
    },
    setParams(state, params) {
        state.params = { ...state.params, ...params };
    },
    setOrder(state, order) {
        state.order = order;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
