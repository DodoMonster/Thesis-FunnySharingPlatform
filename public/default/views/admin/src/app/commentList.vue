<template src="./commentList.tpl"></template>

<script>
	import Vue from 'vue';
	import Pagination from '../components/Pagination.vue';
	import util from '../libs/js/util.js';
	import service from '../service/service.js';

	export default {

		replace: false,

		name: 'CommentList',

		route:{
            canReuse: false
        },

		data(){
			return{
				page:{
					cur:1,//当前页
					totalNum:0,//总共多少条数据
					totalPage:0//总共多少页
				},		
				searchParam:{
					content:'',
				},
				commentList:[],
			}
		},

		ready(){
			var self = this;
			self.getCommentList();
		},


		methods:{
			//获取评论列表
			getCommentList:function(flag){
				var self = this;
				if(flag){
					self.page.cur = 1;
				}
				self.commentList = [];
				service.getCommentList(self.page.cur,self.searchParam.content).done(function(res){
					self.commentList = res.data.list || [];
					self.page.totalPage = res.data.totalPage || 0;
					self.page.totalNum = res.data.totalNum || 0;
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
			//审核评论
			approvalComment:function(id){
				var self = this;
				util.dialog.show({
					msg:['确定审核通过该趣事？'],
					title:'警告提示'
				}).confirm(function(){
					this.emit('hide-dialog');
						service.approvalComment(id).done(function(res){
						util.dialog.alert({
							msg:[res.msg],
							title:'信息提示'
						});
						self.getCommentList(true);
					}).fail(function(res){
						util.dialog.alert({
							msg:[res.msg]
						});
					});
				});		
			},
			//删除评论
			deleteComment:function(id){
				var self = this;
				util.dialog.show({
					msg:['确定删除该趣事？'],
					title:'警告提示'
				}).confirm(function(){
					this.emit('hide-dialog');
						service.deleteComment(id).done(function(res){
						util.dialog.alert({
							msg:[res.msg],
							title:'信息提示'
						});
						self.getCommentList(true);
					}).fail(function(res){
						util.dialog.alert({
							msg:[res.msg]
						});
					});
				});			
			},
		},

		components:{
			Pagination:Pagination,
		},

		watch:{
			'page.cur':function(newVal,oldVal){
				this.getUserList();
			}
		}


	};
</script>