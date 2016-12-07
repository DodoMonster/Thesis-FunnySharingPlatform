<template src="./app.tpl"></template>

<script>
	import Header from './components/common/header.vue';
	import Footer from './components/common/footer.vue';

	export default {

		replace: false,

		name: 'App',

		components: {
			AppHeader: Header,
			AppFooter: Footer
		}

	};

</script>
