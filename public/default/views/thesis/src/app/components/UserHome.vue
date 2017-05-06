<template src="./UserHome.tpl"></template>

<script>
	import service from '../../service/service.js';
	import $ from 'jquery';
	import store from '../../store/index.js';
	import Router from 'vue-router';
    import pagination from './common/pagination.vue';

	export default {

		replace: false,

		name: 'UserHome',

        data(){
        	return{
        		userId:'',
        		userData:{},
        		pageType:3,
        		pwdData:{
        			originPwd:'',
        			newPwd:'',
        			againPwd:''
        		},
                homePage:{
                    cur:1,
                    totalPage:0,
                    totalNum:0,
                },
                thingPage:{
                    cur:1,
                    totalPage:0,
                    totalNum:0,
                },
                favoritePage:{
                    cur:1,
                    totalNum:0,
                    totalPage:0,
                },
                commentPage:{
                    cur:1,
                    totalPage:0,
                    totalNum:0,
                },
                reply:{
                    comment_id:'',
                    content:'',
                    replied_id:'',
                    things_id:''
                },
                userInfo:{},
                userThing:[],
                userComment:[], 
                userReply:[],
                userFavorite:[],
                newUname:'',
                userInfo:store.userInfo,
                isSelf:false,
                isLogin:false
        	}
        },

        ready(){
            let self = this;
            self.userId = self.$route.params.user_id;
            try{
                self.userInfo = store.getUserInfo();
            }catch(e){}
            if(!self.userInfo.register_time){
                self.isLogin = false;
            }else{
                self.isLogin = true;
            }
            // console.log(self.userInfo);
            self.getUserInfo(self.userId);
        	
            if(self.userInfo && self.userInfo.user_id && self.userInfo.user_id == this.userId){
                self.isSelf = true;
            }                    	
        },

        methods:{
            //获取用户信息
        	getUserInfo:function(id,time){
        		let self = this;
        		service.getUserInfo(id,time).done(function(res){
        			self.userData = res.data || {};
                    self.newUname = self.userData.user_name;
                    if(!self.isLogin){
                        self.userInfo = res.data;
                    }
                    if(!self.isSelf){
                        self.getUserComment();                            
                    }else{
                        var storeData = {
                            user_id : self.userData.user_id,
                            user_name:self.userData.user_name,
                            user_photo:self.userData.user_photo,
                            register_time:self.userData.register_time
                        };
                        store.setUserInfo(storeData);
                        self.getUserReply();
                    }
        		}).fail(function(res){
        			alert(res.msg);
        		})
        	},
            //获取用户发表的趣事
            getUserThing:function(flag){
                let self = this;
                if(flag){
                    self.thingPage.cur = 1;
                }
                service.getUserThing(self.userId,self.thingPage.cur,self.userInfo.user_id).done(function(res){
                    self.userThing = res.data.list || {};
                    self.thingPage.totalPage = res.data.totalPage || 0;
                    self.thingPage.totalNum = res.data.totalNum || 0;
                }).fail(function(res){
                    alert(res.msg);
                });
            },
            //获取用户评论
            getUserComment:function(flag){
                let self = this;
                if(flag){
                    self.commentPage.cur = 1;
                }
                service.getUserComment(self.userId,self.commentPage.cur).done(function(res){
                    self.userComment = res.data.list || {};
                    self.commentPage.totalPage = res.data.totalPage || 0;
                    self.commentPage.totalNum = res.data.totalNum || 0;
                }).fail(function(res){
                    alert(res.msg);
                });
            },
            //获取回复
            getUserReply:function(flag){
                let self = this;
                if(flag){
                    self.commentPage.cur = 1;
                }
                service.getUserReply(self.userId,self.commentPage.cur).done(function(res){
                    self.userComment = res.data.comment || [];
                    self.userReply = res.data.reply || [];
                    self.commentPage.totalPage = res.data.totalPage || 0
                    self.commentPage.totalNum = res.data.totalNum || 0;
                }).fail(function(res){
                    alert(res.msg);
                });
            },
            //获取用户收藏
            getUserFavorite:function(flag){
                let self = this;
                if(flag){
                    self.favoritePage.cur = 1;
                }
                service.getUserFavorite(self.userId,self.favoritePage.cur,self.userInfo.user_id).done(function(res){
                    self.userFavorite = res.data.list || [];
                    self.favoritePage.totalPage = res.data.totalPage || 0;
                    self.favoritePage.totalNum = res.data.totalNum || 0;
                }).fail(function(res){
                    alert(res.msg);
                });
            },
            //改变导航
        	changeType:function(type,e){
        		let self = this;
                self.pageType = type;
        		switch(type) {
                    case 2:
                        self.getUserThing(true);
                        break;
                    case 3:
                        if(!self.isSelf){
                            self.getUserComment(true);                            
                        }else{
                            self.getUserReply(true);
                        }
                        break;                    
                    case 4:
                        self.getUserFavorite(true);
                        break;
                }
                $(e.currentTarget).addClass('active').parent('li').siblings().find('.active').removeClass('active');
        	},
            //修改头像
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
                            history.go(0);
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
            //修改密码
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
                    self.pwdData = {
                        originPwd:'',
                        newPwd:'',
                        againPwd:''
                    };
        			self.getUserInfo(self.userData.user_id);
        		}).fail(function(res){
        			if(res.msg){
        				alert(res.msg);
        			}else{
        				alert('修改密码失败，请重试！');
        			}
        		});
        	},
            //修改用户名
            changeUname:function(){
                let self = this;
                if(!self.newUname){
                    alert('用户名不能为空');
                    return false;
                }                
                service.changeUname(self.userData.user_id,self.newUname).done(function(res){
                    alert('修改用户名成功！');
                    self.getUserInfo(self.userData.user_id);
                    history.go(0);                    
                }).fail(function(res){
                    if(res.msg){
                        alert(res.msg);
                    }else{
                        alert('修改密码失败，请重试！');
                    }
                })
            },
            //点赞
            praiseUp:function(id,event){
                if(!this.isLogin){
                    alert('请先登录！');
                    return false;
                }
                let self = this,
                    $this = $(event.currentTarget),
                    $num = $(event.currentTarget).parents('.stats-buttons').siblings('.stats').find('.stats-vote .number');
                if($this.parent('li').siblings('li').find('a').hasClass('voted') || $this.hasClass('voted')){
                    return false;

                }else{
                    service.praiseUp(id,self.userInfo.user_id).done(function(res){
                        $this.addClass('voted'); 
                        $num.text(Number($num.text()) + 1);                                                  
                    }).fail(function(res){
                        alert(res.msg);
                    });
                }                       
                
            },
            //踩
            trampDown:function(id,event){
                let self = this,
                    $this = $(event.currentTarget);
                if(!this.isLogin){
                    alert('请先登录！');
                    return false;
                }
                if($this.parent('li').siblings('li').find('a').hasClass('voted') || $this.hasClass('voted')){
                    return false;
                }else{                                  
                    service.trampDown(id,self.userInfo.user_id).done(function(res){
                        $this.addClass('voted');
                    }).fail(function(res){
                        alert(res.msg);
                    });
                }
            },
            //收藏和取消收藏
            favorite:function(flag,id,e){
                if(!this.isLogin){
                    alert('请先登录！');
                    return false;
                }
                let self = this,
                    $this = $(e.currentTarget).find('i'),
                    className = $this.attr('class'),
                    $num = $(e.currentTarget).parents('.author').siblings('.stats').find('.stats-favorite .number');
                if(className.indexOf('fa-heart-o') !== -1){//未收藏则收藏
                    service.favorite(id,self.userInfo.user_id).done(function(res){
                        $this.attr('class','fa fa-heart deep-orange-color');
                        $num.text(Number($num.text()) + 1);
                        self.getUserFavorite(true);                                                                    
                    }).fail(function(res){
                        alert(res.msg);
                    });         
                }else{//已收藏则取消收藏                    
                    service.cancelFavorite(id,self.userInfo.user_id).done(function(res){                        
                        if(!flag){
                            $this.attr('class','fa fa-heart-o orange-color');
                            $num.text(Number($num.text()) - 1);
                        }
                        self.getUserFavorite(true);                        
                    }).fail(function(res){
                        alert(res.msg);
                    });                 
                }
            },
            //回复评论
            replyComment:function(e,id,name,comment_id,things_id) {
                let self = this,
                    $this = $(e.currentTarget);
                self.reply = {
                    content:$this.prev('input').val(),
                    replied_id:id,
                    replied_name:name,
                    comment_id:comment_id,
                    things_id:things_id
                };
                if(!self.reply.content){
                    alert('回复内容不能为空！');
                    return false;
                }
                console.log(self.reply);
                service.replyComment(self.userInfo,self.reply).done(function(res){
                    $this.parent().addClass('hide');
                    self.getUserReply(true);
                    self.reply = {
                        comment:'',
                        replied_id:'',
                        replied_name:'',
                        comment_id:'',
                        things_id:''
                    };
                }).fail(function(res){
                    alert(res.msg);
                });
            },               
            //退出登录
        	logout:function(){
				let self = this;
				service.logout().done(function(res){
					self.isLogin = false;
					store.isLogin = false;
					store.clearUserInfo();	
					let router = new Router;
					router.go('/index/hot');
				}).fail(function(res){
					alert(res.msg);
				});
        	}
        },

        watch:{
            'thingPage.cur':function(){
                this.getUserThing();
            },
            'commentPage.cur':function(){
                let self = this;
                if(!self.isSelf){
                    self.getUserComment(true);                            
                }else{
                    self.getUserReply(true);
                }
            },
            'favoritePage.cur':function(){
                this.getUserFavorite();
            },
        },

        components:{
            pagination:pagination
        }
	};

</script>