<template src="./Login.tpl"></template>

<style></style>

<script>
	import service from '../../../service/service.js';
	export default {

		replace: false,

		name: 'Login',

		data(){
			return{
				isLogin:true,
				isShowResetPwd:false,
				loginData:{
					username:'',
					password:'',
				},
				registerData:{
					username:'',
					password:'',
					passwordAgain:''
				},
				resetData:{
					username:'',
					password:'',
					passwordAgain:''
				},
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
			},
			login:function(){
				let self = this;
				if(!self.loginData.username){
					alert('请先输入用户名！');
					return false;
				}else if(!self.loginData.password){
					alert('请先输入密码！');
					return false;
				}
				service.login(self.loginData).done(function(res){
					alert(res.msg);
				}).fail(function(res){
					alert(res.msg);
				});
			},
			reset:function(){
				let self = this;
				if(!self.resetData.username){
					alert('请先输入用户名！');
					return false;
				}else if(!self.resetData.password){
					alert('请先输入密码！');
					return false;
				}else if(!self.resetData.passwordAgain){
					alert('请先输入密码！');
					return false;
				}else if(self.resetData.password != self.resetData.passwordAgain){
					alert('两次密码不一致请重新确认！');
					return false;
				}
				service.reset(self.resetData).done(function(res){
					alert(res.msg);
				}).fail(function(res){
					alert(res.msg);
				});
			},
			register:function(){
				let self = this;
				if(!self.registerData.username){
					alert('请先输入用户名！');
					return false;
				}else if(!self.registerData.password){
					alert('请先输入密码！');
					return false;
				}else if(!self.registerData.passwordAgain){
					alert('请先输入密码！');
					return false;
				}else if(self.registerData.password != self.registerData.passwordAgain){
					alert('两次密码不一致请重新确认！');
					return false;
				}
				service.register(self.registerData).done(function(res){
					alert(res.msg);
				}).fail(function(res){
					alert(res.msg);
				});
			},
		},

	};

</script>
