const state = {
	provincesByRegion: [],
};
const getters = {};
const actions = {
	async findByRegion({ commit }, regionKey) {
		try {
			let { data } = await axios.get(`/v1/provinces/${ regionKey }`);
			commit('setProvincesByRegion', data ? data : []);
		} catch (err) {
			throw err;
		}
	},
};
const mutations = {
	setProvincesByRegion(state, provincesByRegion) {
		state.provincesByRegion = provincesByRegion;
	}
};

export default {
	namespaces: true,
	state,
	getters,
	actions,
	mutations
}