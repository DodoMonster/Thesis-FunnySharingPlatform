<template src="./Sidebar.tpl"></template>

<style></style>

<script>
	export default {

		replace: false,

		name: 'Sidebar',

		data () {
			return {
				links: [
					{
						name: 'modal',
						iconClass: 'am-icon-file',
						url: '/modal'
					},
					{
						name: 'pagination',
						iconClass: 'am-icon-file',
						url: '/pagination'
					},
					{
						name: 'taginput',
						iconClass: 'am-icon-file',
						url: '/taginput'
					},
					{
						name: 'typeahead',
						iconClass: 'am-icon-file',
						url: '/typeahead'
					},
					{
						name: 'datetimepicker',
						iconClass: 'am-icon-file',
						url: '/datetimepicker'
					}
				]
			}
		}

	};
</script>