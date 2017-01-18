<div class="user-main clearfix">
    <div class="user-header">
        <a v-link="{name:'',params:{'user_id':userData.user_id}}" class="user-header-avatar">
            <img v-bind:src="userData.user_photo" alt="用户头像">
        </a>
        <div class="user-header-cover">
            <h2>{{userData.user_name}}</h2>
        </div>
        <ul class="user-header-menu">
            <li>
                <a  class="active">主页</a>
            </li>
            <li>
                <a href="/users/29459066/articles/">糗事</a>
            </li>
            <li>
                <a href="/users/29459066/comments/">评论</a>
            </li>
            <li>
                <a @click="changeType(2)">设置</a>
            </li>
        </ul>
    </div>
    <div class="user-col-right" v-show="pageType == 1">
        <div class="user-block user-feed">
            <div class="user-date">
                <span class="user-date-month">
                11
                </span>
                <span class="user-date-break">
                /
                </span>
                <span class="user-date-day">
                12
                </span>
            </div>
            <ul class="user-indent">
                <li class="user-comment-info">
                    <strong>这个名没有注册过</strong>
                    评论了
                    <strong>这个名没有注册过</strong>
                    发表的糗事
                </li>
                <li class="user-comment-text">
                    回复 70楼：我都有盒子装回去的，有自己的房间的
                </li>
                <li class="user-comment-quote">
                    <ul>
                        <li class="user-article-avatar">
                            <a href="/users/26861602/" rel="nofollow">
                            <img src="http://pic.qiushibaike.com/system/avtnew/2686/26861602/thumb/20150322160702.jpg" alt="这个名没有注册过">
                            </a>
                            <a href="/users/26861602/">
                            这个名没有注册过
                            </a>
                        </li>
                        <li class="user-article-text">
                            <a href="/article/117961121" target="_blank">
                            最近买了一个新手机，就把旧手机扔在一边，没管它。手机每天自己定时开机关机，还准时闹铃，用着仅存的一格电努力辛勤地工作着，突然觉得好感动，觉得自己真残忍。
                            </a>
                        </li>

                        <li class="user-article-stat">
                            1988 好笑 ⋅
                            80 评论 ⋅
                            发表于
                            <a href="/history/772f17ed41cb8d53c3c7e8aa46693a3f/" target="_blank">
                            2016-11-12
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="user-block user-feed">
            <div class="user-date">
                <span class="user-date-month">10</span>
                <span class="user-date-break">/</span>
                <span class="user-date-day">13</span>
            </div>
            <ul class="user-indent">
                <li class="user-comment-info">
                <strong>
                这个名没有注册过
                </strong>
                发表了糗事
                </li>
                <li class="user-article-text">
                <a href="/article/117741876" target="_blank">
                老婆怀了二胎，宝宝胎动得厉害。晚上老婆睡不着，忍不住嘀咕了一句：“你说这孩子在里面干啥呢？一直没停！”老公想了想，回道：“可能因为是二手房，现在正忙着装修吧！”
                </a>
                </li>

                <li class="user-article-stat">
                1699 好笑 ⋅
                32 评论 ⋅
                发表于
                <a href="/history/c79391d5e2f856d66059cc5b83129098/" target="_blank">
                2016-10-13
                </a>
                </li>
            </ul>
        </div>

        <div class="user-block user-feed">
            <div class="user-date">
                <span class="user-date-month">
                11
                </span>
                <span class="user-date-break">
                /
                </span>
                <span class="user-date-day">
                12
                </span>
            </div>
            <ul class="user-indent">
                <li class="user-comment-info">
                    <strong>这个名没有注册过</strong>
                    评论了
                    <strong>这个名没有注册过</strong>
                    发表的糗事
                </li>
                <li class="user-comment-text">
                    回复 70楼：我都有盒子装回去的，有自己的房间的
                </li>
                <li class="user-comment-quote">
                    <ul>
                        <li class="user-article-avatar">
                            <a href="/users/26861602/" rel="nofollow">
                            <img src="http://pic.qiushibaike.com/system/avtnew/2686/26861602/thumb/20150322160702.jpg" alt="这个名没有注册过">
                            </a>
                            <a href="/users/26861602/">
                            这个名没有注册过
                            </a>
                        </li>
                        <li class="user-article-text">
                            <a href="/article/117961121" target="_blank">
                            最近买了一个新手机，就把旧手机扔在一边，没管它。手机每天自己定时开机关机，还准时闹铃，用着仅存的一格电努力辛勤地工作着，突然觉得好感动，觉得自己真残忍。
                            </a>
                        </li>

                        <li class="user-article-stat">
                            1988 好笑 ⋅
                            80 评论 ⋅
                            发表于
                            <a href="/history/772f17ed41cb8d53c3c7e8aa46693a3f/" target="_blank">
                            2016-11-12
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="user-col-right" v-show="pageType == 2">

        <div id="editInfo" class="user-block user-setting clearfix">
            <h3>更换头像</h3>
            <form>
                <ul>
                    <li>
                        <img alt="{{userData.user_name}}" class="user-setting-avatar" v-bind:src="userData.user_photo">
                    </li>
                    <li>
                        <input id="user_avatar"  size="30" type="file">
                        <input id="user_submit"  type="button" value="确定上传" @click="changePhoto()">
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
            <h3>修改用户名</h3>
            <form id="new_user">
                <ul>
                    <li>
                        <label for="old_password" class="user-setting-inputlable">请输入用户名</label>
                        <input size="30" type="text" v-model="newUname">
                    </li>                   
                    <li>
                        <input  type="button" value="确认修改" @click="changeUname()">
                    </li>
                </ul>
            </form>
        </div>
        <div id="editPass" class="user-block user-setting clearfix">
            <h3>修改密码</h3>
            <form id="new_user">
                <ul>
                    <li>
                        <label for="old_password" class="user-setting-inputlable">当前密码</label>
                        <input id="old_password" name="old_password" size="30" type="password" v-model="pwdData.originPwd">
                    </li>
                    <li>
                        <label for="new_password" class="user-setting-inputlable">新密码</label>
                        <input id="new_password" name="password" size="30" type="password" v-model="pwdData.newPwd">
                    </li>
                    <li>
                        <label for="password_confirmation" class="user-setting-inputlable">重复新密码</label>
                        <input id="password_confirmation" name="password_confirmation" size="30" type="password" v-model="pwdData.againPwd">
                    </li>
                    <li>
                        <input  type="button" value="确认修改" @click="changePwd()">
                    </li>
                </ul>
            </form>
        </div>
        <div class="user-block user-setting clearfix">
            <h3>帐号</h3>
            <ul>
                <li>
                    <a @click="logout()" class="exit" rel="nofollow">退出登录</a>
                </li>
            </ul>
        </div>
        <!-- popup Start -->
<!--         <div id="bg" class="mask" style="height: 1677px;"></div>
        <div id="popDiv" class="bind-email"></div> -->
        <div id="bind_email_tpl" class="bind-email" style="display: none;">
            <form>
                <label>更换绑定邮箱</label>
                <input type="text" name="email_addr" class="email-info" value="新邮箱地址" onfocus="if(this.value==this.defaultValue){this.value=''}" onblur="if(!this.value){this.value=this.defaultValue;}">
                <input type="text" class="email-pd txt_passwd" value="糗事百科的密码" onfocus="$(this).hide().next().show().focus();">
                <input type="password" name="email_passwd" class="email-pd" id="email-sc2" maxlength="30" size="30" onblur="if(this.value==''){$(this).hide().prev().show();}">
                <input type="button" value="下一步" class="next-st" action_bind_email="" style="color:#fff; font-weight: bold; border: none;width: 95px; line-height: 34px; text-align: center; height: 34px; padding-left: 0px;">
            </form>
        </div>
        <div id="unbind_tpl" class="bind-email" style="display: none;">
            <form onsubmit="return false">
                <label>解除绑定</label>
                <input class="email-pd" placeholder="糗事百科的密码" type="password">
                <a class="next-st pop_btn" action_unbind="" rel="external nofollow">解除绑定</a>
            </form>
        </div>
        <div id="email_sended_tpl" class="bind-email" style="display: none;">
            <form>
                <p>验证邮箱已发到邮箱<span email=""></span>请前往邮箱收取，完成绑定</p>
                <a class="next-st pop_btn" action_go_verify="">去验证</a>
            </form>
        </div>
        <div id="error_msg" class="bind-email">
            <span class="error-tips"> </span>
        </div>

    </div>
    <div class="user-col-left">
        <div class="user-statis user-block">
            <h3>农趣指数</h3>
            <ul>
                <li><span>糗事:</span>0</li>
                <li><span>评论:</span>0</li>
                <li><span>笑脸:</span>0</li>
                <li><span>糗龄:</span>485天</li>                
            </ul>
        </div>
<!--         <div class="user-statis user-block">
            <h3>个人资料</h3>
            <ul>
                <li><span>婚姻:</span></li>
                <li><span>星座:</span></li>
                <li><span>职业:</span></li>
                <li><span>故乡:</span></li>
            </ul>
        </div> -->
    </div>
</div>