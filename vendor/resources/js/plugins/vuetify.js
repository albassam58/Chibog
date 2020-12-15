import Vue from 'vue';
import 'vuetify/dist/vuetify.min.css';
import Vuetify from "vuetify";
import colors from 'vuetify/es5/util/colors';

Vue.use(Vuetify);

const opts = {
	theme: {
    	themes: {
      		light: {
        		primary: "#f24e1e",//colors.orange.darken2,
            success: "#f24e1e",//colors.orange.darken2,
        		secondary: colors.grey.darken1,
        		accent: colors.shades.black,
        		error: colors.red.darken3,
            header: '#FFFFFF',
        		background: "#f24e1e"//colors.orange.darken2
      		},
	      	dark: {
	        	primary: "#f24e1e",//colors.orange.darken3,
	        	background: "#f24e1e",//colors.orange.darken3,
            success: "#f24e1e",//colors.orange.darken3,
	      	}
    	}
  	}
};

export default new Vuetify(opts);