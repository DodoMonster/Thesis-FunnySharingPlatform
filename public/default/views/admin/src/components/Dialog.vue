<template src="./Dialog.tpl"></template>

<script>
	import $ from 'jquery';
	import util from '../libs/js/util.js';

	export default {
	    replace: true,
	    name: 'Dialog',

	    data () {
	        return {
	        	isShow: false,
	        	isAlertDailog:false,
	        	error:'',
				dialogInfo:{
					cancelBtnText : '取消',
	    			confirmBtnText : '确定',
					model: 'text',
					size:'xs',
				},

	        }
	    },

	    ready () {
	    	let self = this;
	    	util.on('show-dialog', (data) => {
	    		self.isShow = true;
				self.isAlertDailog = false;
				self.dialogInfo = data;
				self.dialogInfo.cancleBtnText = data.cancleBtnText;
	    		self.dialogInfo.confirmBtnText = data.confirmBtnText;

	    	});
	    	util.on('hide-dialog',()=>{
	    		self.isShow = false;
	    	});
			util.on('alert-dialog',(data) => {
				self.isShow = true;
				self.isAlertDailog  = true;
				self.dialogInfo = data;
				self.dialogInfo.size = 'sm';
				self.dialogInfo.confirmBtnText = data.confirmBtnText;
			});
	    },

	    methods: {
	    	confirm () {
	    		var ret = this.model == 'formDialog' && this.$refs.dialogbody.$data.custom;
	    		util.emit('confirm-dialog',ret);
	    	},

			cancel(){
				var self = this;
	    		self.isShow = false;
				self.dialogInfo = {};
		 		util.emit('cancel-dialog');
			},


	    },


	}
</script>
