import Vue from 'vue';
import 'vuetify/dist/vuetify.min.css';
import Vuetify from "vuetify";
import colors from 'vuetify/es5/util/colors';

Vue.use(Vuetify);

const opts = {
	theme: {
    	themes: {
      		light: {
        		primary: colors.blue,
        		secondary: colors.grey.darken1,
        		accent: colors.shades.black,
        		error: colors.red.accent3,
        		background: colors.indigo.lighten5
      		},
	      	dark: {
	        	primary: colors.blue.lighten3,
	        	background: colors.indigo.base
	      	}
    	}
  	}
};

export default new Vuetify(opts);