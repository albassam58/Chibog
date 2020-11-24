<template>
	<div>
		<v-card>
			<v-card-title>
				Ratings & Reviews
				<v-spacer />
				<strong class="text-h4">{{ store ? store.rate || 0 : 0 }}&nbsp;</strong>
				<span class="grey--text">/ 5</span>
			</v-card-title>
			<v-card-text>
				<!-- RATE -->
				<v-row v-if="canRate">
					<v-col cols="12" class="text-center mt-4 py-0">
						<div class="text-h6">
					      Rate Our Store
					    </div>
					    <div class="text-subtitle-1">
					      If you enjoy our services and foods, please take a few seconds to rate your experience with the store. It really helps!
					    </div>
					</v-col>
					<v-col cols="12" class="text-center py-0">
					    <div class="d-inline-block">
							<v-rating
						      v-model="rating.star"
						      background-color="orange lighten-3"
						      color="orange"
						      required
						    ></v-rating>
						</div>
						<div class="grey--text text--lighten-1 ml-2 d-inline-block">
					        ({{ rating.star }})
					    </div>
					</v-col>
				</v-row>
				<v-row v-if="canRate">
					<v-col cols="12">
						<v-textarea
							v-model="rating.comment"
					      	counter="300"
					      	label="Leave a comment..."
					      	solo
					      	flat
					      	:rules="commentRule"
					      	@keydown.enter="rate"
					    >
				          	<template v-slot:append>
				            	<v-btn
				              		class="mx-0"
				              		depressed
				              		@click="rate"
				           	 	>
				              		Post
				            	</v-btn>
				          	</template>
				        </v-textarea>
				    </v-col>
			    </v-row>
			    <!-- END RATE -->

			    <v-divider class="mb-4"></v-divider>

			    <v-row v-for="(item, index) in reviews" :key="`review${ item.id }`" justify="space-between" class="px-4">
			        <v-col cols="8">
			            <div class="text-subtitle-2 d-inline-block">
		                  	{{ item.user ? item.user.full_name : item.full_name }}
		                </div>
		                <div class="d-inline-block">
			                <v-rating
				          		:value="item.rate"
				          		background-color="orange lighten-3"
				          		color="orange"
				          		dense
				          		half-increments
				          		readonly
				          		size="14"
				        	></v-rating>
				        </div>
				        <div class="grey--text text--lighten-1 d-inline-block">
					        ({{ item.rate }})
					    </div>
			        </v-col>
			        <v-col
			            class="text-right"
			            cols="4"
			        >
			          	<div>
			          		<span class="text-overline">
			          			{{ item.created_at | moment('D MMM YYYY') }}
			          			<!-- 10 Jan 1994 -->
			          		</span>
			          		@
			          		<span class="text-caption">
			          			{{ item.created_at | moment('hh:mm A') }}
			          		</span>
			          	</div>
			        </v-col>
			        <v-col cols="12">
			        	<div>{{ item.comment }}</div>
			        </v-col>
			        <v-col cols="12" v-if="">
			        	<v-divider class="grey lighten-3"></v-divider>
			        </v-col>
			    </v-row>
			    
			    <v-row v-if="reviews && !reviews.length">
			    	<v-col cols="12">
			    		<div>No ratings yet.</div>
			    	</v-col>
			    </v-row>
			    
			</v-card-text>
		</v-card>
	</div>
</template>

<script type="text/javascript">
	export default {
		props: {
			canRate: {
				type: Boolean,
				default: true
			},
			reviews: Array,
			store: Object
		},
		data: () => ({
	      events: [],
	      rating: {
	      	star: 0
	      },
	      commentRule: [
        	v => (v ? v.length <= 300 : !v) || 'Comment must be less than 300 characters',
	      ]
	    }),
	    computed: {
	      timeline () {
	        return this.events.slice().reverse()
	      },
	    },
	    methods: {
	      rate () {
	        const time = (new Date()).toTimeString()
	        this.events.push({
	          id: this.nonce++,
	          text: this.input,
	          time: time.replace(/:\d{2}\sGMT-\d{4}\s\((.*)\)/, (match, contents, offset) => {
	            return ` ${contents.split(' ').map(v => v.charAt(0)).join('')}`
	          }),
	        })

	        this.input = null
	      },
	    },
	}
</script>