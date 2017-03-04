<div class="user-main clearfix">
    <div class="user-header">
        <a v-link="{name:'',params:{'user_id':userData.user_id}}" class="user-header-avatar">
            <img v-bind:src="userData.user_photo" alt="用户头像">
        </a>
        <div class="user-header-cover">
            <h2>{{userData.user_name}}</h2>
        </div>
        <ul class="user-header-menu">
<!--             <li>
                <a  class="active" @click="changeType(1)">主页</a>
            </li> -->
            <li>
                <a class="active" @click="changeType(3)">评论</a>
            </li>
            <li>
                <a @click="changeType(2)">糗事</a>
            </li>            
            <li>
                <a @click="changeType(4)">收藏</a>
            </li>
            <li v-if="isSelf">
                <a @click="changeType(5)">设置</a>
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
        <pagination :page="homePage"></pagination>  
    </div>
    <div class="user-col-right" v-show="pageType == 2">
        <div id="content-block" class="clearfix" style="width: 700px;min-width: 700px">
            <div class="funny-things clearfix" v-for="things in userThing" style="margin-top: 0">
                <div class="author clearfix">
                    <a class="favorite-btn pull-right" @click="favorite(false,things.things_id,$event)">
                        <i class="fa" :class="[things.is_favorite == 1 ? 'fa-heart deep-orange-color' : 'fa-heart-o orange-color']"></i>
                    </a> 
                    <a v-link="{name:'userHome',params:{user_id:userInfo.user_id}}" target="_blank">
                        <img :src="userInfo.user_photo" alt="用户头像">
                    </a>
                    <a class="author-name" v-link="{name:'userHome',params:{user_id:userInfo.user_id}}" target="_blank" style="margin-top: -10px"><h2>{{userInfo.user_name}}</h2></a>
                    <p class="publish-time pull-left">{{things.publish_time}}</p> 
                                      
                </div>
                <a v-link="{name:'comment',query:{thing_id:things.things_id}}" class="contentHerf">
                    <div class="funny-content">
                        <p>{{things.things_content}}</p>
                    </div>
                </a>
                <div class="thumb" v-if="things.things_image">
                    <a v-link="{name:'comment',query:{thing_id:things.things_id}}" target="_blank">
                    <img :src="things.things_image" alt="{{things.things_content}}" style="width: 40%;">
                    </a>
                </div>
                <div class="stats">
                    <span class="stats-vote">
                        <i class="number">{{things.funny_num}}</i>
                        好笑
                    </span>
                    <span class="stats-comments">
                        <i class="dash">·</i>
                        <a v-link="{name:'comment',query:{thing_id:things.things_id}}">
                            <i class="number">{{things.comment_num}}</i>
                            评论
                        </a>
                    </span>
                    <span class="stats-favorite">
                        <i class="dash">·</i>
                        <i class="number">{{things.favorite_num}}</i>
                            收藏
                    </span>
                </div>
                <div class="stats-buttons clearfix">
                    <ul class="clearfix">
                        <li class="up">
                            <a href="javascript:;" class="voting" :class="[things.is_praise == 1? 'voted' : '']" @click="praiseUp(things.things_id,$event)"><i></i></a>
                        </li>
                        <li class="down">
                            <a href="javascript:;" class="voting" :class="[things.is_tramp == 1? 'voted' : '']"  @click="trampDown(things.things_id,$event)"><i></i></a>
                        </li>
                        <li class="comments">
                            <a v-link="{name:'comment',query:{thing_id:things.things_id}}" class="voting"><i></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <pagination :page="thingPage"></pagination>  
    </div>
    <div class="user-col-right" v-show="pageType == 3">
        <div class="user-block user-feed" v-for="comment in userComment">
            <div class="user-date">
                <span class="user-date-month">
                {{comment.month}}
                </span>
                <span class="user-date-break">
                /
                </span>
                <span class="user-date-day">
                {{comment.date}}
                </span>
            </div>
            <ul class="user-indent" v-if="!isSelf">
                <li class="user-comment-info">
                    <strong>{{userInfo.user_name}}</strong>
                    评论了
                    <strong>{{comment.user_name}}</strong>
                    发表的糗事
                </li>
                <li class="user-comment-text">
                    {{comment.content}}
                </li>
                <li class="user-comment-quote">
                    <ul>
                        <li class="user-article-avatar">
                            <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}"  rel="nofollow" target="_blank">
                            <img :src="comment.user_photo" alt="{{comment.user_name}}">
                            </a>
                            <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}" target="_blank">
                            {{comment.user_name}}
                            </a>
                        </li>
                        <li class="user-article-text">
                            <a v-link="{name:'comment',query:{thing_id:comment.things_id}}" target="_blank">
                            {{comment.things_content}}
                            </a>
                        </li>

                        <li class="user-article-stat">
                            {{comment.funny_num}} 好笑 ⋅
                            {{comment.comment_num}} 评论 ⋅
                            发表于
                            <a>
                            {{comment.publish_time}}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="user-indent" v-if="isSelf">
                <li class="user-comment-info">
                    <a v-link="{name:'userHome',params:{'user_id':comment.reply_user}}"><strong>{{comment.reply_user_name}}</strong></a> 回复了你
                </li>
                <li class="user-comment-text">
                    {{comment.reply_content}}
                </li>
                <li class="user-comment-quote">
                    <ul>
                        <li class="user-article-avatar">
                            <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}"  rel="nofollow" target="_blank">
                            <img :src="comment.user_photo" alt="{{comment.user_name}}">
                            </a>
                            <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}" target="_blank">
                            {{comment.user_name}}
                            </a>
                        </li>
                        <li class="user-article-text">
                            <a v-link="{name:'comment',query:{thing_id:comment.things_id}}" target="_blank">
                            {{comment.things_content}}
                            </a>
                        </li>

                        <li class="user-article-stat">
                            {{comment.funny_num}} 好笑 ⋅
                            {{comment.comment_num}} 评论 ⋅
                            发表于
                            <a>
                            {{comment.publish_time}}
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="reply-input clearfix">
                    <span class="reply-user pull-left" style="margin-top: -3px;">
                        <img :src="userInfo.user_photo">
                    </span>
                    <input class="form-control pull-left" type="text" placeholder="请输入您的回复内容..." style="width: 80%;margin-left: 10px;">
                    <button class="confirm-reply-btn pull-right btn btn-primary btn-sm" @click="replyComment($event,comment.reply_user,comment.reply_user_name,comment.comment_id,comment.things_id)">回复</button>
                </li>
            </ul>
        </div>
        <pagination :page="commentPage"></pagination>  
    </div>
    <div class="user-col-right" v-show="pageType == 4">
        <div id="content-block" class="clearfix" style="width: 700px;min-width: 700px">
            <div class="funny-things clearfix" v-for="things in userFavorite" style="margin-top: 0">
                <div class="author clearfix">
                    <a class="favorite-btn pull-right" @click="favorite(true,things.things_id,$event)">
                        <i class="fa" :class="[things.is_favorite == 1 ? 'fa-heart deep-orange-color' : 'fa-heart-o orange-color']"></i>
                    </a> 
                    <a v-link="{name:'userHome',params:{user_id:userInfo.user_id}}" target="_blank">
                        <img :src="userInfo.user_photo" alt="用户头像">
                    </a>
                    <a class="author-name" v-link="{name:'userHome',params:{user_id:userInfo.user_id}}" target="_blank" ><h2>{{userInfo.user_name}}</h2></a>
                    <p class="publish-time pull-left">{{things.publish_time}}</p>                                     
                </div>
                <a v-link="{name:'comment',query:{thing_id:things.things_id}}" class="contentHerf">
                    <div class="funny-content">
                        <p>{{things.things_content}}</p>
                    </div>
                </a>
                <div class="thumb" v-if="things.things_image">
                    <a v-link="{name:'comment',query:{thing_id:things.things_id}}" target="_blank">
                    <img :src="things.things_image" alt="{{things.things_content}}" style="width: 40%;">
                    </a>
                </div>
                <div class="stats">
                    <span class="stats-vote">
                        <i class="number">{{things.funny_num}}</i>
                        好笑
                    </span>
                    <span class="stats-comments">
                        <i class="dash">·</i>
                        <a v-link="{name:'comment',query:{thing_id:things.things_id}}">
                            <i class="number">{{things.comment_num}}</i>
                            评论
                        </a>
                    </span>
                </div>
                <div class="stats-buttons clearfix">
                    <ul class="clearfix">
                        <li class="up">
                            <a href="javascript:;" class="voting" :class="[things.is_praise == 1? 'voted' : '']" @click="praiseUp(things.things_id,$event)"><i></i></a>
                        </li>
                        <li class="down">
                            <a href="javascript:;" class="voting" :class="[things.is_tramp == 1? 'voted' : '']"  @click="trampDown(things.things_id,$event)"><i></i></a>
                        </li>
                        <li class="comments">
                            <a v-link="{name:'comment',query:{thing_id:things.things_id}}" class="voting"><i></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <pagination :page="favoritePage"></pagination>  
    </div>
    <div class="user-col-right" v-show="pageType == 5">

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
       
        <div id="error_msg" class="bind-email">
            <span class="error-tips"> </span>
        </div>

    </div>
    
    <div class="user-col-left">
        <div class="user-statis user-block">
            <h3>农趣指数</h3>
            <ul>
                <li><span>糗事:</span>{{thingPage.totalNum || 0}}</li>
                <li><span>评论:</span>{{userData.comment_num || 0}}</li>
                <li><span>笑脸:</span>{{userData.funny_num || 0}}</li>
                <li><span>收藏:</span>{{userData.favorite_num || 0}}</li>
                <li><span>糗龄:</span>{{Math.ceil(userData.old) || 0}}天</li>                
            </ul>
        </div>
    </div>
</div>