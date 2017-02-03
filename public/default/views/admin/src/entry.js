import $ from 'jquery';
import Vue from 'vue'; 
import Router from 'vue-router';

import App from 'app/App.vue';
import Index from 'app/Index.vue';

import UserList from 'app/userList.vue';
import ThingsList from 'app/thingsList.vue';
import AdminList from 'app/adminList.vue';
import CommentList from 'app/commentList.vue';

Vue.use(Router);

let router = new Router({});

router.map({
	'/index': {
		name:'index',
		component: Index
	},
	'/userList':{
		name:'userList',
		component:UserList
	},
	'/thingsList':{
		name:'thingsList',
		component:ThingsList
	},

	'/adminList':{
		name:'adminList',
		component:AdminList
	},
	'/commentList':{
		name:'commentList',
		component:CommentList
	},
});

router.redirect({
    '/': '/index'
});
router.start(App, '#app');