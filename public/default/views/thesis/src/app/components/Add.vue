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
					// is_anonymous:'0'
				}
			}			
		},

		methods:{
			publishThings:function(){
				let self = this;
				if(self.publishData.is_anonymous){
					self.publishData.is_anonymous = 1;
				}else{
					self.publishData.is_anonymous = 0;
				}
				var file = document.querySelector('#article_picture').files[0];
		        var fd = new FormData();
				if(!self.publishData.content){
					alert('请输入趣事内容');
					return false;
				}
		        
		        fd.append("things_img", file);
		        fd.append("things_content",self.publishData.content);
		        fd.append("is_anonymous",self.publishData.is_anonymous);
		      	        
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
		            	// alert('发表成功！');
		            	console.log(res);
		            }		                		                
		            }
		        xhr.send(fd);
		        return false;
			}
		}


	};
</script>