import $ from 'jquery';
import Vue from 'vue';
import Router from 'vue-router';
import VueValidator from 'vue-validator';

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
	'/index': {
		component: Index
	},
	'/add': {
		component: Add
	},
	'/comment': {
		component: Comment
	},
	'/edit': {
		component: Edit
	},
	'/userHome': {
		component: UserHome
	},
});

router.redirect({
	'/': '/index'
});
router.start(App, '#app');
