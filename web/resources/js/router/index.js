import routes from 'vue-auto-routing'

import Vue from 'vue'
import Router from 'vue-router'
import store from '../store'

Vue.use(Router);

const router = new Router({
	routes,
    mode: "history",
    base: "/"
});

export default router;

router.beforeResolve( ( to, from, next ) => {
	// check if correct parameters and not root
	if (!to.matched.length && to.path !== "/") {
		next({ name: 'error-404' });
		return;
	}
	
	// if requires auth but no token return access error
	if (_.has(to.meta, 'requiresAuth') && !localStorage.getItem('api_token')) {
		next({ name: 'login' });
		return;
	} else if (to.name == 'login' && localStorage.getItem('api_token')) {
		next({ path: "/" });
		return;
	}

	// check if page is validated and not root
	if (to.path !== "/") {
		const page = to.matched[0].components.default;
		const { validate } = page;
		if (validate != null) {
			if (!validate(to)) {
		      	next({ name: 'error-404' });
		      	return;
		  	}
		}
	}

	next();
});