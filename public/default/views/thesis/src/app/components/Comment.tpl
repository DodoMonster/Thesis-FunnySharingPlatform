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
                    <!--                     <li class="comments">
                        <a v-link="{name:'comment',params:{things_id:things.things_id}}" class="voting"><i></i></a>
                    </li> -->
                </ul>
            </div>
            <div class="single-share">
                <a href="" class="share-wechat" title="分享到微信"></a>
                <a href="" class="share-qq" title="分享到QQ"></a>
                <a href="" class="share-qzone" title="分享到空间"></a>
                <a href="" class="share-weibo" title="分享到微博"></a>
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
                        <div class="clearfix">
                            <div class="avatars">
                                <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}" target="_blank">
                                    <img :src="comment.user_photo" alt="用户头像" title="用户头像">
                                </a>
                            </div>
                            <div class="replay">
                                <a v-link="{name:'userHome',params:{'user_id':comment.user_id}}" class="user-login" title="{{comment.user_name}}">{{comment.user_name}}</a>
                                <span class="body">{{comment.content}}</span>
                            </div>
                            <div class="report">{{$index+1}}</div>
                        </div>
                        <ul class="comment-opt clearfix">
                            <li class="comment-time">2017-03-02 17:32:40</li>
                            <li><button class="reply-btn" @click="showReplyBox($event)">回复</button></li>
                        </ul>
                        <div class="reply-input">
                            <input type="text" v-model="reply.content">
                        </div>
                    </div>
                </div>
            </div>
            <pagination :page="page"></pagination>

        </div>
    </div>

</div>