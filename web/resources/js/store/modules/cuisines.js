const state = {
	cuisines: [],
	cuisine: {}
};
const getters = {};
const actions = {
	async fetch({ commit }) {
		try {
			let { data } = await axios.get('/v1/cuisines');
			commit('setCuisines', data);
		} catch (err) {

		}
	}
};
const mutations = {
	setCuisines(state, cuisines) {
		state.cuisines = cuisines;
	},
	setCuisine(state, cuisine) {
		state.cuisine = cuisine;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}