<template src="./adminList.tpl"></template>

<script>
	import Vue from 'vue';
	import Modal from '../components/Modal.vue';
	import util from '../libs/js/util.js';
	import service from '../service/service.js';

	export default {

		replace: false,

		name: 'AdminList',

		route:{
            canReuse: false
        },

		data(){
			return{				
				showPwd:true,
				isEditAdmin:false,
				adminBoxMsg:{
					title:'编辑管理员资料',
					show:false
				},
				adminList:[],
				adminData:{
					admin_name:'',
					password:'',
					againPwd:'',
					cellphone:'',
				}
			}
		},

		ready(){
			this.getAdminList();
		},

		methods:{
			showResetPwd:function(){
				this.showPwd = false;
			},
			hideResetPwd:function(){
				this.showPwd = true;
			},

			showEditDialog:function(flag,admin){
				let self = this;
				self.adminBoxMsg.show = true;
				
				if(flag){//添加管理员
					self.isEditAdmin = false;
					self.adminBoxMsg.title = '添加管理员';
					self.adminData = {
						admin_name:'',
						password:'',
						againPwd:'',
						cellphone:''
					}
				}else{//编辑管理员
					this.showPwd = true;
					self.adminData = admin;
					self.isEditAdmin = true;
					self.adminBoxMsg.title = '编辑管理员资料';
				}
			},

			getAdminList:function(){
				var self = this;
				service.getAdminList().done(function(res){
					self.adminList = res.data;
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg]
					});
				});
			},
			deleteAdmin:function(id){
				var self = this;
				util.dialog.show({
					msg:['确定删除该管理员？'],
					title:'警告提示'
				}).confirm(function(){
					this.emit('hide-dialog');
						service.deleteAdmin(id).done(function(res){
						util.dialog.alert({
							msg:[res.msg],
							title:'信息提示'
						});
						self.getAdminList();
					}).fail(function(res){
						util.dialog.alert({
							msg:[res.msg]
						});
					});
				});			
			},
			submitAdminData:function(){
				let self = this;
				if(!self.adminData.admin_name){
					util.dialog.alert({msg:['请先输入用户名！']});
					return false;
				}
				if(!self.adminData.password && !self.isEditAdmin || (!self.adminData.password && self.isEditAdmin && !self.showPwd)){
					util.dialog.alert({msg:['请先输入密码！']});
					return false;
				}
				if((!self.adminData.againPwd && !self.isEditAdmin) || (!self.adminData.againPwd && self.isEditAdmin && !self.showPwd) ){
					util.dialog.alert({msg:['请先重复输入密码！']});
					return false;
				}
				if(self.adminData.password != self.adminData.againPwd && !self.isEditAdmin || ((self.adminData.password != self.adminData.againPwd) && self.isEditAdmin && self.showPwd)){
					util.dialog.alert({msg:['两次输入的密码不一致，请重新确定！']});
					return false;
				}
				service.submitAdmin(self.isEditAdmin,self.adminData).done(function(res){
					util.dialog.alert({
						msg:['操作成功！'],
						title:'信息提示'
					});	
					self.adminBoxMsg.show = false;	
					self.getAdminList();		
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg]
					});
				});					
			}
		},
		
		components:{
			// Pagination:Pagination,
			Modal:Modal,
		}

	};
</script>