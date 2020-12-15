<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>
	<v-container>
		<v-row>
			<v-col
				cols="6"
				class="d-flex flex-row"
			>
				<div class="text-h4 mb-4 primary--text">Maligayang pagbabalik sa <strong>ENKA</strong></div>
			</v-col>
		</v-row>
		<v-row class="text-subtitle">
			<v-col cols="6">
				<v-card
				    class="mx-auto"
				>
				  	<v-card-title class="primary--text">
				  		Mga limitasyon ng pag gamit ng ENKA:
				  	</v-card-title>

				    <v-card-text class="text--primary">
				      	<li>Hanggang isa (1) lang ang pwedeng i register na store</li>
						<li>Hanggang labing lima (15) lang ang pwedeng i register na menu</li>
				    </v-card-text>
				</v-card>
			</v-col>
			<v-col cols="6">
				<v-card
				    class="mx-auto"
				>
				  	<v-card-title class="primary--text">
				  		Busilak ba ang iyong PUSO?
				  	</v-card-title>

				  	<v-card-subtitle>
				  		Magdonate na sa aming GCASH o PAYMAYA
				  	</v-card-subtitle>

				    <v-card-text class="text--primary">
				    	<v-row>
				    		<v-col cols="12" sm="6">
				      			QR CODE GCASH
				      		</v-col>
				      		<v-col cols="12" sm="6">
				      			QR CODE PAYMAYA
				      		</v-col>
				      	</v-row>
				    </v-card-text>
				</v-card>
			</v-col>
		</v-row>

		<v-divider />

		<v-row>
			<v-col cols="12">
				<div class="text-h4 mb-4 primary--text">Announcements</div>
			</v-col>
			<v-col cols="12" sm="12" md="4" v-for="n in 4" :key="n">
				<v-card
				    class="mx-auto"
				    max-width="400"
				>
				    <v-img
				      	class="white--text align-end"
				      	height="200px"
				      	src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
				    >
				      	<v-card-title>Top 10 Australian beaches</v-card-title>
				    </v-img>

				    <v-card-subtitle class="pb-0">
				      	Number 10
				    </v-card-subtitle>

				    <v-card-text class="text--primary">
				      	<div>Whitehaven Beach</div>

				      	<div>Whitsunday Island, Whitsunday Islands</div>
				    </v-card-text>

				    <v-card-actions>
				      	<v-btn
				        	color="orange"
				        	text
				      	>
				        	Share
				      	</v-btn>

				      	<v-btn
				        	color="orange"
				        	text
				      	>
				        	Explore
				      	</v-btn>
				    </v-card-actions>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			icons: [
		        'mdi-facebook',
		        'mdi-twitter',
		        'mdi-linkedin',
		        'mdi-instagram',
		      ],
		}),
		async created() {
			let vm = this;

			if (!localStorage.getItem('authenticated')) {
				vm.$router.push('/login');
			}

			await vm.getVendor();

            // check if email is verified, if not, redirect to email verification
            if (vm.vendor && (!vm.vendor.email_verified_at || !vm.vendor.mobile_verified_at)) {
                if (vm.$route.path != '/verification' && vm.$route.path != "/vendor") {
                    vm.$router.push('/verification');
                }
            }
		},
		computed: {
			...mapState({
				vendor: state => state.currentVendor.vendor,
			})
		},
		methods: {
			...mapActions({
				'getVendor': 'currentVendor/getVendor'
			})
		},
	}
</script>