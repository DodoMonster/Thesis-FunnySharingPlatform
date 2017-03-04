<template src="./Comment.tpl"></template>

<script>
	import $ from 'jquery';
	import util from '../../libs/js/util.js';
	import store from '../../store/index.js';
	import service from '../../service/service.js';
	import pagination from './common/pagination.vue';
	export default {

		replace: false,

		name: 'Comment',

		data(){
			return{
				thingInfo:{},
				thing_id:'',
				page:{
					cur:1,
					totalPage:0,
					totalNum:0
				},
				reply:{
					comment_id:'',
					content:'',
					replied_id:'',
					things_id:''
				},
				userInfo:store.userInfo,
				commentContent:'',
				commentsList:[],
			}
		},

		ready(){			
			let self = this;
			self.thing_id = this.$route.query.thing_id;   
			this.getThingInfo();
			this.getCommentsList(true);
		},

		methods:{
			//获取趣事信息
			getThingInfo:function(){
				let self = this;		
				if(self.userInfo && self.userInfo.user_id){
					var user_id = self.userInfo.user_id
				}else{
					var user_id = '';
				}
				service.getThingInfo(self.thing_id,user_id).done(function(res){
					self.thingInfo = res.data;
				}).fail(function(res){
					alert(res.msg);
				})
			},

			//获取评论列表
			getCommentsList:function(flag){
				let self = this;		
				if(flag){
					self.page.cur = 1;
				}
				self.commentsList = [];
				service.getCommentsList(self.page.cur,self.thing_id).done(function(res){
					self.commentsList = res.data.list || [];
					self.page.totalPage = res.data.totalPage;
					self.page.totalNum = res.data.totalNum;
				}).fail(function(res){
					alert(res.msg);
				})
			},

			//评论趣事
			comment:function(){
				let self = this;		
				if(!self.commentContent){
					alert('评论不能为空！');
					return false;
				}
				if(!self.userInfo.user_id){
					alert('请先登录');
					return false;
				}
				service.comment(self.thing_id,self.commentContent,self.userInfo.user_id).done(function(res){
					alert('评论成功！');
					self.commentContent = '';
					self.getCommentsList(true);
				}).fail(function(res){
					alert(res.msg);
				})
			},

			//好笑
			praiseUp:function(event){
				let self = this,
					$this = $(event.currentTarget);
				if($this.parent('li').siblings('li').find('a').hasClass('voted') || $this.hasClass('voted')){
					return false;

				}else{
					service.praiseUp(self.thing_id,self.userInfo.user_id).done(function(res){
				    	$this.addClass('voted');			
					}).fail(function(res){
						alert(res.msg);
					});
				}						
				
			},

			//不好笑
			trampDown:function(event){
				let self = this,
					$this = $(event.currentTarget);
				if($this.parent('li').siblings('li').find('a').hasClass('voted') || $this.hasClass('voted')){
					return false;
				}else{									
					service.trampDown(self.thing_id,self.userInfo.user_id).done(function(res){
						$this.addClass('voted');
					}).fail(function(res){
						alert(res.msg);
					});
				}
			},
			//收藏和取消收藏
			favorite:function(id,e){
				let self = this,
					$this = $(e.currentTarget).find('i'),
					className = $this.attr('class'),
					$num = $(e.currentTarget).parents('.stats-buttons').siblings('.stats').find('.stats-favorite .number');
				if(className.indexOf('fa-heart-o') !== -1){//未收藏
					service.favorite(id,self.userInfo.user_id).done(function(res){
						$this.attr('class','fa fa-heart deep-orange-color');
						$num.text(Number($num.text()) + 1);	
					}).fail(function(res){
						alert(res.msg);
					});			
				}else{//已收藏
					service.cancelFavorite(id,self.userInfo.user_id).done(function(res){
						$this.attr('class','fa fa-heart-o orange-color');
						$num.text(Number($num.text()) - 1);
					}).fail(function(res){
						alert(res.msg);
					});					
				}
			},	
			//显示回复评论的输入框
			showReplyBox:function(e,id,name,comment_id,things_id,flag){
				let self = this,
					$this = $(e.currentTarget);
				self.reply = {
					comment:'',
					replied_id:id,
					replied_name:name,
					comment_id:comment_id,
					things_id:things_id
				};
				if(!$this.hasClass('show')){
					if(!flag){
						var $input = $this.addClass('show').text('关闭').parents('.comment-content').siblings('.reply-input');
					}else{
						var $input = $this.parents('.reply-content-wrap').siblings('.reply-input');
					}
					$input.removeClass('hide');
					
				}else{
					if(!flag){
						var $input = $this.removeClass('show').text('回复').parents('.comment-content').siblings('.reply-input');
					}else{
						var $input = $this.parents('.reply-content-wrap').siblings('.reply-input');
					}
					$input.addClass('hide');
				}
				$input.find('input').focus();
			},
			//回复评论
			replyComment:function(e) {
				let self = this,
					$this = $(e.currentTarget);
				self.reply.content = $this.prev('input').val();
				if(!self.reply.content){
					alert('回复内容不能为空！');
					return false;
				}
				service.replyComment(self.userInfo,self.reply).done(function(res){
					$this.parent().addClass('hide');
					self.getCommentsList(true);
				}).fail(function(res){
					alert(res.msg);
				});
			}						
		},
		components:{
			pagination:pagination
		},
		wartch:{
			'page.cur':function(){
				this.getCommentsList();
			}
		}
	};

</script>
