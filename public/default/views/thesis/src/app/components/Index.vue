<template src="./Index.tpl"></template>

<script>
	import vue from 'vue';
	import store from '../../store/index.js';
	import LoginBox from './common/login.vue';

	export default {
		replace: true,

		name: 'Index',

		data () {
			return {
				showLoginForm : false,
				store : store,
				funnyThingsList:[],
				page:{
					cur:1,
					totalNum:0,
					totalPage:0,
				}
			}
		},

		ready(){
			var self = this;
			self.getFunnyThingsList(self.page);
		},

		methods:{
			showLoginBox:function(){
				store.showLoginForm = true;
			},

			closeLoginBox:function(){				
				store.showLoginForm = false;
			},

			getFunnyThingsList:function(){
				let self = this;
				service.getFunnyThingsList(self.page).done(function(res){
					self.funnyThingsList = res.data.list;
					self.page.totalNum = res.data.totalNum;
					self.page.totalPage = res.data.totalPage;
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg];
					});
				});
			},

			//好笑
			praiseUp:function(id){
				let self = this;
				service.praiseUp().done(function(res){
					alert('点赞成功！');
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg];
					});
				});
			},

			//不好笑
			trampDown:function(id){
				let self = this;
				service.trampDown().done(function(res){
					alert('踩成功！');
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg];
					});
				});
			},

			//收藏
			favorite:function(id){
				let self = this;
				service.trampDown().done(function(res){
					alert('收藏成功');
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg];
					});
				});
			}

		},

		watch:{
			'store.showLoginForm':function(val,oldVal){
				if(val){
					store.showLoginForm = true;
				}else{
					store.showLoginForm = false;
				}
			}
		},

		components:{
			LoginBox : LoginBox,
		},

		watch:{
			'page.cur':function(newVal,oldVal){
				let self = this;
				self.page.cur = newVal;
				self.getFunnyThingsList(self.page);
			}
		}
	};
</script>