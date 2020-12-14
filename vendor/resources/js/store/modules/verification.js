const state = {
	verification: null,
	resent: null,
};
const getters = {};
const actions = {
	async verify({ commit }, url) {
		try {
			let { data } = await axios.get(url, { toasterError: false });
			commit('setVerification', data);
		} catch (err) {
			commit('setVerification', err.response ? err.response.data : err.statusText);
			throw err;
		}
	},
	async resend({ commit }, form) {
		try {
			let { data } = await axios.post('/v1/vendor/resend', form, { toasterMessage: 'Email verification is successfully resent.' });
			commit('setResent', data.data);
		} catch (err) {
			commit('setResent', err.response ? err.response.data.message : err.statusText);
			throw err;
		}
	}
};
const mutations = {
	setVerification(state, verification) {
		state.verification = verification;
	},
	setResent(state, resent) {
		state.resent = resent;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}