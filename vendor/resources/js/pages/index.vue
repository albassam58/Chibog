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
				<div class="text-h4 mb-4">Stores</div>
			</v-col>
			<v-col
				cols="6"
				class="d-flex flex-row-reverse"
			>
				<v-btn
					v-if="storesByVendor.length < 1"
					color="primary"
					@click="$router.push('/stores/add')"
				>
					<v-icon>mdi-plus</v-icon>
					Add Store
				</v-btn>
			</v-col>
		</v-row>

		<v-row>
			<v-col
				class="pa-3"
				cols="12"
				sm="6"
				md="4"
				v-for="item in storesByVendor"
				:key="item.id"
			>
				<store-card
					:store="item"
					:key="item.name"
					:hasView="true"
					:hasEdit="true"
					:hasUpload="true"
				></store-card>
			</v-col>
		</v-row>

	</v-container>
</template>

<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'
	import StoreCard from '@components/StoreCardComponent'

	export default {
		components: {
			StoreCard
		},
		data: () => ({
			loading: false,
		}),
		async created() {
			let vm = this;
			await vm.fetchByVendor();
		},
		computed: {
			...mapState('stores', {
				storesByVendor: state => state.storesByVendor
			})
		},
		methods: {
			...mapActions('stores', ['fetchByVendor'])
		}
	}
</script>