<template src="./Login.tpl"></template>

<style></style>

<script>
	import store from '../../../store/index.js';
	export default {

		replace: false,

		name: 'Login',

		data(){
			return{
				
			}
		},

		methods:{
			cancelBubble:function(e){
				if(e.stopPropagation){
					e.stopPropagation();
				}else{
					e.cancelBubble = true;
				}
				
			}
		},

	};

</script>
