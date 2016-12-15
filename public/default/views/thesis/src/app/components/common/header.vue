<template src="./header.tpl"></template>

<script>
	import store from '../../../store/index.js';
	export default {

		replace: false,

		name: 'Header',

		data(){
			return{
				
			}
		},

		methods:{
			showLoginBox:function(){
				store.showLoginForm = true;
			}
		},
	}
</script>
