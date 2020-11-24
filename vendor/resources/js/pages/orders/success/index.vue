<template>
    <div>
        <v-container>
            <v-row>
                <v-col
                    cols="12"
                    class="">
                    <div class="text-h4 mb-4">Order Successful</div>
			    </v-col>
    		</v-row>
		    <v-card
		        v-for="transaction in transactions"
		        :key="transaction.id">
		        <v-card-title class="justify-center">Transaction ID on {{ transaction.store }}</v-card-title>
		        <v-card-text
		            class="text-center">
		            <v-row>
		                <v-col
		                    cols="12">
		                    <div class="text-h3">{{ transaction.id }}</div>
		                </v-col>
		            </v-row>
		        </v-card-text>
		    </v-card>
    	</v-container>
    </div>
</template>
<script type="text/javascript">
	import { mapState, mapActions } from 'vuex'

	export default {
		data: () => ({
			transactions: null
		}),
	    async created() {
	    	let vm = this;

	    	// if no order, redirect to home page
    		if (!_.has(vm.$route.query, 'transactions')) {
    			vm.$router.push('/');
    			return;
    		}

    		vm.transactions = JSON.parse(vm.$route.query.transactions);
	    }
	}
</script>
