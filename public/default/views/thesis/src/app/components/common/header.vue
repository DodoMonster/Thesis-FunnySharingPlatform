<template src="./header.tpl"></template>

<script>
	import store from '../../../store/index.js';
	import service from '../../../service/service.js';	
	import $ from 'jquery';
	import Router from 'vue-router';
	import util from '../../../libs/js/util.js';
	export default {

		replace: true,

		name: 'Header',

        data(){
			return{
				store:store,
				thingsType:'hot'
			}
		},

		ready(){
			let self = this;
			if(self.$route.name !== 'userHome' && self.$route.params.thingsType){
				self.thingsType = self.$route.params.thingsType;
			}else{
				self.thingsType = '';
			}
			$('#nav li').click(function(){
				$(this).addClass('highlight').siblings('li').removeClass('highlight');
			});
			if(store.userInfo.user_id){
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
					self.isLogin = false;
					store.isLogin = false;
					store.clearUserInfo();	
					// window.history.pushState({},0,util.changeURLArg('is_tramp',0));
					// window.history.pushState({},0,util.changeURLArg('is_favorite',0));
					// window.history.pushState({},0,util.changeURLArg('is_praise',0));
					// history.go(0);
					let router = new Router;
					router.go('/index/hot');
					history.go(0);
				}).fail(function(res){
					alert(res.msg);
				});
			}
		},

		watch:{
			'store.isLogin':function(newVal,oldVal){
				this.isLogin = newVal;
			},
			'$route.name':function(newVal,oldVal){
				if(newVal !== 'index' && newVal !== 'add'){
					$('.menu-bar li').removeClass('highlight');
				}
			}
		}
	}
</script>
