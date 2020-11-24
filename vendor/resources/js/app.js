/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

import router from "./router/index";
import App from "@components/App";
import vuetify from "./plugins/vuetify";
import store from "./store";
import Axios from "./axios"
import CustomDataTable from '@components/CustomDataTable'
Vue.component('custom-data-table', CustomDataTable);


window.axios = Axios;

// global functions
const MyPlugin = {
  install(Vue, options) {
    Vue.prototype.$serialize = (params) => {
      	return Object.entries(params).map(([key, val]) => `${key}=${encodeURIComponent(JSON.stringify(val))}`).join('&');
    }
  },
}
Vue.use(MyPlugin)

Vue.use(require('vue-moment'));

Vue.mixin({
	methods: {
		appendLocalStorage(key, newVal) {
		    // Parse any JSON previously stored in key
		    var existingEntries = JSON.parse(localStorage.getItem(key));

		    if (existingEntries == null) existingEntries = [];

		    // localStorage.setItem("entry", JSON.stringify(newVal));
		    // Save allEntries back to local storage
		    existingEntries.push(newVal);
		    localStorage.setItem(key, JSON.stringify(existingEntries));
		},
		removeItemLocalStorage(key, index) {
		    // Parse any JSON previously stored in key
		    var existingEntries = JSON.parse(localStorage.getItem(key));

		    if (existingEntries !== null) {
		    	existingEntries.splice(index, 1);
		    	localStorage.setItem(key, JSON.stringify(existingEntries));
		    }
		},
		capitalize(string) {
		   	return string.toLowerCase().replace(/^.|\s\S/g, function(str) { 
		       	return str.toUpperCase(); 
		   	});
		},
		completeAddress(data) {
			let vm = this;

			let street = data.street || data.customer_street;
			let barangay = data.barangay || data.customer_barangay;
			let city = data.city || data.customer_city;
			// capitalize is a mixin defined in /resources/js/app.js
			return vm.capitalize(`${ street }, ${ barangay }, ${ city}`);
		},
		scheduleDay(data) {
			let vm = this;

			let days = [
				{
					'short': 'Mon',
					'long': 'Monday',
					'color': 'grey lighten-4'
				},
				{
					'short': 'Tue',
					'long': 'Tuesday',
					'color': 'red'
				},
				{
					'short': 'Wed',
					'long': 'Wednesday',
					'color': 'green'
				},
				{
					'short': 'Thu',
					'long': 'Thursday',
					'color': 'yellow'
				},
				{
					'short': 'Fri',
					'long': 'Friday',
					'color': 'pink'
				},
				{
					'short': 'Sat',
					'long': 'Saturday',
					'color': 'black'
				},
				{
					'short': 'Sun',
					'long': 'Sunday',
					'color': 'red'
				}
			];

			let scheduleDays = data.schedule_day.split(",");
			
			let schedule = [];
			for (var i = 0; i < scheduleDays.length; i++) {
				if (scheduleDays[i] !== "") {
					schedule.push(days[i]);
				}
			}

			return schedule;
		}
	}
})

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    vuetify,
    store,
    render: h => h(App)
});