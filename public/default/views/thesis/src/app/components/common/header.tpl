<header>
    <div id="header" class="row clearfix">
        <div id="logo">
            <a href="./home#!/index/hot"></a>
        </div>
        <ul id="nav" class="menu-bar menu clearfix">
            <li :class="thingsType == 'hot' ? 'highlight' : ''">
                <a v-link="{name:'index',params:{thingsType:'hot'}}">热门</a>
            </li>
            <li :class="thingsType == 'fresh' ? 'highlight' : ''">
                <a v-link="{name:'index',params:{thingsType:'fresh'}}">新鲜</a>
            </li>
           
            <li :class="thingsType == 'word' ? 'highlight' : ''">
                <a v-link="{name:'index',params:{thingsType:'word'}}">文字</a>
            </li>
            <li :class="thingsType == 'pic' ? 'highlight' : ''">
                <a v-link="{name:'index',params:{thingsType:'pic'}}">图片</a>
            </li>         
            <li>
                <a v-link="{name:'add'}">投稿</a>
            </li>
        </ul>

        <div id="loginBtn">
            <a href="javascript:;" @click="showLoginBox()" v-show="!store.isLogin">登录/注册</a>
            <a v-link="{name:'userHome',params:{'user_id':store.userInfo.user_id}}" v-show="store.isLogin" class="userinfo">
                <!-- <img v-bind:src="store.userInfo.user_photo" alt="用户头像"> -->
                <img src="/uploads/avatar/default-avatar.png" alt="">
            {{store.userInfo.user_name}}</a>
            <a class="logout-btn" @click="logout" v-show="store.isLogin">退出</a>
        </div>
    </div>
</header>
