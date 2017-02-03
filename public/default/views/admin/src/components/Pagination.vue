<template src="./Pagination.tpl"></template>


<script>
	export default {

		replace: true,

		name: 'Pagination',

		props:['page'],

		data(){
			return {
				cur:1
			}
		},

		methods:{
			btnClick:function(index){
				this.page.cur = index;
			}
		},

		computed:{
			indexs:function(){
				let left = 1,
					arr = [],
					num	= this.page.totalPage,
					right = num;
				if(num > 10){
					if(this.page.cur > 5 && this.page.cur < num - 4){
						left = this.page.cur - 5;
						right = this.page.cur + 4;
					}else{
						if(this.page.cur <= 5){
							left = 1;
							right = 10;
						}else{
							right = num;
							left = num - 9;
						}
					}
				}

				while ( left <= right) {
					arr.push(left);
					left++;
				}
				return arr;
			}
		}

	};
</script>