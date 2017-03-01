<template src="./Header.tpl"></template>

<script>
	import Router from 'vue-router';
	import util from '../libs/js/util.js';
	import Modal from '../components/Modal.vue';
	import service from '../service/service.js';
	import store from '../store/store.js';

	export default {

		replace: false,

		name: 'Header',

		data(){
			return{
				userInfo:{},
				link:store.pageData.links,
				editPwdData:{
					originPwd : '',
					pwd : '',
					againPwd:''
				},
				editPwdModalMsg:{
					title:'修改密码',
					show : false,										
				},
			}
		},

		ready(){
			this.getAdminInfo();
		},

		methods:{
			logout:function(){
				localStorage.clear();
				window.location.href = '/adminlogin/login';
			},

			getAdminInfo:function(){
				let self = this;
				try{
					self.userInfo = JSON.parse(localStorage.getItem('userInfo'));
				}catch(e){
					console.log(e);
				}
			},

			editPwd:function(){
				var self = this;
				if(!self.editPwdData.originPwd){
					util.dialog.alert({msg:['请先输入原密码！']});
					return false;
				}
				if(!self.editPwdData.pwd){
					util.dialog.alert({msg:['请先输入密码！']});
					return false;
				}
				if(!self.editPwdData.againPwd ){
					util.dialog.alert({msg:['请先重复输入密码！']});
					return false;
				}
				if(self.editPwdData.pwd != self.editPwdData.againPwd){
					util.dialog.alert({msg:['两次输入的密码不一致，请重新确定！']});
					return false;
				}
				service.resetPwd(self.userInfo.admin_id,self.editPwdData).done(function(res){
					util.dialog.alert({
						msg:[res.msg],
						title:'信息提示'
					});
					self.editPwdModalMsg.show = false;
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg],
						title:'错误提示'
					});
				});
			},

			showEditPwdModal:function(){
				this.editPwdModalMsg.show = true;
				this.editPwdData = {
					originPwd : '',
					pwd : '',
					againPwd:''
				};
			}
		},

		components: {
			Modal:Modal,
			// AppBread : Bread,
		},

	};
</script>