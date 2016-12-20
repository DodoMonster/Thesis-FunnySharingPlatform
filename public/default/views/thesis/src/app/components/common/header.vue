<template src="./header.tpl"></template>

<script>
	import store from '../../../store/index.js';
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

		ready(){
			$('#nav li').click(function(){
				$(this).addClass('highlight').siblings('li').removeClass('highlight');
			});
		},
		data(){
			return{
			}
		},

		methods:{
			showLoginBox:function(){
				store.showLoginForm = true;
			}
		},
	}
</script>
