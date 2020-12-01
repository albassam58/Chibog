<template>
    <div>
        <v-menu
            v-model="menu"
            :close-on-content-click="false"
            right
            offset-y
            transition="scale-transition"
        >
            <template v-slot:activator="{ on, attrs }">
                <v-badge
                    :color="badgeCount > 0 ? 'red' : ''"
                    :content="badgeCount"
                    overlap
                    offset-x="24"
                    offset-y="25"
                    class="mr-2"
                >
                    <v-btn
                        icon
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-email</v-icon>
                    </v-btn>
                </v-badge>
            </template>

            <v-card width="400" v-if="notifications.length">
                <v-list v-for="(notification, index) in notifications" :key="notification.id">
                    <v-list-item @click="read(notification.id)">
                        <v-list-item-content :class="notification.status == 1 ? 'font-weight-bold' : ''">
                            <v-list-item-title>
                                {{ fullname(notification) }}
                                <span style="float: right;" class="text-caption">
                                  {{ notification.created_at | moment("from", "now") }}
                                </span>
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                <strong>{{ getStore(notification) }}</strong> {{ notification.message }}
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>

                <v-card-actions class="justify-center">
                    <v-btn
                        text
                        color="primary"
                        @click="seeAll"
                    >
                        See All
                    </v-btn>
                </v-card-actions>
            </v-card>
            <v-card width="400" v-else>
                <v-card-text class="text-center">No Notification</v-card-text>
            </v-card>
        </v-menu>
    </div>
</template>

<script>
    import { mapGetters, mapState, mapActions } from 'vuex'

    export default {
        data: () => ({
            menu: false,
            badgeCount: 0,
            notifications: [],
        }),
        async created() {
            let vm = this;

            await vm.fetchVendorPopup();
            await vm.countUnread();

            vm.badgeCount = vm.totalUnread;
            vm.notifications = vm.popupNotifications;
        },
        computed: {
            ...mapState('orderNotifications', {
                popupNotifications: state => state.popupNotifications,
                totalUnread: state => state.totalUnread
            })
        },
        methods: {
            ...mapActions({
                'fetchVendorPopup': 'orderNotifications/fetchVendorPopup',
                'countUnread': 'orderNotifications/countUnread'
            }),
            getStore(notification) {
                let notif = JSON.parse(notification.payload)[0]
                return notif.store ? notif.store.name + " • " : "";
            },
            fullname(notification) {
                let notif = JSON.parse(notification.payload)[0]
                return notif.customer_first_name + " " + notif.customer_last_name;
            },
            read(id) {
                let vm = this;

                let index = _.findIndex(vm.notifications, function(object) {
                    return object.id == id
                });

                if (index >= 0) {
                    // mark as read then decrement badgeCount
                    if (vm.notifications[index].status == 1) {
                        vm.notifications[index].status = 2;
                        vm.badgeCount--;
                    }

                    vm.$router.push(`/notifications?id=${ vm.notifications[index].id }`);
                }
                
                vm.menu = false;
            },
            seeAll(index) {
                let vm = this;
                vm.$router.push(`/notifications`);
                vm.menu = false;
            },
            updateOrderNotification(data) {
                let vm = this;
                vm.badgeCount++;

                if (vm.notifications.length >= 5) {
                    vm.notifications.pop();
                }

                vm.notifications.unshift(data);
            }
        },
        watch: {
            totalUnread(newVal) {
                let vm = this;
                vm.badgeCount = newVal;
            },
            popupNotifications(newVal) {
                let vm = this;
                vm.notifications = newVal;
            }
        }
    }
</script>