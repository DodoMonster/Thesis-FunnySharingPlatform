<template src="./app.tpl"></template>

<script>
	import Header from './components/common/header.vue';
	import Footer from './components/common/footer.vue';
	import LoginBox from './components/common/login.vue';
	import store from '../store/index.js';

	export default {

		replace: false,

		name: 'App',

		data () {
			return {				
				store : store,
			}
		},

		ready(){			
			if(store.getUserInfo()){
				if(store.userInfo.user_id){
					store.isLogin = true;										
				}
			}
		},

		methods:{
			closeLoginBox:function(){			
				store.showLoginForm = false;
			},
		},

		watch:{
			'store.showLoginForm':function(val,oldVal){
				if(val){
					store.showLoginForm = true;
				}else{
					store.showLoginForm = false;
				}
			},

		},

		components: {
			AppHeader: Header,
			AppFooter: Footer,
			LoginBox : LoginBox,
		}

	};

</script>
