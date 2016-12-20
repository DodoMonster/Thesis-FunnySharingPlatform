<app-header></app-header>

<router-view></router-view>

<div id="login-box" @click="closeLoginBox()" v-show="store.showLoginForm">
	<login-box></login-box>
</div>

<app-footer></app-footer>
