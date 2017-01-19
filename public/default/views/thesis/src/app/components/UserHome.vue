<template src="./UserHome.tpl"></template>

<script>
	import service from '../../service/service.js';
	import $ from 'jquery';
	import store from '../../store/index.js';
	import Router from 'vue-router';
	export default {

		replace: false,

		name: 'UserHome',

		route: {
            canReuse: false, //决定组件是否可以被重用
            data ({ to }) {
                let id = to.params.user_id;
                let self = this;
                self.userId = id;
                // console.info(to.params.articleId);
            }
        },

        data(){
        	return{
        		userId:'',
        		userData:{},
        		pageType:1,
        		pwdData:{
        			originPwd:'',
        			newPwd:'',
        			againPwd:''
        		},
                newUname:'',
                store:store,
        	}
        },

        ready(){
        	this.getUserInfo(this.userId);            
        	$('.user-header-menu a').click(function(){
        		$(this).addClass('active').parent('li').siblings().find('.active').removeClass('active');
        	})
        },

        methods:{
        	getUserInfo:function(id){
        		let self = this;
        		service.getUserInfo(id).done(function(res){
        			self.userData = res.data || {};
                    self.newUname = self.userData.user_name;
                    store.setUserInfo(self.userData);   
                    store.userInfo = store.getUserInfo();
        		}).fail(function(res){
        			alert(res.msg);
        		})
        	},
        	changeType:function(type){
        		let self = this;
        		self.pageType = type;
        	},
        	changePhoto:function(){
        		let self = this;
        		var file = document.querySelector('#user_avatar').files[0];
		        var fd = new FormData();
		        if(!file){
		        	alert('上传的图片不能为空！');
		        	return false;
		        }
		        fd.append("photo", file);
		        fd.append("user_id",self.userData.user_id);
		        var xhr = new XMLHttpRequest();
	        	xhr.open('POST', '/thesis/changeAvatar', true);
	        	xhr.upload.onprogress = function(e) {
		            if (e.lengthComputable) {
		                var percentComplete = (e.loaded / e.total) * 100;
		                console.log(percentComplete + '% uploaded');
		            }
		        };
		        xhr.onload = function() {
		            if (this.status == 200) {
		                var res = JSON.parse(this.response);
		               	if(res.code == 0){
			            	alert('修改头像成功');
							self.getUserInfo(self.userData.user_id);
			            }else{
							alert(res.msg);
			            }
		                		                		               
		            }else{
		            	alert('网络出错，请刷新页面重试！');
		            }
		        };
		        xhr.send(fd);
		        return false;
        	},

        	changePwd:function(){
        		let self = this;
        		if(!self.pwdData.originPwd){
        			alert('原密码不能为空');
        			return false;
        		}
        		if(!self.pwdData.newPwd){
        			alert('新密码不能为空');
        			return false;
        		}
        		if(!self.pwdData.againPwd){
        			alert('请重复新密码！');
        			return false;
        		}
        		if(self.pwdData.newPwd !== self.pwdData.againPwd){
        			alert('两次密码输入不一致，请重新确认密码！');
        			return false;
        		}
        		service.changePwd(self.userData.user_id,self.pwdData).done(function(res){
        			alert('修改密码成功！');
        			self.getUserInfo(self.userData.user_id);
        		}).fail(function(res){
        			if(res.msg){
        				alert(res.msg);
        			}else{
        				alert('修改密码失败，请重试！');
        			}
        		});
        	},
            changeUname:function(){
                let self = this;
                if(!self.newUname){
                    alert('用户名不能为空');
                    return false;
                }                
                service.changeUname(self.userData.user_id,self.newUname).done(function(res){
                    alert('修改用户名成功！');
                    self.getUserInfo(self.userData.user_id);
                }).fail(function(res){
                    if(res.msg){
                        alert(res.msg);
                    }else{
                        alert('修改密码失败，请重试！');
                    }
                })
            },
        	logout:function(){
				let self = this;
				service.logout().done(function(res){
					alert(res.msg);	
					self.isLogin = false;
					store.isLogin = false;
					store.clearUserInfo();	
					let router = new Router;
					router.go('/index/hot');
				}).fail(function(res){
					alert(res.msg);
				});
        	}
        }
	};

</script>