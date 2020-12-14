import Vue from 'vue';
import 'vuetify/dist/vuetify.min.css';
import Vuetify from "vuetify";
import colors from 'vuetify/es5/util/colors';

Vue.use(Vuetify);

const opts = {
	theme: {
    	themes: {
      		light: {
        		primary: colors.orange.darken2,
            success: colors.orange.darken2,
        		secondary: colors.grey.darken1,
        		accent: colors.shades.black,
        		error: colors.red.darken3,
        		background: colors.orange.darken2
      		},
	      	dark: {
	        	primary: colors.orange.darken3,
	        	background: colors.orange.darken3,
            success: colors.orange.darken3,
	      	}
    	}
  	}
};

export default new Vuetify(opts);