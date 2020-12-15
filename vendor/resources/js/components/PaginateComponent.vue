<template>
	<div>
		<v-pagination
			circle
			v-model="currentPage"
			:length="lastPage"
			:disabled="disabled"
		></v-pagination>
	</div>
</template>

<script type="text/javascript">
	export default {
		props: ['action', 'collection', 'visible'],
		watch: {
			currentPage(newVal, oldVal) {
				if (oldVal) {
					let vm = this;
					vm.paginatePage(newVal);
					window.scroll({
					    top: 0,
					    left: 0,
					    behavior: 'smooth'
					})
				}
			}
		},
		data() {
			return {
				store: this.action.split('/')[0],
				disabled: false
			}
		},
		computed: {
			currentPage: {
				get() {
					return this.$store.state[this.store][this.collection].current_page
				},
				set(value) {
					this.$store.commit(this.store + '/setCurrentPage', value);
				}
			},
			lastPage: {
				get() {
					return this.$store.state[this.store][this.collection].last_page
				}
			}
		},
		methods: {
			async paginatePage(page) {
				let vm = this;

				vm.disabled = true;

				if (!vm.action) {
					vm.action = 'fetch';
				}
				await vm.$store.commit(vm.store + '/setParams', { ...{ page: page }});
				await vm.$store.dispatch(vm.action);

				vm.disabled = false;
			}
		}
	}
</script>