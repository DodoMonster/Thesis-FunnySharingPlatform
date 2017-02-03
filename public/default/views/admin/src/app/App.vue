<template src="./App.tpl"></template>

<script>
	import Header from '../components/Header.vue';
	import Sidebar from '../components/Sidebar.vue';
	import Footer from '../components/Footer.vue';
	import AppDialog from '../components/Dialog.vue';
	// import store from '../store/store.js';

	export default {

		replace: false,

		name: 'App',

		data(){
			return{
				
			} 
			
		},

		ready(){

			if($('body').hasClass('content-appear')) {
				$('body').addClass('content-appearing')
				setTimeout(function() {
					$('body').removeClass('content-appear content-appearing');
				}, 800);
			}
			
			/* Preloader */
			setTimeout(function() {
				$('.preloader').fadeOut();
			}, 500);

			
			/* Scroll */
			if(jQuery.browser.mobile == false) {
				function initScroll(){
					$('.custom-scroll').jScrollPane({
						autoReinitialise: true,
						autoReinitialiseDelay: 100
					});
				}

				initScroll();

				$(window).resize(function() {
					initScroll();
				});
			}

			/* Scroll - if mobile */
			if(jQuery.browser.mobile == true) {
				$('.custom-scroll').css('overflow-y','scroll');
			}
		},

		methods:{
			
		},

		components: {
			AppHeader: Header,
			AppSidebar: Sidebar,
			AppFooter: Footer,
			AppDialog:AppDialog,
			// Modal:Modal,
			// AppBread : Bread,
		},

	};

</script>