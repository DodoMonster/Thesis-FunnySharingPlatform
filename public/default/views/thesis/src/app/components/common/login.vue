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
			// this.userInfo = this.store.getUserInfo();
			if(this.store.userInfo.user_id){
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
					store.setUserInfo(res.data.data);	
					// store.userInfo = store.getUserInfo();
					// self.store.isLogin = true;	
					// self.isLoginBox = true;			
					// store.showLoginForm = false;			
					history.go(0);
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
					self.isLoginBox = true;			
					self.isShowResetPwd = false;
				}).fail(function(res){
					alert(res.msg);
				});
			},
			register:function(){
				let self = this;
				if(!self.registerData.username){
					alert('请先输入用户名！');
					return false;
				}else if(self.registerData.username.length<4){
					alert('用户名不能少于4个字符！');
					return false;
				}else if(!self.registerData.password){
					alert('请先输入密码！');
					return false;
				}else if(self.registerData.password.length<6){
					alert('密码不能少于6个字符！');
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
					self.isLoginBox = true;			
					self.isShowResetPwd = false;					
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
