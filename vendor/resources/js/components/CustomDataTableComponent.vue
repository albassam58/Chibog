<template>
    <div>
        <v-card>

            <v-card-title>
                <v-text-field
                    v-if="searchHint"
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    :hint="searchHint"
                    persistent-hint
                ></v-text-field>
                <v-text-field
                    v-else
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>

            <v-card-text>
                <!-- <filter-data-table /> -->
                <slot name="filter"></slot>
            </v-card-text>

    		<v-data-table
                multi-sort
                :page="currentPage"
    		    :headers="headers"
    		    :items="items"
    		    :options.sync="options"
    		    :server-items-length="totalItems"
    		    :loading="loading"
    		    :sort-by="orderBy"
          		:sort-desc="orderDirection"
    		    class="elevation-1"
    		    :footer-props="{
    			    'items-per-page-options': [10, 15, 50, 100]
    			}"
            >
                <template v-slot:item="row">
                    <tr>
                        <slot name="item" v-bind:item="row.item"></slot>
                    </tr>
                </template>
            </v-data-table>

        </v-card>
	</div>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
    import FilterDataTable from "@components/FilterDataTableComponent"
    import _ from 'lodash'

	export default {
        components: {
            FilterDataTable
        },
        props: ['searchHint', 'headers', 'initFilter', 'module', 'sortBy', 'sortDesc'],
		data: () => ({
            search: '',
            filters: {},
            currentPage: 1,
            items: [],
            totalItems: 0,
            options: {},
            orderBy: [],
            orderDirection: [],
            loading: false
        }),
        watch: {
            search(newVal, oldVal) {
                this.searchList(newVal);
            },
            options: {
                handler() {
                    this.loadItems();
                },
            },
            deep: true,
        },
        computed: {
            modulePage() {
                return this.module.split("/")[0];
            }
        },
        async created() {
            let vm = this;

            vm.orderBy = vm.sortBy;
            vm.orderDirection = vm.sortDesc;
        },
        methods: {
			async loadItems() {
				let vm = this;

                try {
    	            vm.loading = true;

                    const { page, sortBy, sortDesc } = vm.options;

                    vm.currentPage = page;
                    vm.orderBy = sortBy;
                    vm.orderDirection = sortDesc;

    	            let params = vm.$serialize({
                        ...vm.options,
                        search: vm.search,
                        filters: {
                            ...vm.filters,
                            ...vm.initFilter
                        }
                    });
    	            await vm.$store.dispatch(vm.module, params);

                    vm.items = vm.$store.state[vm.modulePage].items.data;
    	            vm.totalItems = vm.$store.state[vm.modulePage].items.total;

                    vm.loading = false;

                    vm.$emit('done');
                } catch (err) {
                    vm.loading = false;

                    vm.$emit('done');
                }
			},
            searchList: _.debounce(async function(query) {
                let vm = this;

                try {
                    vm.loading = true;

                    const { sortBy, sortDesc } = vm.options;

                    // set current page to 1 if searching
                    vm.currentPage = 1;
                    vm.orderBy = sortBy;
                    vm.orderDirection = sortDesc;

                    let params = vm.$serialize({
                        ...vm.options,
                        page: 1, // set current page to 1 if searching
                        search: query,
                        filters: {
                            ...vm.initFilter,
                            ...vm.filters
                        }
                    });
                    await vm.$store.dispatch(vm.module, params);

                    vm.items = vm.$store.state[vm.modulePage].items.data;
                    vm.totalItems = vm.$store.state[vm.modulePage].items.total;

                    vm.loading = false;
                } catch (err) {
                    vm.loading = false;
                }
            }, 500),
            async filterList(val) {
                let vm = this;

                try {
                    vm.loading = true;

                    const { sortBy, sortDesc } = vm.options;

                    // set current page to 1 if searching
                    vm.currentPage = 1;
                    vm.orderBy = sortBy;
                    vm.orderDirection = sortDesc;
                    vm.filters = {
                        ...vm.initFilter,
                        ...val
                    }

                    let params = vm.$serialize({
                        ...vm.options,
                        page: 1, // set current page to 1 if searching
                        search: vm.search,
                        filters: {
                            ...val,
                            ...vm.initFilter
                        }
                    });
                    await vm.$store.dispatch(vm.module, params);

                    vm.items = vm.$store.state[vm.modulePage].items.data;
                    vm.totalItems = vm.$store.state[vm.modulePage].items.total;

                    vm.loading = false;
                } catch (err) {
                    vm.loading = false;
                }
            }
        }
	}
</script>