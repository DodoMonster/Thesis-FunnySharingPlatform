<template src="./Login.tpl"></template>

<script>
	import service from '../../../service/service.js';
	import store from '../../../store/index.js';
	export default {

		replace: false,

		name: 'Login',

		data(){
			return{
				store:store,
				isLoginBox:true,
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
				userInfo:store.userInfo,
			}
		},

		ready(){
			if(store.userInfo.user_id){
				this.store.isLogin = true;
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
				this.isLoginBox = false;
				this.isShowResetPwd = false;
			},

			showLogin:function(){
				this.isLoginBox = true;
				this.isShowResetPwd = false;
			},

			showResetPwd:function(){
				this.isShowResetPwd = true;
				this.isLoginBox = false;
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
					localStorage.setItem('userInfo', JSON.stringify(res.data.data));
					store.setUserInfo();	
					self.store.isLogin = true;				
					store.showLoginForm = false;					
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
					self.isShowResetPwd = false;
					store.showLoginForm = false;
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
					store.showLoginForm = false;					
				}).fail(function(res){
					alert(res.msg);
				});
			},
		},

		watch:{
			'store.isLogin':function(newVal,oldVal){
				store.isLogin = newVal;
			}
		}

	};

</script>
