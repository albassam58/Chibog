const state = {
	regions: []
};
const getters = {};
const actions = {
	async fetch({ commit }) {
		try {
			let { data } = await axios.get('/v1/regions');
			commit('setRegions', data);
		} catch (err) {

		}
	}
};
const mutations = {
	setRegions(state, regions) {
		state.regions = regions;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}