<template src="./header.tpl"></template>

<script>
	import store from '../../../store/index.js';
	import service from '../../../service/service.js';	
	import $ from 'jquery';
	export default {

		replace: false,

		name: 'Header',

		route: {
            canReuse: false, //决定组件是否可以被重用
            data ({ to }) {
                let id = to.params.thingsType;
                let self = this;
                self.thingsType = id;
                console.info(to.params.thingsType);
            }
        },

        data(){
			return{
				userInfo:store.userInfo,
				store:store,
			}
		},

		ready(){
			$('#nav li').click(function(){
				$(this).addClass('highlight').siblings('li').removeClass('highlight');
			});
			if(this.userInfo.user_id){
				store.isLogin = true;
			}else{
				store.isLogin = false;
			}
		},
		

		methods:{
			showLoginBox:function(){
				store.showLoginForm = true;
			},

			logout:function(){
				let self = this;
				service.logout().done(function(res){
					alert(res.msg);	
					self.isLogin = false;
					store.isLogin = false;
					store.clearUserInfo();				
				}).fail(function(res){
					alert(res.msg);
				});
			}
		},

		watch:{
			'store.isLogin':function(newVal,oldVal){
				this.isLogin = newVal;
			}
		}
	}
</script>
