<template>
	<div>
		<v-data-table
			v-model="selected"
      		:headers="headers"
      		:items="items"
      		:search="search"
      		:loading="loading"
      		:show-select="checkbox"
    		loading-text="Loading... Please wait"
      		hide-default-footer
    	>
    		<template
    			v-if="checkbox"
		        v-slot:item.data-table-select="{ isSelected, select }"
		    >
		        <v-simple-checkbox
		          color="green"
		          :value="isSelected"
		          @input="select($event)"
		        ></v-simple-checkbox>
		    </template>
    	</v-data-table>
    	<paginate :action="action" :collection="collection" />
    </div>
</template>

<script type="text/javascript">
	import Paginate from '@components/PaginateComponent'

	export default {
		props: ['checkbox', 'loading', 'headers', 'items', 'search', 'action', 'collection'],
		components: {
			Paginate
		},
		watch: {
			search(newVal, oldVal) {
	            this.searchList(newVal);
	        },
	        selected(val) {
	        	this.$emit('update-selected-row', val);
	        }
		},
		data() {
			return {
				store: this.action.split('/')[0],
				selected: []
			}
		},
		methods: {
			searchList(query) {
				let vm = this;
				vm.$store.commit(vm.store + '/setParams', { page: 1, search: query });
				vm.$store.dispatch(vm.action);
				vm.selected = [];
			},
		}
	}
</script>