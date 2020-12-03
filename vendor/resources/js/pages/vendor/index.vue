<route>
  {
    "meta": {
      "requiresAuth": true
    }
  }
</route>
<template>
	<div>
		<v-container>
			<v-row>
				<v-col cols="6" class="d-flex flex-row">
					<div class="text-h4 mb-4">User Settings</div>
				</v-col>
				<v-col cols="6" class="d-flex flex-row-reverse">
					<v-btn color="secondary" dark @click="$router.back(-1)" text>
						<v-icon>mdi-arrow-left</v-icon>
						Back
					</v-btn>
				</v-col>
			</v-row>
			<v-simple-table>
		    	<template v-slot:default>
		      		<thead>
		        		<tr>
		          			<th class="text-left">
		            			Device
		          			</th>
		          			<th class="text-left">
		            			Last Used
		          			</th>
		          			<th class="text-right">
		          				<v-btn text color="error" @click="logoutAllDialog = true">Logout All</v-btn>
		          			</th>
		        		</tr>
		      		</thead>
		      		<tbody>
		        		<tr
		          			v-for="token in tokens"
		          			:key="token.name"
		          			v-if="token.tokenable_id == vendor.id"
	        			>
		          			<td class="text-left">{{ token.name }}</td>
		          			<td class="text-left">{{ (token.last_used_at ? token.last_used_at : token.created_at) | moment('MMM. DD, YYYY \\at hh:mm A') }} <span class="font-italic">({{ (token.last_used_at ? token.last_used_at : token.created_at) | moment('from', 'now') }})</span></td>
		          			<td class="text-right">
		          				<v-btn text color="error" @click="openLogoutDialog(token.id)">Logout</v-btn>
		          			</td>
		        		</tr>
		      		</tbody>
		    	</template>
		  	</v-simple-table>

		  	<v-dialog
		      v-model="logoutAllDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Logout All Devices
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
				        Are you sure to logout all the devices except this?
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="logoutAllDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		            		color="primary"
		            		text
		            		:disabled="logoutAllDialogDisable"
		            		@click="logoutAllDevices"
		          		>Logout All</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>

		  	<v-dialog
		      v-model="logoutDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Logout Device
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
				        Are you sure to logout the device?
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="logoutDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		            		color="primary"
		            		text
		            		:disabled="logoutDialogDisable"
		            		@click="logout"
		          		>Logout</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>

		</v-container>
	</div>
</template>

<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			id: null,
			logoutDialog: false,
			logoutDialogDisable: false,
			logoutAllDialog: false,
			logoutAllDialogDisable: false
		}),
		async created() {
			let vm = this;
			await vm.fetchTokens();
		},
		computed: {
            ...mapState({
            	tokens: state => state.currentVendor.tokens,
                vendor: state => state.currentVendor.vendor
            })
        },
        methods: {
            ...mapActions({
            	'fetchTokens': 'currentVendor/getVendorTokens',
            	'logoutDevice': 'currentVendor/logoutDevice',
            	'logoutAll': 'currentVendor/logoutAll',
            }),
            openLogoutDialog(id) {
            	let vm = this;

            	vm.id = id;
            	vm.logoutDialog = true;
            },
            async logout() {
            	let vm = this;
            	try {
            		vm.logoutDialogDisable = true;

            		await vm.logoutDevice(vm.id);

            		vm.logoutDialogDisable = true;
            		vm.logoutDialog = false;

            		vm.fetchTokens();
            	} catch (err) {
            		console.log(err);
            	}
            },
            async logoutAllDevices() {
            	let vm = this;
            	try {
            		vm.logoutAllDialogDisable = true;

            		await vm.logoutAll(vm.id);

            		vm.logoutAllDialogDisable = true;
            		vm.logoutAllDialog = false;

            		vm.fetchTokens();
            	} catch (err) {
            		console.log(err);
            	}
            }
        }
	}
</script>