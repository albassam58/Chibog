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
			<v-col cols="12">
				<div class="text-h4 mb-4">Stores</div>
			</v-col>
		</v-row>

		<custom-data-table
			module="stores/fetch"
		    :headers="headers"
		    :sortBy="sortBy"
      		:sortDesc="sortDesc"
      		ref="table"
        >
        	<template v-slot:filter>
        		<v-row>
        			<v-col cols="12" sm="12" md="4" lg="3">
		        		<v-select
		                  	:items="status"
		                  	label="Status"
		                  	item-text="name"
		          			item-value="id"
		          			multiple
		          			chips
		          			@change="filterStatus"
		                ></v-select>
		            </v-col>
		        </v-row>
        	</template>
        	<template v-slot:item="{ item }">
        		<td>
        			<a
	                    :href="imageLink(item)"
	                    target="blank"
	                >
	                    <v-img
	                        :src="imageLink(item)"
	                        aspect-ratio="1"
	                    >
	                        <template v-slot:placeholder>
	                            <v-row
	                                class="fill-height ma-0"
	                                align="center"
	                                justify="center"
	                            >
	                                <v-progress-circular
	                                    indeterminate
	                                    color="grey lighten-5"
	                                ></v-progress-circular>
	                            </v-row>
	                        </template>
	                    </v-img>
	                </a>
        		</td>
        		<td>{{ item.name }}</td>
        		<td>{{ item.description }}</td>
        		<td>{{ item.street }}</td>
        		<td>
        			<div>{{ item.vendor.full_name }}</div>
        			<div class="text-caption">
        				<v-icon x-small>mdi-cellphone</v-icon>
        				{{ item.vendor.mobile_number }}
        			</div>
        			<div class="text-caption">
        				<v-icon x-small>mdi-email</v-icon>
        				<a :href="`mailto:${ item.vendor.email }`">
        					{{ item.vendor.email }}
        				</a>
        			</div>
        			<div class="text-caption">
        				<v-icon x-small>mdi-link</v-icon>
        				<a
        					v-if="item.social_media"
        					:href="item.social_media"
        					target="blank"
        				>
        					{{ item.social_media }}
        				</a>
        				<span v-else color="error">No link provided</span>
        			</div>
        		</td>
        		<td>
                    <div :class="
                        {
                            'grey--text': item.status == 1,
                            'green--text': item.status == 2,
                            'red--text': item.status == 3
                        }
                    ">{{ item.status_value }}</div>
                    <div class="text-caption">{{ item.remarks }}</div>      
                </td>
        		<td class="text-center">
        			<v-tooltip bottom v-if="item.status !== 2">
	                    <template v-slot:activator="{ on, attrs }">
	                        <v-btn
	                            icon
	                            color="success"
	                            v-bind="attrs"
	                            v-on="on"
	                            @click="openApprovalDialog(item, 2)">
	                            <v-icon>mdi-check</v-icon>
	                        </v-btn>
	                    </template>
	                    <span>Approve</span>
	                </v-tooltip>
	                <v-tooltip bottom v-if="item.status !== 3">
	                    <template v-slot:activator="{ on, attrs }">
	                        <v-btn
	                            icon
	                            color="error"
	                            v-bind="attrs"
	                            v-on="on"
	                            @click="openApprovalDialog(item, 3)">
	                            <v-icon>mdi-close</v-icon>
	                        </v-btn>
	                    </template>
	                    <span>Reject</span>
	                </v-tooltip>
        		</td>
        	</template>
        </custom-data-table>

        <v-dialog
	      	v-model="approvalDialog"
	      	width="500"
	    >
	      	<v-card>
	        	<v-card-title>
	          		{{ approvalItem.newStatus == 2 ? "Approve" : "Reject" }}
	        	</v-card-title>

	        	<v-card-text class="my-4 text-body-1">
	          		Are you sure to {{ approvalItem.newStatus == 2 ? "approve" : "reject" }} this store?

                    <v-form
                        ref="form"
                    >
                        <v-textarea
                            v-model="remarks"
                            :rules="rules"
                            label="Reason..."
                            required
                            @keyup.shift.enter="approval(approvalItem.newStatus)"
                        ></v-textarea>
                    </v-form>
	        	</v-card-text>

	        	<v-divider></v-divider>

	        	<v-card-actions>
	          		<v-spacer></v-spacer>
	          		<v-btn
	          			color="grey"
	          			text
	          			@click="approvalDialog = false"
          			>Cancel</v-btn>
	          		<v-btn
	            		:color="approvalItem.newStatus == 2 ? 'success' : 'error'"
	            		text
	            		:disabled="approvalDialogDisable"
	            		@click="approval(approvalItem.newStatus)"
	          		>{{ approvalItem.newStatus == 2 ? "Approve" : "Reject" }}</v-btn>
	        	</v-card-actions>
	      	</v-card>
	    </v-dialog>

	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			approvalDialog: false,
			approvalDialogDisable: false,
			approvalItem: {
				newStatus: 0
			},
            remarks: null,
            headers: [
            	{ text: "Status", value: "status", align: ' d-none' },
            	{ text: "Picture", value: "logo" },
                { text: "Name", value: "name" },
                { text: "Description", value: "description", sortable: false },
                { text: "Street", value: "street", sortable: false },
                { text: "Vendor", value: "vendor.full_name", sortable: false },
                { text: "Status", value: "status_value", sortable: false },
                { text: "Action", value: "action", align: "center"}
            ],
            sortBy: ['status', 'name'],
            sortDesc: [false, false],
            status: [
            	{
            		id: 1,
            		name: 'Pending'
            	},
            	{
            		id: 2,
            		name: 'Approved'
            	},
            	{
            		id: 3,
            		name: 'Rejected'
            	}
            ],
            filters: {},
            rules: [
                v => !!v || 'Field is required',
            ],
            vendorUrl: null
        }),
        async created() {
        	let vm = this;

        	let { data } = await axios('/v1/vendor-url');
            vm.vendorUrl = data;
        },
        methods: {
        	...mapActions({
        		'update': 'stores/update'
        	}),
        	filterStatus(val) {
        		let vm = this;
        		vm.filters = { ...vm.filters, status: val };
        		vm.$refs.table.filterList(vm.filters);
        	},
        	imageLink(item) {
        		let vm = this;
        		let placeholder = "https://via.placeholder.com/300/FFFFFF/000000?text=No Image";

        		return item.logo ? vm.vendorUrl + "/" + item.logo : placeholder;
        	},
        	openApprovalDialog(item, status) {
        		let vm = this;

        		vm.approvalDialog = true;
        		vm.approvalItem = { ...item, newStatus: status };
        	},
        	async approval(newStatus) {
        		let vm = this;

                let valid = vm.$refs.form.validate();
                if (valid) {
                    vm.approvalDialogDisable = true;

            		await vm.update({ id: vm.approvalItem.id, status: newStatus, remarks: vm.remarks });
            		vm.$refs.table.loadItems(); // reload items

                    vm.remarks = null;
                    vm.$refs.form.resetValidation();

            		vm.approvalDialogDisable = false;
            		vm.approvalDialog = false;
                }
        	}
        }
	}
</script>