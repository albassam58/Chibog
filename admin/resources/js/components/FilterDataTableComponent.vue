<template>
	<div>
			<v-row
	        	align="center"
	        	justify="center"
	      	>
	        	<v-btn
	          		color="pink"
	          		dark
	          		@click.stop="drawer = !drawer"
	        	>
	          		Toggle
	        	</v-btn>
	      	</v-row>

	      	<v-navigation-drawer
		      	v-model="drawer"
		      	app
		      	clipped
		      	right
		      	width="400"
		    >
		      	
		    </v-navigation-drawer>
	</div>
</template>

<script type="text/javascript">
	export default {
		data: () => ({
			drawer: false
		}),
		computed: {
			keepOpen(){ return this.onClickOutside; },
		},
		methods: {
			onClickOutside() { return true; },
		}
	}
</script>