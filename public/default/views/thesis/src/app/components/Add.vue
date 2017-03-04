<template src="./Add.tpl"></template>

<script>
	import service from '../../service/service.js';
	import Router from 'vue-router';
	import store from '../../store/index.js';
	export default {
		replace: false,

		name: 'Add',

		data () {
			return{
				publishData:{
					content:'',
					img:'',
				},
				userInfo:store.userInfo,
			}			
		},

		methods:{
			publishThings:function(){
				let self = this;
				if(!self.userInfo || !self.userInfo.user_id){
					alert('请先登录！');
					return false;
				}
				var file = document.querySelector('#article_picture').files[0];
		        var fd = new FormData();
				if(!self.publishData.content){
					alert('请输入趣事内容');
					return false;
				}
		        
		        fd.append("things_img", file);
		        fd.append("things_content",self.publishData.content);
		        fd.append("user_id",self.userInfo.user_id);
		      	        
		        var xhr = new XMLHttpRequest();

	        	xhr.open('POST', '/thesis/publishThings', true);
		        
		        xhr.upload.onprogress = function(e) {
		            if (e.lengthComputable) {
		                var percentComplete = (e.loaded / e.total) * 100;
		                console.log(percentComplete + '% uploaded');
		            }
		        };
		        xhr.onload = function() {
		            if (this.status == 200) {
		                var res = JSON.parse(this.response);
		                if(res.code === 0){
		            		alert('趣事发表成功！');
		            		let router = new Router();
		            		router.go('/index/fresh');
		                }else{
		                	alert(res.msg);
		                }		                		                
		            }
		        }
		        xhr.send(fd);
		        return false;
			}
		}


	};
</script>