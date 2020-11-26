const state = {
	orderNotifications: [],
	popupNotifications: [],
	orderNotification: {},
	totalUnread: 0,
	params: {
        page: 1,
        search: '',
        filters: {}
    },
};
const getters = {};
const actions = {
	async fetchVendor({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/order-notifications/vendor?${ params }`);
			commit('setOrderNotifications', data.data);
		} catch (err) {
			throw err;
		}
	},
	async fetchVendorPopup({ commit }) {
		try {
			let { data } = await axios.get(`/v1/order-notifications/vendor/popup`);
			commit('setPopupNotifications', data.data);
		} catch (err) {
			throw err;
		}
	},
	async countUnread({ commit }) {
		try {
			let { data } = await axios.get(`/v1/order-notifications/count/unread`);
			commit('setTotalUnread', data.data);
		} catch (err) {
			throw err;
		}
	},
	async find({ commit }, id) {
		try {
			let { data } = await axios.get(`/v1/order-notifications/${ id }`);
			commit('setOrderNotification', data.data);
		} catch (err) {
			throw err;
		}
	},
	async readChecked({ commit }, ids) {
		try {
			await axios.put(`/v1/order-notifications/read/checked/${ ids }`);
		} catch (err) {
			throw err;
		}
	},
	async deleteChecked({ commit }, ids) {
		try {
			await axios.delete(`/v1/order-notifications/delete/checked/${ ids }`);
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	setOrderNotifications(state, orderNotifications) {
		state.orderNotifications = orderNotifications;
	},
	setPopupNotifications(state, popupNotifications) {
		state.popupNotifications = popupNotifications;
	},
	setOrderNotification(state, orderNotification) {
		state.orderNotification = orderNotification;
	},
	setTotalUnread(state, total) {
		state.totalUnread = total;
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