
<div class="site-sidebar-overlay"></div>
<div class="site-sidebar">
	<a class="logo" href="/admin/index">
		<img src="http://imgstatic.ufile.ucloud.com.cn/thesis_logo.png" alt="logo">
	</a>
	<div class="custom-scroll custom-scroll-dark">
		<ul class="sidebar-menu">
			<li class="menu-title m-t-0-5">Navigation</li>
			<li class="with-sub" v-for="lvl1Nav in links" id="{{lvl1Nav.menu}}">
				<a href="javascript:;" class="waves-effect waves-light">
					<span class="s-caret"><i class="ti-angle-down"></i></span>
					<span class="s-icon"><i class="{{lvl1Nav.icon}}"></i></span>
					<span class="s-text">{{lvl1Nav.title}}</span>
				</a>
				<ul class="subnav" v-if="lvl1Nav.links" @click="stopPropagation($event)">
				<template v-for="lvl2Nav in lvl1Nav.links">
					<li>
						<a class="sdkList" @click="refreshLocalPage(lvl2Nav.url)">{{lvl2Nav.title}}</a>
					</li>
				</template>
					
				</ul>
			</li>			
		</ul>
	</div>
</div>