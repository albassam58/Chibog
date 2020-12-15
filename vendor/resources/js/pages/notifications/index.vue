<template>
	<div>
		<v-container>
			<v-row>
				<v-col cols="6" class="d-flex flex-row">
					<div class="text-h4 mb-4">Notifications</div>
				</v-col>
				<v-col cols="6" class="d-flex flex-row-reverse">
					<v-btn color="default" @click="$router.back(-1)" text>
						<v-icon>mdi-arrow-left</v-icon>
						Back
					</v-btn>
				</v-col>
			</v-row>
			<v-row>
				<v-col cols="12" sm="12" md="5">
					<v-card>
						<v-card-title>
							<v-checkbox
								v-model="checkAll"
			                	@change="checkAllNotification($event)"
			                	:input-value="active"
			                  	color="deep-purple accent-4"
			                ></v-checkbox>
						    Notifications
						</v-card-title>
						<v-card-actions>
							<v-spacer />
							<v-btn
								text
								x-small
								color="primary"
								@click="markAsReadDialog = true"
							>
								Mark as read
							</v-btn>
							<v-btn
								text
								x-small
								color="error"
								@click="deleteDialog = true"
							>
								Delete
							</v-btn>
						</v-card-actions>
						<v-list-item-group
							v-model="selectedNotification"
							color="indigo"
						>
		                    <v-list-item v-for="(notification, index) in orderNotifications" :key="notification.id">
		                    		<v-list-item-action>
						                <v-checkbox
						                	v-model="checkedNotification"
						                	:value="notification.id"
						                  	color="deep-purple accent-4"
						                ></v-checkbox>
						            </v-list-item-action>
			                        <v-list-item-content :class="notification.status == 1 ? 'font-weight-bold' : ''" @click="read(index, notification.id)" active>
			                            <v-list-item-title>
			                                {{ fullname(notification) }}
			                                <span style="float: right;" class="text-caption">
			                                  {{ notification.created_at | moment("from", "now") }}
			                                </span>
			                            </v-list-item-title>
			                            <v-list-item-subtitle>
			                                {{ getStore(notification) }}
			                                &nbsp;•&nbsp;{{ notification.message }}
			                            </v-list-item-subtitle>
			                        </v-list-item-content>
		                    </v-list-item>
		                </v-list-item-group>

		                <v-divider class="my-4" />

		            	<paginate
		            		action="orderNotifications/fetchVendor"
		            		collection="orderNotifications"
		            	></paginate>
		            </v-card>
				</v-col>
				<v-col cols="12" sm="12" md="7" v-if="$route.query && $route.query.id">
					<v-card v-for="(items, index) in order" :key="index">
						<v-card-title>
							{{ index }}
						</v-card-title>
						<v-card-subtitle>
							<strong>
								<router-link :to="`/stores/${ getStoreId(orderNotification) }`">{{ getStore(orderNotification) }}</router-link>
							</strong>
							&nbsp;•&nbsp;{{ orderNotification.message }}
						</v-card-subtitle>
						<v-card-text>
							<v-simple-table>
							    <template v-slot:default>
							      	<thead>
							        	<tr>
							          		<th class="text-left">
							            		Name
							          		</th>
							          		<th class="text-center">
							            		Quantity
							          		</th>
							          		<th class="text-right">
							            		Total
							          		</th>
							        	</tr>
							      	</thead>
							      	<tbody>
							        	<tr
							          		v-for="item in items"
							          		:key="item.name"
							        	>
							          		<td class="text-left">{{ item.name }}</td>
							          		<td class="text-center">{{ item.quantity }}</td>
							          		<td class="text-right">{{ (item.quantity * item.amount).toFixed(2) }}</td>
							        	</tr>
							      	</tbody>
							    </template>
							</v-simple-table>
						</v-card-text>
						<v-card-actions>
							<v-spacer />
							<v-btn text color="primary" @click="$router.push(`/orders?search=${ getTransactionId() }`)">View Full Details</v-btn>
						</v-card-actions>
					</v-card>
				</v-col>
			</v-row>

			<v-dialog
		      v-model="deleteDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Delete
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
		          		Are you sure to delete all checked notification?
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="deleteDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		          			rounded
		            		color="error"
		            		:disabled="deleteDialogDisable"
		            		@click="deleteChecked"
		            		class="px-8"
		          		>Delete</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>

		    <v-dialog
		      v-model="markAsReadDialog"
		      width="500"
		    >
		      	<v-card>
		        	<v-card-title>
		          		Mark As Read
		        	</v-card-title>

		        	<v-card-text class="my-4 text-body-1">
		          		Are you sure to mark all checked notification as read?
		        	</v-card-text>

		        	<v-divider></v-divider>

		        	<v-card-actions>
		          		<v-spacer></v-spacer>
		          		<v-btn
		          			color="grey"
		          			text
		          			@click="markAsReadDialog = false"
	          			>Cancel</v-btn>
		          		<v-btn
		            		color="primary"
		            		rounded
		            		:disabled="markAsReadDialogDisable"
		            		@click="readChecked"
		            		class="px-8"
		          		>Read</v-btn>
		        	</v-card-actions>
		      	</v-card>
		    </v-dialog>

		</v-container>
	</div>
</template>

<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'
	import Paginate from '@components/PaginateComponent'

	export default {
		components: {
			Paginate
		},
		data: () => ({
			order: {},
			itemsPerPage: 10,
			checkAll: false,
			active: false,
			deleteDialog: false,
			deleteDialogDisable: false,
			markAsReadDialog: false,
			markAsReadDialogDisable: false,
			selectedNotification: null,
			checkedNotification: [],
		}),
		async created() {
			let vm = this;

			vm.$store.commit('orderNotifications/setParams', { itemsPerPage: vm.itemsPerPage });
			await vm.fetchVendor();

			if (vm.$route.query && vm.$route.query.id) {
				try {
					await vm.find(vm.$route.query.id);
					vm.order = JSON.parse(vm.orderNotification.payload);

					vm.order = _.groupBy(vm.order, function(object) {
						return object.customer_first_name + " " + object.customer_last_name;
					});

					vm.selectedNotification = _.findIndex(vm.orderNotifications, function(o) { return o.id == vm.$route.query.id });
				} catch (err) {
					vm.$router.push('/notifications');
				}
			}
		},
		computed: {
            ...mapState('orderNotifications', {
            	orderNotifications: state => state.orderNotifications.data,
                orderNotification: state => state.orderNotification
            })
        },
        methods: {
            ...mapActions({
            	'fetchVendor': 'orderNotifications/fetchVendor',
                'find': 'orderNotifications/find',
                'fetchVendorPopup': 'orderNotifications/fetchVendorPopup',
                'countUnread': 'orderNotifications/countUnread',
                'deleteCheckedNotification': 'orderNotifications/deleteChecked',
                'readCheckedNotification': 'orderNotifications/readChecked'
            }),
            getStore(notification) {
                let notif = JSON.parse(notification.payload)[0]
                return notif.store ? notif.store.name : "";
            },
            getStoreId(notification) {
            	let notif = JSON.parse(notification.payload)[0]
                return notif.store_id;
            },
            getTransactionId() {
            	let vm = this;
            	let notif = JSON.parse(vm.orderNotification.payload)[0]
            	return notif.transaction_id;
            },
            fullname(notification) {
                let notif = JSON.parse(notification.payload)[0]
                return notif.customer_first_name + " " + notif.customer_last_name;
            },
            checkAllNotification(event) {
            	let vm = this;

            	if (event) {
	            	let ids = _.map(vm.orderNotifications, 'id');
	            	vm.checkedNotification = ids;
	            } else {
	            	vm.checkedNotification = [];
	            }
            },
            async read(index, id) {
            	let vm = this;

            	try {
	            	await vm.find(id);

	            	vm.order = JSON.parse(vm.orderNotification.payload);

					vm.order = _.groupBy(vm.order, function(object) {
						return object.customer_first_name + " " + object.customer_last_name;
					})

	            	await vm.fetchVendorPopup();
					await vm.countUnread();

	                let url = `/notifications?id=${ id }`;
	                if (vm.$route.fullPath !== url) vm.$router.push(url);
	            } catch (err) {

	            }
            },
            async deleteChecked() {
            	let vm = this;

            	try {
	            	vm.deleteDialogDisable = true;

	            	if (vm.checkedNotification.length) {
		            	await vm.deleteCheckedNotification(vm.checkedNotification);
		            	await vm.fetchVendor();
		            	await vm.fetchVendorPopup();
						await vm.countUnread();

						let url = `/notifications`;
		                if (vm.$route.fullPath !== url) vm.$router.push(url);
		            }

		            vm.checkAll = false;
					vm.checkedNotification = [];
	                vm.deleteDialogDisable = false;
	            	vm.deleteDialog = false;
	            } catch (err) {
	            	vm.checkAll = false;
					vm.checkedNotification = [];
	            	vm.deleteDialogDisable = false;
	            	vm.deleteDialog = false;
	            }
            },
            async readChecked() {
            	let vm = this;

            	try {
	            	vm.markAsReadDialogDisable = true;

	            	if (vm.checkedNotification.length) {
		            	await vm.readCheckedNotification(vm.checkedNotification);
		            	await vm.fetchVendor();
		            	await vm.fetchVendorPopup();
						await vm.countUnread();
					}

					vm.checkAll = false;
					vm.checkedNotification = [];
					vm.markAsReadDialogDisable = false;
	            	vm.markAsReadDialog = false;
	            } catch (err) {
	            	vm.checkAll = false;
					vm.checkedNotification = [];
					vm.markAsReadDialogDisable = false;
	            	vm.markAsReadDialog = false;
	            }
            }
        }
	}
</script>