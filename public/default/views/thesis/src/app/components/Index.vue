<template src="./Index.tpl"></template>

<script>
	import $ from 'jquery';
	import util from '../../libs/js/util.js';
	import store from '../../store/index.js';
	import service from '../../service/service.js';
	import pagination from './common/pagination.vue';
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
					totalNum:20,
					totalPage:5,
				},
				userInfo:store.userInfo
			}
		},

		ready(){
			var self = this;
			self.getFunnyThingsList();			
		},

		methods:{
			//获取趣事
			getFunnyThingsList:function(flag,type){
				let self = this,
					_type = type || self.type;
				if(!self.userInfo){
					var user_id = '';
				}else{
					var user_id = self.userInfo.user_id;
				}
				if(flag){
					self.page.cur = 1;
				}
				service.getFunnyThingsList(self.page.cur,user_id,_type).done(function(res){
					self.funnyThingsList = res.data.list;				
					self.page.totalNum = res.data.totalNum;
					self.page.totalPage = res.data.totalPage;
				}).fail(function(res){
					alert(res.msg);
				});
			},
		
			//好笑
			praiseUp:function(id,event){
				if(!this.userInfo || !this.userInfo.user_id){
					alert('请先登录！');
					return false;
				}
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
				if(!this.userInfo || !this.userInfo.user_id){
					alert('请先登录！');
					return false;
				}
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
				if(!this.userInfo || !this.userInfo.user_id){
					alert('请先登录！');
					return false;
				}
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
				this.getFunnyThingsList(false,this.type);
			},
			'type':function(newVal,oldVal){
				this.getFunnyThingsList(true,newVal);									
			}

		},

		components:{
			pagination:pagination
		}
	};
</script>