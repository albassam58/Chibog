const state = {
	sales: [],
	orders: [],
	totalSales: 0.00,
	totalOrders: 0,
	ordersPerStatus: [],
	params: {
        page: 1,
        search: '',
        filters: {}
    },
};
const getters = {};
const actions = {
	async sales({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/statistics/sales?${ params }`);
			commit('setSales', data.data);
		} catch (err) {

		}
	},
	async orders({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/statistics/orders?${ params }`);
			commit('setOrders', data.data);
		} catch (err) {

		}
	},
	async totalSales({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/statistics/total-sales?${ params }`);
			commit('setTotalSales', data.data);
		} catch (err) {

		}
	},
	async totalOrders({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/statistics/total-orders?${ params }`);
			commit('setTotalOrders', data.data);
		} catch (err) {

		}
	},
	async ordersPerStatus({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/statistics/orders-per-status?${ params }`);
			commit('setOrdersPerStatus', data.data);
		} catch (err) {

		}
	}
};
const mutations = {
	setSales(state, sales) {
		state.sales = sales;
	},
	setOrders(state, orders) {
		state.orders = orders;
	},
	setTotalSales(state, totalSales) {
		state.totalSales = totalSales;
	},
	setTotalOrders(state, totalOrders) {
		state.totalOrders = totalOrders;
	},
	setOrdersPerStatus(state, ordersPerStatus) {
		state.ordersPerStatus = ordersPerStatus;
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