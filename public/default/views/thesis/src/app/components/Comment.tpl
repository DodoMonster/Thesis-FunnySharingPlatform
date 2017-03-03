<div id="content" class="main">
    <div id="content-block" class="clearfix">
        <div class="funny-things clearfix">
            <div class="author clearfix">
                <a class="favorite-btn pull-right" @click="favorite(thingInfo.things_id,$event)">
                    <i class="fa" :class="[is_favorite == 1 ? 'fa-heart deep-orange-color' : 'fa-heart-o orange-color']"></i>
                </a>
                <a v-link="{name:'userHome',params:{user_id:thingInfo.user_id}}" target="_blank"><img :src="thingInfo.userInfo.user_photo" alt="用户头像"></a>
                <a class="author-name" v-link="{name:'userHome',params:{user_id:thingInfo.user_id}}" target="_blank">
                    <h2>{{thingInfo.userInfo.user_name}}</h2>
                </a>
                <p class="publish-time pull-left">{{thingInfo.publish_time}}</p>
            </div>
            <a href="javascript:;" class="contentHerf">
                <div class="funny-content">
                    <p>{{thingInfo.things_content}}</p>
                </div>
            </a>
            <div class="stats">
                <span class="stats-vote">
        			<i class="number">{{thingInfo.funny_num}}</i>
        			好笑
        		</span>
                <span class="stats-comments">
        			<i class="dash">·</i>
        			<a href="">
        				<i class="number">{{thingInfo.comment_num}}</i>
        				评论
        			</a>
        		</span>
                <span class="stats-favorite">
                    <i class="dash">·</i>
                    <a href="">
                        <i class="number">{{thingInfo.favorite_num}}</i>
                        收藏
                    </a>
                </span>
            </div>
            <div class="stats-buttons clearfix">
                <ul class="clearfix">
                    <li class="up">
                        <a href="javascript:;" class="voting" :class="[is_praise == 1? 'voted' : '']" @click="praiseUp($event)"><i></i></a>
                    </li>
                    <li class="down">
                        <a href="javascript:;" class="voting" :class="[is_tramp == 1? 'voted' : '']" @click="trampDown($event)"><i></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="comments-wrap">
            <h3 class="comments-title fs-m">评论（<em id="comments-num">{{thingInfo.comment_num}}</em>）</h3>
            <div class="comments">
                <div class="comment-area">
                    <div id="length" class="comment-limit-tips">
                        <strong>140</strong>
                    </div>
                    <input id="comment-input" class="comment-input" name="comment[content]" autocomplete="off" placeholder="我有话说..." v-model="commentContent" style="overflow: hidden;">
                    <button @click="comment()" type="button" id="comment_submit" class="comment-submit">评论</button>
                </div>
                <div class="comments-list">
                    <div class="comment-block clearfix" v-for="comment in commentsList">
                        <div class="clearfix comment-content">
                            <div class="avatars">
                                <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}" target="_blank">
                                    <img :src="comment.user_photo" alt="用户头像" title="用户头像">
                                </a>
                            </div>
                            <ul class="replay">
                                <li class="pull-left">
                                    <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}" class="user-login" title="{{comment.user_name}}">{{comment.user_name}}</a>
                                </li>
                                <li class="pull-left">
                                    <span class="comment-time">{{comment.comment_time}}</span>                                    
                                </li>
                                <li class="pull-right" v-if="userInfo.user_id">
                                    <button class="reply-btn" @click="showReplyBox($event,comment.user_id,comment.user_name,comment.comment_id)">回复</button>
                                </li>
                            </ul>
                            <p class="body pull-left">{{comment.content}}</p>
                        </div>
                        <div class="reply-content-wrap">
                            <div class="reply-content" v-for="reply in comment.reply">
                                <ul class="replay">
                                    <li class="pull-left">
                                        <a v-link="{name:'userHome',params:{'user_id':reply.reply_user}}" class="user-login">{{reply.reply_user_name}}</a> 回复
                                        <a v-link="{name:'userHome',params:{'user_id':reply.replied_user}}" class="user-login" style="margin-left: 10px;">{{reply.replied_user_name}}</a>
                                    </li>
                                    <li class="pull-left">
                                        <span class="reply-time">{{reply.reply_time}}</span>                                    
                                    </li>
                                    <li class="pull-right">
                                        <a class="reply-btn" @click="showReplyBox($event,reply.reply_user,reply.reply_user_name,comment.comment_id,true)">回复</a>
                                    </li>
                                </ul>
                                <p class="body pull-left">{{reply.reply_content}}</p>
                            </div>                            
                        </div>
                        <div class="reply-input hide">
                            <span class="reply-user">
                                <img :src="userInfo.user_photo">
                            </span>
                            <input type="text" placeholder="请输入您的回复内容...">
                            <button class="confirm-reply-btn btn btn-primary btn-sm" @click="replyComment($event)">提交</button>
                        </div>
                    </div>
                </div>
            </div>
            <pagination :page="page"></pagination>

        </div>
    </div>

</div>