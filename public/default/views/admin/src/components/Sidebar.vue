<template src="./Sidebar.tpl"></template>


<script>
	import Router from 'vue-router';
	import store from '../store/store.js';
	export default {

		replace: false,

		name: 'Sidebar',

		data () {
			return store.pageData;
		},

		ready(){
			$('.large-sidebar .site-sidebar li.with-sub').each(function() {
				var li = $(this),
					clickLink = li.find('>a'),
					subMenu = li.find('>ul');

				li.click(function(e){
					if ($(this).hasClass('active')) {
						li.removeClass('active');
						$(this).find('>ul').slideUp(200);
					} else {
						if($(this).siblings().hasClass('active')){
							$(this).siblings().removeClass('active').find('ul').slideUp(200);
						}
						li.addClass('active');
						$(this).find('>ul').slideDown(200);
					}
				});				
			});

			/* Sidebar - show and hide */
			$('.site-header .collapse-button').click(function() {
				if ($('body').hasClass('site-sidebar-opened')) {
					$(this).removeClass('active');
					$('body').removeClass('site-sidebar-opened');
					if(jQuery.browser.mobile == false){
						$('html').css('overflow','auto');
					}
				} else {
					$(this).addClass('active');
					$('body').addClass('site-sidebar-opened');
					if(jQuery.browser.mobile == false){
						$('html').css('overflow','hidden');
					}
				}
				if ($('body').hasClass('compact-sidebar')) {
					$('body').removeClass('compact-sidebar').addClass('large-sidebar');
					if(jQuery.browser.mobile == false) {
						$('body').addClass('fixed-sidebar');
						sidebarIfActive();
					}
				}
			});

			/* Sidebar - overlay */
			$('.site-sidebar-overlay').click(function() {
				$('.site-header .collapse-button').removeClass('active');
				$('body').removeClass('site-sidebar-opened');
				if(jQuery.browser.mobile == false){
					$('html').css('overflow','auto');
				}
			});
		},
		methods:{
			refreshLocalPage:function(url){
				let randomKey = Math.random(1,10),
					router = new Router;
				url += '?' + randomKey;
				// console.log(url);
				router.go(url);
			},

			//阻止冒泡
			stopPropagation:function(e) {  
			    e = e || window.event;  
			    if(e.stopPropagation) { //W3C阻止冒泡方法  
			        e.stopPropagation();  
			    } else {  
			        e.cancelBubble = true; //IE阻止冒泡方法  
			    }  
			    
			},
		}

	};
</script>