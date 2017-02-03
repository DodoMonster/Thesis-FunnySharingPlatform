<template src="./userList.tpl"></template>

<style>
.long-search label,
.long-search input,
.long-search select,
.long-search option{
	font-size: 90%;
}
.state-form{
	display: none;
}
</style>

<script>
	import Vue from 'vue';
	import Pagination from '../components/Pagination.vue';
	import Modal from '../components/Modal.vue';
	import util from '../libs/js/util.js';
	import service from '../service/service.js';

	export default {

		replace: false,

		name: 'UserList',

		route:{
            canReuse: false
        },

		data(){
			return{
				modalMsg:{
					title:'重置普通用户密码',	
					show:false									
				},
				addDialog:{
					title:'添加用户',	
					show:false	
				},
				page:{
					cur:1,//当前页
					totalNum:0,//总共多少条数据
					totalPage:0//总共多少页
				},		
				searchParam:{
					uname:'',
				},
				userList:[],
				resetPwdData:{
					uid:'',
					pwd:'',
					againtPwd:''
				},
				addData:{
					user_name:'',
					user_password:'',
					user_againtPwd:''
				},
			}
		},

		ready(){
			var self = this;
			self.getUserList();
		},


		methods:{
			showResetBox:function(id){
				this.modalMsg.show = true;
				this.resetPwdData.uid = id;
			},
			showAddDialog:function(){
				this.addDialog.show = true;
				this.addData = {
					user_name:'',
					user_password:'',
					user_againtPwd:''
				};
			},
			//删除用户
			deleteUser:function(id){
				var self = this;
				util.dialog.show({
					msg:['确定删除该用户？'],
					title:'警告提示'
				}).confirm(function(){
					this.emit('hide-dialog');
						service.deleteUser(id).done(function(res){
						util.dialog.alert({
							msg:[res.msg],
							title:'信息提示'
						});
						self.getUserList(true);
					}).fail(function(res){
						util.dialog.alert({
							msg:[res.msg]
						});
					});
				});	
			},

			//添加用户
			addUser:function(){
				var self = this;
				if(!self.addData.user_name){
					util.dialog.alert({msg:['请先输入用户名']});
					return false;
				}
				if(!self.addData.user_password){
					util.dialog.alert({msg:['请先输入用户新密码']});
					return false;
				}
				if(!self.addData.user_againtPwd){
					util.dialog.alert({msg:['请先重复输入用户新密码']});
					return false;
				}
				if(self.addData.user_password != self.addData.user_againtPwd){
					util.dialog.alert({msg:['两次密码输入不相同，请重新输入！']});
					return false;
				}
				service.addUser(self.addData).done(function(res){
					util.dialog.alert({
						msg:[res.msg],
						title:'信息提示'
					});
					self.addDialog.show = false;
					self.getUserList(true);
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg]
					});
				});
			},

			//获取用户列表
			getUserList:function(flag){
				var self = this;
				self.userList = [];
				if(flag){
					self.page.cur = 1;
				}
				service.getUserList(self.page.cur,self.searchParam.uname).done(function(res){
					self.userList = res.data.list || [];
					self.page.totalPage = res.data.totalPage || 0;
					self.page.totalNum = res.data.totalNum || 0;
				}).fail(function(res){
					self.userList = [];
					self.page.totalPage = 0;
					self.page.totalNum = 0;
					if(res.msg){
						util.dialog.alert({
							msg:[res.msg],
							title:'错误提示'
						});
					}else{
						util.dialog.alert({
							msg:['请求失败，请重试！'],
							title:'错误提示'
						});
					}
				});
			},

			//重置密码成功
			resetPwd:function(){
				var self = this;
								
				if(!self.resetPwdData.pwd){
					util.dialog.alert({msg:['请先输入用户新密码']});
					return false;
				}
				if(!self.resetPwdData.againPwd){
					util.dialog.alert({msg:['请先重复输入用户新密码']});
					return false;
				}
				if(self.resetPwdData.pwd != self.resetPwdData.againPwd){
					util.dialog.alert({msg:['两次密码输入不相同，请重新输入！']});
					return false;
				}
				service.resetUserPwd(self.resetPwdData).done(function(res){
					util.dialog.alert({
						msg:[res.msg],
						title:'信息提示'
					});
					self.modalMsg.show = false;
				}).fail(function(res){
					if(res.msg){
						util.dialog.alert({
							msg:[res.msg],
							title:'错误提示'
						});
					}else{
						util.dialog.alert({
							msg:['请求失败，请重试！'],
							title:'错误提示'
						});
					}
				});
			},

			//强制解绑手机
			showCutPhoneBox:function(cell){
				util.dialog.show({
					modal:'text',
					msg:['确定解绑该手机号码吗？'],
					title:'警告信息',					
				}).confirm(function(){
					this.emit('hide-dialog');
					service.unbindCellphone(cell).done(function(res){
						util.dialog.alert({
							msg:[res.msg],
							title:'信息提示'
						});
						self.getUserList();
					}).fail(function(res){
						if(res.msg){
							util.dialog.alert({
								msg:[res.msg],
								title:'错误提示'
							});
						}else{
							util.dialog.alert({
								msg:['请求失败，请重试！'],
								title:'错误提示'
							});
						}
					});
				});
			},


			editState:function(id,e){
				var self = this;
				var $self = $(e.target);
				var state = $self.parents('form').find("#state_select option:selected").val();
				service.editState(id,state).done(function(res){
					util.dialog.alert({
						msg:[res.msg],
						title:'信息提示'
					});
					self.getUserList();
					$self.parents('form').hide().parent('td').find('span').show().next('a').find('i').show();
				}).fail(function(res){
					if(res.msg){
						util.dialog.alert({
							msg:[res.msg],
							title:'错误提示'
						});
					}else{
						util.dialog.alert({
							msg:['请求失败，请重试！'],
							title:'错误提示'
						});
					}
				});
			}

			
		},

		components:{
			Pagination:Pagination,
			Modal:Modal,
		},

		watch:{
			'page.cur':function(newVal,oldVal){
				this.getUserList();
			}
		}


	};
</script>