<div class="user-main clearfix">
    <div class="user-header">
        <a href="/users/29459066/" class="user-header-avatar">
        <img src="http://pic.qiushibaike.com/system/avtnew/2945/29459066/medium/20150717105850.jpg" alt="Dodo Monster">
        </a>
        <div class="user-header-cover">
            <h2>Dodo Monster</h2>
        </div>
        <ul class="user-header-menu">
            <li>
                <a href="/users/29459066/" class="active">主页</a>
            </li>
            <li>
                <a href="/users/29459066/articles/">糗事</a>
            </li>
            <li>
                <a href="/users/29459066/comments/">评论</a>
            </li>
            <li>
                <a href="/my/edit">设置</a>
            </li>
        </ul>
    </div>
    <div class="user-col-right">

        <div id="editInfo" class="user-block user-setting clearfix">
            <h3>更换头像</h3>
            <form action="/my/edit" enctype="multipart/form-data" id="edit_user_29459066" method="post">
                <input type="hidden" name="_xsrf" value="2|4fca4c9c|8798c7b71eaba7bc1d8939b71abddac8|1478965973">
                <ul>
                    <li>
                        <img alt="Dodo Monster" class="user-setting-avatar" src="http://pic.qiushibaike.com/system/avtnew/2945/29459066/medium/20150717105850.jpg">
                    </li>
                    <li>
                        <input name="_method" type="hidden" value="put">
                        <input id="user_avatar" name="user[avatar]" size="30" type="file">
                        <input id="user_submit" name="commit" type="submit" value="确定上传">
                    </li>
                    <li>
                    图片支持JPG格式，尺寸小于200x200像素，文件容量2M以内。
                    </li>
                </ul>
            </form>
        </div>
        <div class="user-block user-setting clearfix">
            <h3>账号绑定</h3>
            <ul>
                <li>
                    <a rel="external nofollow" oauth_href="" href="https://open.weixin.qq.com/connect/qrconnect?appid=wx559af2d26b56c655&amp;redirect_uri=http%3A%2F%2Fwww.qiushibaike.com%2Fmy%2Fedit%3Fsrc%3Dwx&amp;response_type=code&amp;scope=snsapi_login#wechat_redirect" class="social-wechat" name="third_account[‘type’]">
                    绑定微信账号
                    </a>
                </li>
                <li>
                    <a rel="external nofollow" oauth_href="" href="https://api.weibo.com/oauth2/authorize?client_id=63372306&amp;redirect_uri=http%3A%2F%2Fwww.qiushibaike.com%2Fmy%2Fedit" class="social-weibo" name="third_account[‘type’]">
                    绑定微博账号
                    </a>
                </li>
                <li>
                    <a class="social-btn social-qq" rel="nofollow">
                    北城亂世Sum
                    </a>
                    <a href="javascript:;" data-type="1" rel="nofollow">
                    解绑
                    </a>
                </li>
                <li>
                    <a rel="external nofollow" href="javascript:;" class="social-email" data-email="" act_bind_email="" bind-type="new" title="">
                    绑定邮箱
                    </a>
                </li>
            </ul>
        </div>
        <div id="editPass" class="user-block user-setting clearfix">
            <h3>修改密码</h3>
            <form id="new_user">
                <ul>
                    <li>
                        <label for="old_password" class="user-setting-inputlable">当前密码</label>
                        <input id="old_password" name="old_password" size="30" type="password">
                    </li>
                    <li>
                        <label for="new_password" class="user-setting-inputlable">新密码</label>
                        <input id="new_password" name="password" size="30" type="password">
                    </li>
                    <li>
                        <label for="password_confirmation" class="user-setting-inputlable">重复新密码</label>
                        <input id="password_confirmation" name="password_confirmation" size="30" type="password">
                    </li>
                    <li>
                        <input name="commit" action_change_pass="" type="button" value="确认修改">
                    </li>
                </ul>
            </form>
        </div>
        <div class="user-block user-setting clearfix">
            <h3>帐号</h3>
            <ul>
                <li>
                    <a href="/new4/logout" class="exit" rel="nofollow">退出登录</a>
                </li>
            </ul>
        </div>
        <!-- popup Start -->
        <div id="bg" class="mask" style="height: 1677px;"></div>
        <div id="popDiv" class="bind-email"></div>
        <div id="bind_email_tpl" class="bind-email">
            <form>
                <label>更换绑定邮箱</label>
                <input type="text" name="email_addr" class="email-info" value="新邮箱地址" onfocus="if(this.value==this.defaultValue){this.value=''}" onblur="if(!this.value){this.value=this.defaultValue;}">
                <input type="text" class="email-pd txt_passwd" value="糗事百科的密码" onfocus="$(this).hide().next().show().focus();">
                <input type="password" name="email_passwd" class="email-pd" id="email-sc2" maxlength="30" size="30" onblur="if(this.value==''){$(this).hide().prev().show();}">
                <input type="button" value="下一步" class="next-st" action_bind_email="" style="color:#fff; font-weight: bold; border: none;width: 95px; line-height: 34px; text-align: center; height: 34px; padding-left: 0px;">
            </form>
        </div>
        <div id="unbind_tpl" class="bind-email" style="">
            <form onsubmit="return false">
                <label>解除绑定</label>
                <input class="email-pd" placeholder="糗事百科的密码" type="password">
                <a class="next-st pop_btn" action_unbind="" rel="external nofollow">解除绑定</a>
            </form>
        </div>
        <div id="email_sended_tpl" class="bind-email">
            <form>
                <p>验证邮箱已发到邮箱<span email=""></span>请前往邮箱收取，完成绑定</p>
                <a class="next-st pop_btn" action_go_verify="">去验证</a>
            </form>
        </div>
        <div id="error_msg" class="bind-email">
            <span class="error-tips"> </span>
        </div>
        <!-- popup End -->
        <script async="" src="https://www.google-analytics.com/analytics.js"></script><script type="text/javascript" src="http://static.qiushibaike.com/js/src/libs/jquery-1.8.2.min.js?v=cfa9051cc0b05eb519f1e16b2a6645d7"></script>
        <script type="text/javascript" src="http://static.qiushibaike.com/js/src/web/my_edit.js?v=e049ac48a8b433f1c481ea7d10df5d94"></script>

    </div>
    <div class="user-col-left">
        <div class="user-statis user-block">
            <h3>糗百指数</h3>
            <ul>
                <li><span>粉丝数:</span>0</li>
                <li><span>关注数:</span>0</li>
                <li><span>糗事:</span>0</li>
                <li><span>评论:</span>0</li>
                <li><span>笑脸:</span>0</li>
                <li><span>糗事精选:</span>0</li>
            </ul>
        </div>
        <div class="user-statis user-block">
            <h3>个人资料</h3>
            <ul>
                <li><span>婚姻:</span></li>
                <li><span>星座:</span></li>
                <li><span>职业:</span></li>
                <li><span>故乡:</span></li>
                <li><span>糗龄:</span>485天</li>
            </ul>
        </div>
    </div>
</div>