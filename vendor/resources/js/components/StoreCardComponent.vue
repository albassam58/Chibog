<template>
	<div>	
		<v-card
			v-if="store"
	    	class=""
	  	>
	    	<template slot="progress">
	      		<v-progress-linear
	        		color="deep-purple"
	        		height="10"
	        		indeterminate
	      		></v-progress-linear>
	    	</template>

	    	<!-- @vdropzone-removed-file="removeFile" -->
	    	<vue-dropzone
	    		v-if="hasUpload"
	    		id="dropzone"
	  			:ref="'vueDropzone' + store.id"
	  			:options="dropzoneOptions"
	  			@vdropzone-file-added="fileAdded"
	  			@vdropzone-sending="sendingEvent"
	  			@vdropzone-error="uploadError"
	  			@vdropzone-success="uploadSuccess"
	  		></vue-dropzone>
	  		<!-- <v-btn @click="upload">Upload</v-btn> -->

	    	<v-img
	    		height="300"
	    		v-else
	    		:src="store.logo ? `/${ store.logo }` : `https://via.placeholder.com/450/277135/FFFFFF?text=${ store.name.charAt(0) }`"
	    	></v-img>

	    	<v-card-title>
	    		{{ capitalize(store.name) }}
	    		<v-chip
	    			:color="isAvailable(store).color"
	    			small
	    			dark
	    			class="ml-2"
	    		>
	    			{{ isAvailable(store).text }}
	    		</v-chip>
	    	</v-card-title>

	    	<v-card-text>
	      		<v-row
	        		align="center"
	        		class="mx-0"
	      		>
		        	<v-rating
		          		:value="store.rate"
		          		background-color="orange lighten-3"
				         color="orange"
		          		dense
		          		half-increments
		          		readonly
		          		size="14"
		        	></v-rating>

		        	<div class="grey--text ml-4">
		          		<v-btn
		          			v-if="hasRating"
		          			text
		          			color="primary"
		          			@click="goto('reviews')"
		          		>{{ store.rate }} ({{ store.reviews_count }})</v-btn>
		          		<div
		          			v-else
		          			text
		          		>{{ store.rate }} ({{ store.reviews_count }})</div>
		        	</div>
	      		</v-row>

	      		<div class="my-4">
		      		<div class="subtitle-1">
		        		<!-- $ • Italian, Cafe -->
		        		<v-icon>mdi-cellphone</v-icon>
		        		{{ store.vendor ? store.vendor.mobile_number : "" }}
		      		</div>
		      		<div class="subtitle-1">
		        		<!-- $ • Italian, Cafe -->
		        		<v-icon>mdi-email</v-icon>
		        		{{ store.vendor ? store.vendor.email : "" }}
		      		</div>
		      		<div class="subtitle-1">
		        		<!-- $ • Italian, Cafe -->
		        		<v-icon>mdi-map-marker</v-icon>
		        		{{ completeAddress(store) }}
		      		</div>
		      	</div>

	      		<div>{{ store.description }}</div>
	    	</v-card-text>

	    	<v-divider class="mx-4"></v-divider>

	    	<v-card-title>Availability</v-card-title>

	    	<v-card-text>
				<div>
			    	<v-icon>mdi-calendar-clock</v-icon>
			    	{{ new Date(`2019-05-05 ${ store.schedule_time_in }`) | moment("hh:mm A") }} - {{ new Date(`2019-05-05 ${ store.schedule_time_out }`) | moment("hh:mm A") | moment("hh:mm A") }}
			    </div>
	    	</v-card-text>

	    	<v-card-text>
				<v-chip
					v-for="(day, index) in scheduleDay(store)"
					:key="day.short"
			      	:color="day.color"
			      	:dark="day.short == 'Mon' || day.short == 'Thu' ? false : true"
			    >
			      	{{ day.short }}
			    </v-chip>
			    
	    	</v-card-text>

	    	<v-card-actions>
	    		<v-btn
	        		color="deep-purple lighten-2"
	        		text
	        		v-if="hasView"
	        		@click="$router.push(`/stores/${ store.id }`)"
	      		>
	        		View
	      		</v-btn>
	      		<v-btn
	        		color="deep-purple lighten-2"
	        		text
	        		v-if="hasEdit"
	        		@click="$router.push(`/stores/${ store.id }/edit`)"
	      		>
	        		Edit
	      		</v-btn>
	    	</v-card-actions>
	 	</v-card>
	</div>
</template>

<script type="text/javascript">
	import VueDropzone from 'vue2-dropzone'
	import 'vue2-dropzone/dist/vue2Dropzone.min.css'

	export default {
		components: {
			VueDropzone
		},
		props: {
			store: {
				type: Object,
				default: {}
			},
			hasView: {
				type: Boolean,
				default: false
			},
			hasEdit: {
				type: Boolean,
				default: false
			},
			hasRating: {
				type: Boolean,
				default: false
			},
			hasUpload: {
				type: Boolean,
				default: false
			}
		},
		data() {
			return {
				currentFile: {},
				dropzoneOptions: {
			        url: '/api/v1/stores/upload',
			        // autoProcessQueue: false,
			        thumbnailWidth: 300, //px
			        thumbnailHeight: 300,
			        maxFilesize: 5, //mb
			        maxFiles: 1,
			        acceptedFiles: "image/*",
			        // addRemoveLinks: true, // add a link to every file preview to remove
			        headers: { "Authorization": localStorage.getItem('api_token') }
			    },
			    currentStore: this.store
			}
		},
		mounted() {
			let vm = this;

			// if with picture, manually add
			if (!_.isEmpty(vm.currentStore.picture) && vm.hasUpload) {
				var file = {
					size: vm.currentStore.picture.size,
					name: vm.currentStore.picture.name,
					type: vm.currentStore.picture.type
				};
			    var url = `/${ vm.currentStore.logo }` ;
			    vm.$refs[`vueDropzone${ vm.currentStore.id }`].manuallyAddFile(file, url);
			}
		},
		methods: {
			fileAdded(file) {
				let vm = this;

				if (!_.isEmpty(vm.currentFile)) {
					vm.$refs[`vueDropzone${ vm.currentStore.id }`].removeFile(vm.currentFile);
				}

				vm.currentFile = file;
			},
			goto(refName) {
		        var element = this.$parent.$refs[refName];
		        var top = element.$el.offsetTop; 
		        window.scrollTo({
		        	top: top,
		        	behavior: 'smooth'
		        });
		    },
		    sendingEvent(file, xhr, formData) {
		    	let vm = this;
		      	formData.append("store_id", vm.currentStore.id);
		    },
		    upload() {
		    	let vm = this;
		    	vm.$refs[`vueDropzone${ vm.currentStore.id }`].processQueue();
		    },
		    uploadSuccess(file, response) {
		    	let vm = this;
		    	vm.store.picture = response.data.picture;
		    	vm.store.logo = response.data.logo;
		    	vm.currentStore.picture = response.data.picture;
		    	vm.currentStore.logo = response.data.logo;
		    },
		    uploadError(file, _message, xhr) {
		    	const message = JSON.parse(xhr.response).message
			    const elements = document.querySelectorAll('.dz-error-message span')
			    const lastElement = elements[elements.length - 1]
			    lastElement.textContent = message;
		    },
		    removeFile(file, error, xhr) {
		    	// if manually added, remove from server
		    	if (file.manuallyAdded) {

		    	}
		    },
		    isAvailable(store) {
		    	// check if open or closed
		    	let vm = this;
		    	let days = store.schedule_day.split(",");
		    	let timein = vm.$moment(store.schedule_time_in, 'h:mma');
		    	let timeout = vm.$moment(store.schedule_time_out, 'h:mma');

		    	// get the day of the week, e.g. Mon = 1, Sun = 7
		    	let currentDay = vm.$moment(new Date()).weekday();
		    	let currentTime = vm.$moment(new Date(), 'h:mma');

		    	// check if day is available
		    	let isDayAvailable = days[currentDay - 1];

				if (isDayAvailable && timein.isBefore(currentTime) && currentTime.isBefore(timeout)) {
					return {
						color: 'green',
						text: 'OPEN'
					};
				}

				return {
					color: 'red',
					text: 'CLOSED'
				};
		    }
		},
	}
</script>