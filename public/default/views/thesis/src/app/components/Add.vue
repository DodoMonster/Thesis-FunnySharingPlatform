<template src="./Add.tpl"></template>

<script>
	import service from '../../service/service.js';
	export default {
		replace: false,

		name: 'Add',

		data () {
			return{
				publishData:{
					content:'',
					img:'',
					is_anonymous:'0'
				}
			}			
		},

		methods:{
			publishThings:function(){
				let self = this;
				service.publishThings(self.publishData).done(function(res){
					alert('发表成功！');
				}).fail(function(res){
					alert(res.msg);
				});
			}
		}


	};
</script>