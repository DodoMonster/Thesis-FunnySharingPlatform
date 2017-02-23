<template src="./Index.tpl"></template>

<script>
	import $ from 'jquery';
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
				},
				userInfo:store.userInfo
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
				service.getFunnyThingsList(self.page.cur,self.userInfo.user_id).done(function(res){
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
			praiseUp:function(id,event){
				let self = this,
					$this = $(event.currentTarget),
					$num = $this.parents('.stats-buttons').siblings('.stats').find('.stats-vote .number');
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

			//不好笑
			trampDown:function(id,event){
				let self = this,
					$this = $(event.currentTarget);
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
				if(self.thingsData.hot_things){
					switch(newVal) {
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
				}
				
			}

		},

	};
</script>