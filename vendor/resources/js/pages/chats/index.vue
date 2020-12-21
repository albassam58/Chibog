<template>
	<div>
		<v-container>
			<v-row>
				<v-col cols="6" class="d-flex flex-row">
					<div class="text-h4 mb-4 primary--text">Messages</div>
				</v-col>
				<v-col cols="6" class="d-flex flex-row-reverse">
					<v-btn color="default" @click="$router.back(-1)" text>
						<v-icon>mdi-arrow-left</v-icon>
						Back
					</v-btn>
				</v-col>
			</v-row>
			<chat ref="chat"></chat>
		</v-container>
	</div>
</template>

<script type="text/javascript">
	import Chat from '@components/ChatComponent'

	export default {
		components: {
			Chat
		}
	}
</script>