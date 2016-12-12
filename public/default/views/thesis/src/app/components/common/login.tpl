<!--start 登录页面-->
<div class="signin-box animated fadeInUp" id="login-form" @click="cancelBubble">
    <div class="sigin-left">
      <!--   <div class="signin-account clearfix">
            <h4 class="social-signin-heading">社交帐号登录</h4>
            <a rel="external nofollow" oauth_href="" href="https://open.weixin.qq.com/connect/qrconnect?appid=wx559af2d26b56c655&amp;redirect_uri=http%3A%2F%2Fwww.qiushibaike.com%2Fnew4%2Fsession%3Fsrc%3Dwx&amp;response_type=code&amp;scope=snsapi_login#wechat_redirect" class="social-btn social-wechat">
            使用 微信 账号</a>
            <a rel="external nofollow" oauth_href="" href="https://api.weibo.com/oauth2/authorize?client_id=63372306&amp;redirect_uri=http%3A%2F%2Fwww.qiushibaike.com%2Fnew4%2Fsession" class="social-btn social-weibo">
            使用 微博 账号</a>
            <a rel="external nofollow" oauth_href="" href="https://graph.qq.com/oauth2.0/authorize?which=Login&amp;display=pc&amp;client_id=100251437&amp;response_type=code&amp;redirect_uri=www.qiushibaike.com/new4/session?src=qq" class="social-btn social-qq">
            使用 QQ 账号 </a>
        </div> -->
        <div class="signin-form clearfix">
            <h4 class="social-signin-heading">糗事百科账号登录</h4>
            <form method="post" action="/thesis/Login">
                <div class="signin-section clearfix">
                <input type="text" class="form-input form-input-first" name="username" placeholder="昵称或邮箱">
                <input type="password" class="form-input" name="password" placeholder="密码">
                <input type="checkbox" id="remember_me" name="remember_me" checked="" value="checked" style="display:none">
                </div>
                <div class="signin-error" id="signin-error"></div>
                <button type="submit" id="form-submit" class="form-submit">登录</button>
            </form>
        </div>
        <div class="signin-foot clearfix">
            <a rel="nofollow" href="/new4/fetchpass" class="fetch-password">忘记密码?</a>
        </div>
    </div>
</div>
<!--end 登录页面-->	
