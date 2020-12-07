const state = {
	verification: {},
	resent: null,
};
const getters = {};
const actions = {
	async verify({ commit }, url) {
		try {
			let { data } = await axios.get(url);

			let vendor = JSON.parse(localStorage.getItem('current_vendor'));
			if (vendor) {
				vendor.email_verified_at = new Date().toJSON().slice(0,19).replace(/T/g,' ');
				localStorage.setItem('current_vendor', JSON.stringify(vendor));
			}
			commit('setVerification', data);
		} catch (err) {
			commit('setVerification', err.response.data);
			throw err;
		}
	},
	async resend({ commit }, { id, email }) {
		try {
			let { data } = await axios.get(`/v1/vendor/resend/${ id }/${ email }`);
			commit('setResent', data.data);
		} catch (err) {
			commit('setResent', err.response ? err.response.data : err.message);
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