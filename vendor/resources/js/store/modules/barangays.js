const state = {
	barangaysByProvinceCity: []
};
const getters = {};
const actions = {
	async findByProvinceCity({ commit }, { provinceName, cityName }) {
		try {
			let { data } = await axios.get(`/v1/barangays/${ provinceName }/${ cityName }`);
			commit('setBarangaysByProvinceCity', data);
		} catch (err) {

		}
	}
};
const mutations = {
	setBarangaysByProvinceCity(state, barangaysByProvinceCity) {
		state.barangaysByProvinceCity = barangaysByProvinceCity;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}