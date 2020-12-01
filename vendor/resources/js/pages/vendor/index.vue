<template>
	<div>
		<v-container>
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
		          				<v-btn text color="error">Logout All</v-btn>
		          			</th>
		        		</tr>
		      		</thead>
		      		<tbody>
		        		<tr
		          			v-for="token in tokens"
		          			:key="token.name"
	        			>
		          			<td class="text-left">{{ token.name }}</td>
		          			<td class="text-left">{{ (token.last_used_at ? token.last_used_at : token.created_at) | moment('MMM. DD, YYYY \\at hh:mm A') }} <span class="font-italic">({{ (token.last_used_at ? token.last_used_at : token.created_at) | moment('from', 'now') }})</span></td>
		          			<td class="text-right">
		          				<v-btn text color="error">Logout</v-btn>
		          			</td>
		        		</tr>
		      		</tbody>
		    	</template>
		  	</v-simple-table>
		</v-container>
	</div>
</template>

<script type="text/javascript">
	import { mapGetters, mapState, mapActions } from 'vuex'

	export default {
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
            }),
        }
	}
</script>