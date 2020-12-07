const state = {
	foodTypes: [],
	foodType: {}
};
const getters = {};
const actions = {
	async fetch({ commit }) {
		try {
			let { data } = await axios.get('/v1/food-types');
			commit('setFoodTypes', data);
		} catch (err) {

		}
	}
};
const mutations = {
	setFoodTypes(state, foodTypes) {
		state.foodTypes = foodTypes;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}