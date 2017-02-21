<template src="./Comment.tpl"></template>

<script>
	import $ from 'jquery';
	import util from '../../libs/js/util.js';
	import store from '../../store/index.js';
	import service from '../../service/service.js';
	export default {

		replace: false,

		name: 'Comment',

		data(){
			return{
				thingInfo:{},
				thing_id:'',
				is_praise:false,
				is_tramp:false,
				page:{
					cur:1,
					totalPage:0,
					totalNum:0
				},
				userInfo:store.userInfo,
				commentContent:'',
				commentsList:[],
			}
		},

		ready(){			
			let self = this;
			self.thing_id = this.$route.query.thing_id;
			self.is_praise = this.$route.query.is_praise;
	        self.is_tramp = this.$route.query.is_tramp;		    
			this.getThingInfo();
			this.getCommentsList(true);
			console.log(this.thing_id);
		},

		methods:{
			//获取趣事信息
			getThingInfo:function(){
				let self = this;				
				service.getThingInfo(self.thing_id).done(function(res){
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
				}
				service.comment(self.thing_id,self.commentContent).done(function(res){
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
		}
	};

</script>
