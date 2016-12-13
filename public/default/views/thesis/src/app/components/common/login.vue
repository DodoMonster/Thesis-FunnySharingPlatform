<template src="./Login.tpl"></template>

<style></style>

<script>
	import store from '../../../store/index.js';
	export default {

		replace: false,

		name: 'Login',

		data(){
			return{
				isLogin:true,
				isShowResetPwd:false
			}
		},

		methods:{
			cancelBubble:function(e){
				if(e.stopPropagation){
					e.stopPropagation();
				}else{
					e.cancelBubble = true;
				}
				
			},

			showRegister:function(){
				this.isLogin = false;
				this.isShowResetPwd = false;
			},

			showLogin:function(){
				this.isLogin = true;
				this.isShowResetPwd = false;
			},

			showResetPwd:function(){
				this.isShowResetPwd = true;
				this.isLogin = false;
			}
		},

	};

</script>
