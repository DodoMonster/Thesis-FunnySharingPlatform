<template src="./header.tpl"></template>

<script>
	import store from '../../../store/index.js';
	import service from '../../../service/service.js';	
	import $ from 'jquery';
	export default {

		replace: true,

		name: 'Header',

        data(){
			return{
				userInfo:store.userInfo,
				store:store,
				thingsType:'hot',
			}
		},

		ready(){
			let self = this;
			if(self.$route.name !== 'userHome'){
				self.thingsType = self.$route.params.thingsType;
			}else{
				self.thingsType = '';
			}
			self.userInfo = store.getUserInfo();
			$('#nav li').click(function(){
				$(self).addClass('highlight').siblings('li').removeClass('highlight');
			});
			if(self.userInfo.user_id){
				store.isLogin = true;
			}else{
				store.isLogin = false;
			}
		},
		

		methods:{
			showLoginBox:function(){
				store.showLoginForm = true;
			},

			logout:function(){
				let self = this;
				service.logout().done(function(res){
					alert(res.msg);	
					self.isLogin = false;
					store.isLogin = false;
					store.clearUserInfo();				
				}).fail(function(res){
					alert(res.msg);
				});
			}
		},

		watch:{
			'store.isLogin':function(newVal,oldVal){
				this.isLogin = newVal;
			}
		}
	}
</script>
