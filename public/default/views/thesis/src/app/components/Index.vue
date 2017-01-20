<template src="./Index.tpl"></template>

<script>
	import vue from 'vue';
	import util from '../../libs/js/util.js';
	import store from '../../store/index.js';
	import service from '../../service/service.js';

	export default {
		replace: true,

		name: 'Index',

		route:{
            canReuse: false,
            data ({ to }) {
                this.type = to.params.thingsType;                
            }
        },

		data () {
			return {				
				store : store,
				funnyThings:{},
				funnyThingsList:[],
				thingsData:{},
				type:'hot',
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
			// showLoginBox:function(){
			// 	store.showLoginForm = true;
			// },
			getFunnyThingsList:function(){
				let self = this;
				service.getFunnyThingsList(self.page.cur).done(function(res){
					self.thingsData = res.data;
					switch(self.type) {
						case 'hot':
							self.funnyThings = self.thingsData.hot_things;
							break;
						case 'fresh':
							self.funnyThings = self.thingsData.fresh_things;
							break;
						case 'pic':
							self.funnyThings = self.thingsData.img_things;
							break;
						case 'word':
							self.funnyThings = self.thingsData.word_things;
							break;
						default:
							self.funnyThings = self.thingsData.hot_things;
							break;
					}
					self.funnyThingsList = self.funnyThings.list;
					self.page.totalNum = self.funnyThings.totalNum;
					self.page.totalPage = self.funnyThings.totalPage;
				}).fail(function(res){
					util.dialog.alert({
						msg:[res.msg],
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
						msg:[res.msg],
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
						msg:[res.msg]
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
						msg:[res.msg]
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
			},
			'page.cur':function(newVal,oldVal){
				let self = this;
				self.page.cur = newVal;
				self.getFunnyThingsList(self.page);
			},
			'type':function(newVal,oldVal){
				let self = this;
				console.log(self.thingsData);
				switch(newVal) {
						case 'hot':
						console.log(1);
							self.funnyThings = self.thingsData.hot_things;
							break;
						case 'fresh':
						console.log(2);
							self.funnyThings = self.thingsData.fresh_things;
							break;
						case 'pic':
						console.log(3);
							self.funnyThings = self.thingsData.img_things;
							break;
						case 'word':
						console.log(4);
							self.funnyThings = self.thingsData.word_things;
							break;
						default:
						console.log(5);
							self.funnyThings = self.thingsData.hot_things;
							break;
					}
					console.log(self.thingsData.fresh_things);
					console.log(self.funnyThings);
					self.funnyThingsList = self.funnyThings.list;
					self.page.totalNum = self.funnyThings.totalNum;
					self.page.totalPage = self.funnyThings.totalPage;
			}

		},

	};
</script>