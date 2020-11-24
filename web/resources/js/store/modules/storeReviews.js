const state = {
	reviews: [],
	review: {},
	params: {
		page: 1,
		search: '',
		filters: {}
	}
};
const getters = {};
const actions = {
	async fetch({ commit }) {
		try {
			let params = this._vm.$serialize(state.params);
			let { data } = await axios.get(`/v1/store-reviews?${ params }`);

			commit('setReviews', data.data);
		} catch (err) {
			throw err
		}
	},
	async save({ commit }, form) {
		try {
			let { data } = await axios.post('/v1/store-reviews', form);
			commit('setReview', data.data);
		} catch (err) {
			throw err
		}
	}
};
const mutations = {
	setReviews(state, reviews) {
		state.reviews = reviews;
	},
	setReview(state, review) {
		state.review = review;
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