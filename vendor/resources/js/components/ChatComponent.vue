<template>
	<v-container
      class="fill-height pa-0 "
    >
      	<v-row class="no-gutters elevation-4">
        	<v-col
          		cols="12" sm="4"
          		class="flex-grow-1 flex-shrink-0"
          		style="border-right: 1px solid #0000001f;"
        	>
          		<v-responsive
            		class="overflow-y-auto fill-height"
            		height="500"
          		>
            		<v-list subheader>
              			<v-list-item-group v-model="activeChat">
                			<template v-for="(customer, index) in customers">
	                  			<v-list-item
	                    			:key="`parent${index}`"
	                    			:value="customer.id"
	                  			>
	                    			<v-list-item-content>
	                      				<v-list-item-title>
	                      					<strong v-if="customer.status == 1">
	                      						{{ customer.user ? customer.user.full_name : "No Name" }}
	                      					</strong>
	                      					<span v-else>
	                      						{{ customer.user ? customer.user.full_name : "No Name" }}
	                      					</span>
	                      				</v-list-item-title>
	                      				<v-list-item-subtitle :title="customer.message">
	                      					<strong v-if="customer.status == 1">
	                      						{{ customer.message }}
	                      					</strong>
	                      					<span v-else>
	                      						{{ customer.message }}
	                      					</span>
	                      				</v-list-item-subtitle>
	                    			</v-list-item-content>
	                    			<v-list-item-icon>
	                    				<v-badge
				                            v-if="customer.status == 1"
				                            color="blue"
				                            dot
				                            offset-x="7"
				                            offset-y="7"
				                        >
				                            <v-icon>mdi-message</v-icon>
				                        </v-badge>
	                      				<v-icon v-else :color="selected && selected.id == customer.id ? 'primary' : 'default'">
	                        				mdi-message
	                      				</v-icon>
	                    			</v-list-item-icon>
	                  			</v-list-item>
	                  			<v-divider
	                    			:key="`chatDivider${index}`"
	                    			class="my-0"
	                  			/>
	                		</template>
              			</v-list-item-group>
            		</v-list>
            		<v-pagination
						circle
						v-model="currentPage"
						:length="lastPage"
						:total-visible="5"
						:disabled="paginateDisabled"
					></v-pagination>
          		</v-responsive>
        	</v-col>
	        <!-- ACTIVE CHAT -->
	        <v-col
	          	cols="auto"
	          	class="flex-grow-1 flex-shrink-0"
	        >
	          	<v-responsive
	            	v-if="activeChat"
	            	class="overflow-y-hidden fill-height"
	            	height="500"
	          	>
	            	<v-card
	              		flat
	              		class="d-flex flex-column fill-height"
	           		>
	              		<v-card-title>
	                		{{ selected.user ? selected.user.full_name : "No name" }}
	              		</v-card-title>
	              		<v-card-text class="flex-grow-1 overflow-y-auto" ref="chatDiv" id="chatDiv">
	                		<template v-for="(msg, i) in messages">
	                  			<div
	                    			:class="{ 'd-flex flex-row-reverse': msg.model == 'Vendor' }"
	              				>
	                    			<v-menu offset-y>
	                      				<template v-slot:activator="{ on }">
	                        				<!-- <v-hover
		                          				v-slot:default="{ hover }"
		                        			> -->
		                          				<v-chip
		                            				:color="msg.model == 'Vendor' ? 'primary' : ''"
		                            				style="height:auto; white-space: normal;"
		                            				class="pa-4 mb-2"
		                            				v-on="on"
		                          				>
		                            				{{ msg.message }}
		                            				<sub
		                              					class="ml-2"
		                              					style="font-size: 0.5rem;"
		                            				>
		                            					{{ msg.created_at | moment("from", "now") }}
		                            				</sub>
						                            <!-- <v-icon
						                              	v-if="hover"
						                              	small
						                            >
		                              					mdi-chevron-down
		                            				</v-icon> -->
		                          				</v-chip>
	                        				<!-- </v-hover> -->
	                      				</template>
		                      			<!-- <v-list>
				                        	<v-list-item>
				                          		<v-list-item-title>delete</v-list-item-title>
				                    		</v-list-item>
				                      	</v-list> -->
			                    	</v-menu>
		                  		</div>
		                	</template>
	              		</v-card-text>
	              		<v-card-actions>
	              			<v-textarea
			                  	v-model="form.message"
			                  	:rules="messageRules"
			                  	label="Send a message..."
			                  	no-details
			                  	height="100"
			                  	outlined
			                  	:disabled="disabled"
			                  	append-outer-icon="mdi-send"
			                  	@click:append-outer="send"
			                  	hide-details
			                />
	              		</v-card-actions>
	            	</v-card>
	          	</v-responsive>
	        </v-col>
      	</v-row>
    </v-container>
</template>

<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			form: {},
			disabled: false,
			paginateDisabled: false,
			lastPage: 1,
			currentPage: 1,
			activeChat: null,
			customers: [],
			selected: null,
		    avatar: null,
		    open: [],
		    customerOptions: {
		    	page: 1,
                itemsPerPage: 10,
                sortBy: ['id'],
                sortDesc: [true]
		    },
		    options: {
		    	page: 1,
                itemsPerPage: 10,
                sortBy: ['id'],
                sortDesc: [true]
		    },
		    messages: [],
	      	messageRules: [
	        	v => (v ? v.length <= 300 : !v) || 'Comment must be less than 300 characters',
		    ],
		    hasUnreadMessage: [],
		    oldScrollHeight: 0,
		    oldMessages: null,
	    }),
	    async created() {
	    	let vm = this;

	    	let params = vm.$serialize(vm.customerOptions);
            await vm.$store.dispatch('chats/fetchCustomers', params);
            vm.customers = vm.$store.state['chats'].customers.data;
            vm.lastPage = vm.$store.state['chats'].customers.last_page;
	    },
	    computed: {
			...mapState({
				items: state => state.chats.items,
				item: state => state.chats.item
			})
		},
		updated() {
		    let chat = this.$refs.chatDiv
		    if (chat) chat.addEventListener('scroll', this.onScroll);
		},
		methods: {
			...mapActions({
                'fetch': 'chats/fetch',
				'find': 'chats/find',
				'save': 'chats/save',
				'update': 'chats/update'
			}),
			async send() {
				let vm = this;

				try {
					vm.disabled = true;

					vm.form.channel = `chat-from-vendor-${ vm.selected.user_id }`;
					vm.form.userId = vm.selected.user_id;
					await vm.save(vm.form);

					vm.form = {};
					vm.messages.push(vm.item);

					// scroll to bottom
					setTimeout(() => {
						var container = this.$el.querySelector("#chatDiv");
						if (container) container.scrollTop = container.scrollHeight;
					}, 0);

					vm.disabled = false;
				} catch (err) {
					vm.disabled = false;
				}
			},
			async pushData(data) {
				let vm = this;

				// check if the new message is in the list of customers
				let customer = vm.customers.find(customer => customer.user_id === data.data.user_id);
				let customerIndex = _.findIndex(vm.customers, function(o) {
					return o.user_id == data.data.user_id;
				});

				if (customerIndex < 0) {
					// push at first index
					vm.customers.unshift(data.data);
				} else {
					// move new message at the top of the customers list
					vm.moveArray(vm.customers, customerIndex, 0);
					vm.customers[0].message = data.data.message;

					// if the incoming message is from active message, update status to 2 already
					if (vm.selected && (vm.selected.user_id == vm.customers[0].user_id)) {
						await vm.update({ id: data.data.id, status: 2 });
						vm.customers[0].status = 2;
					} else {
						vm.customers[0].status = 1;
					}
				}

				vm.messages.push(data.data);

				// scroll to bottom
				setTimeout(() => {
					var container = this.$el.querySelector("#chatDiv");
					if (container) container.scrollTop = container.scrollHeight;
				}, 0);
			},
			from(message) {
				if (message.model == "User") {
					return message.user ? message.user.full_name : "";
				}

				if (message.model == "Vendor") {
					return message.vendor ? message.vendor.full_name : "";
				}
			},
			moveArray(arr, fromIndex, toIndex) {
			    var element = arr[fromIndex];
			    arr.splice(fromIndex, 1);
			    arr.splice(toIndex, 0, element);
			},
			async onScroll() {
				let vm = this;

				if (vm.oldMessages === null || vm.oldMessages.length) {
				   	var container = vm.$el.querySelector("#chatDiv");
				   	if (container) {
				      	var top = container.scrollTop;
				      	if (top == 0) {
				      		await vm.loadMore();

				      		let scrollHeight = container.scrollHeight;
				      		let offsetHeight = container.offsetHeight;

				      		// preventing to auto scroll to top.
			      			container.scrollTop = (scrollHeight - offsetHeight) - vm.oldScrollHeight;
			      			vm.oldScrollHeight = scrollHeight;
				      	}                               
				   	}
				}
			},
			async loadMore() {
				let vm = this;

				vm.options.page = vm.options.page + 1;
				let params = vm.$serialize({
        			...vm.options,
        			itemsPerPage: 20,
        			sortBy: ['id'],
        			sortDesc: [true],
        			filters: {
        				vendor_id: vm.selected.vendor_id,
        				user_id: vm.selected.user_id
        			}
        		});
	            await vm.$store.dispatch('chats/fetch', params);
	            vm.oldMessages = vm.$store.state['chats'].items.data.reverse();
	            vm.messages = vm.oldMessages.concat(vm.messages);
			}
		},
		watch: {
    		async activeChat(val) {
    			let vm = this;

    			if (!val) {
    				vm.selected = [];
    				return;
    			}

        		let index = _.findIndex(vm.customers, function(o) {
        			return o.id === val;
        		});
        		vm.selected = vm.customers[index];

        		vm.options.page = 1;
        		let params = vm.$serialize({
        			...vm.options,
        			itemsPerPage: 20,
        			sortBy: ['id'],
        			sortDesc: [true],
        			filters: {
        				vendor_id: vm.selected.vendor_id,
        				user_id: vm.selected.user_id
        			}
        		});
	            await vm.$store.dispatch('chats/fetch', params);
	            vm.messages = vm.$store.state['chats'].items.data.reverse();

	            vm.customers[index].status = 2;

	            // scroll to bottom
	            setTimeout(() => {
	            	var container = this.$el.querySelector("#chatDiv");
					if (container) container.scrollTop = container.scrollHeight;
	            }, 0)
    		},
    		async currentPage(newVal, oldVal) {
				if (oldVal) {
					let vm = this;

					try {
						vm.paginateDisabled = true;

						vm.customerOptions.page = newVal;
						let params = vm.$serialize(vm.customerOptions);
			            await vm.$store.dispatch('chats/fetchCustomers', params);
			            vm.customers = vm.$store.state['chats'].customers.data;

			            vm.paginateDisabled = false;
			        } catch (err) {
			        	vm.paginateDisabled = false;
			        }
				}
			}
    	},
    	beforeDestroy() {
    		let chat = this.$refs.chatDiv
		    if (chat) chat.removeEventListener('scroll', this.onScroll);
		}
	}
</script>