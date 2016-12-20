import $ from 'jquery';
import Vue from 'vue';
import Router from 'vue-router';

import "./scss/app.scss";
import App from "./app/app.vue";

import Index from './app/components/Index.vue';
import Add from './app/components/Add.vue';
import Comment from './app/components/Comment.vue';
import Edit from './app/components/Edit.vue';
import UserHome from './app/components/UserHome.vue';

Vue.use(Router);

let router = new Router({});

router.map({
	'/index/:thingsType': {
		name:'index',
		component: Index
	},
	// '/index/:thingsType': {
	// 	name:'fresh',
	// 	component: Index
	// },
	// '/index/:thingsType': {
	// 	name:'pic',
	// 	component: Index
	// },
	// '/index/:thingsType': {
	// 	name:'word',
	// 	component: Index
	// },
	'/index/add': {
		name:'add',
		component: Add
	},
	'/comment': {
		name:'comment',
		component: Comment
	},
	'/edit': {
		name:'edit',
		component: Edit
	},
	'/add': {
		name:'add',
		component: Add
	},
	'/userHome': {
		component: UserHome
	},
});

router.redirect({
	'/': '/index'
});
router.start(App, '#app');
