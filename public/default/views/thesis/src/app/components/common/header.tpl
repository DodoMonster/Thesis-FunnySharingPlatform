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
            <a href="javascript:;" @click="showLoginBox()" v-show="!isLogin">登录/注册</a>
            <a v-link="{name:'userHome',params:userInfo.user_id}" v-show="isLogin">{{userInfo.user_name}}</a>
        </div>
    </div>
</header>
