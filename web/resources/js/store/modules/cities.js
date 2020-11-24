const state = {
	citiesByProvince: [],
};
const getters = {};
const actions = {
	async findByProvince({ commit }, provinceName) {
		try {
			let { data } = await axios.get(`/v1/cities/${ provinceName }`);
			commit('setCitiesByProvince', data ? data : []);
		} catch (err) {

		}
	}
};
const mutations = {
	setCitiesByProvince(state, citiesByProvince) {
		state.citiesByProvince = citiesByProvince;
	}
};

export default {
	namespaces: true,
	state,
	getters,
	actions,
	mutations
}