const state = {
	otp: null
};
const getters = {};
const actions = {
	async send({ commit }, form) {
		try {
			let { data } = await axios.post('/v1/otp/send', form, { toasterMessage: 'Successfully sent!' });
			commit('SET_OTP', data.data);
		} catch (err) {
			throw err;
		}
	},
	async verify({ commit }, form) {
		try {
			let { data } = await axios.post('/v1/otp/verify', form, { toasterMessage: 'Successfully verified!' });
			commit('SET_OTP', data.data);
		} catch (err) {
			throw err;
		}
	}
};
const mutations = {
	SET_OTP(state, otp) {
		state.otp = otp;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}