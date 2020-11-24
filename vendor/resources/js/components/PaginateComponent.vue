<template>
	<div>
		<v-pagination
			v-model="currentPage"
			:length="lastPage"
			:total-visible="8"
		></v-pagination>
	</div>
</template>

<script type="text/javascript">
	export default {
		props: ['action', 'collection'],
		watch: {
			currentPage(newVal, oldVal) {
				if (oldVal) this.paginatePage(newVal);
			}
		},
		data() {
			return {
				store: this.action.split('/')[0],
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
			paginatePage(page) {
				if (!this.action) {
					this.action = 'fetch';
				}
				this.$store.commit(this.store + '/setParams', { ...{ page: page }});
				this.$store.dispatch(this.action);
			}
		}
	}
</script>