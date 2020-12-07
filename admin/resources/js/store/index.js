import Vue from "vue";
import Vuex from "vuex";

// import currentUser from "./modules/currentUser";
// import items from "./modules/items";
// import stores from "./modules/stores";
import modules from "./modules";

Vue.use(Vuex);

export default new Vuex.Store({
	modules
	// modules: {
	// 	currentUser,
	// 	items,
	// 	stores
	// }
});