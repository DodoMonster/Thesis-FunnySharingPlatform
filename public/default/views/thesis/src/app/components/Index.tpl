<div id="content" class="main">
	<div id="content-block" class="clearfix">
		<div class="funny-things clearfix" v-for="things in funnyThingsList">
			<div class="author clearfix">
				<a class="favorite-btn pull-right" @click="favorite(things.things_id,$event)">
					<i class="fa" :class="[things.is_favorite == 1 ? 'fa-heart deep-orange-color' : 'fa-heart-o orange-color']"></i>
				</a>
				<a v-link="{name:'userHome',params:{user_id:things.user_id}}" target="_blank">
					<img :src="things.user_photo" alt="用户头像">
				</a>
				<a v-link="{name:'userHome',params:{user_id:things.user_id}}" target="_blank" class="author-name"><h2>{{things.user_name}}</h2></a>
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
						<a href="javascript:;" class="voting" :class="[things.is_praise == 1? 'voted' : '']"@click="praiseUp(things.things_id,$event)"><i></i></a>
					</li>
					<li class="down">
						<a href="javascript:;" class="voting" :class="[things.is_tramp == 1? 'voted' : '']"  @click="trampDown(things.things_id,$event)"><i></i></a>
					</li>
					<li class="comments">
						<a v-link="{name:'comment',query:{thing_id:things.things_id}}" class="voting"><i></i></a>
					</li>
				</ul>
			</div>
<!-- 			<div class="single-share jiathis_style_32x32">
				<a class="share-wechat jiathis_button_weixin" title="分享到微信"></a>
				<a class="share-qq jiathis_button_tqq" title="分享到QQ"></a>
				<a class="share-qzone jiathis_button_qzone" title="分享到空间"></a>
				<a class="share-weibo jiathis_button_tsina" title="分享到微博"></a>
			</div> -->
		</div>
		<pagination :page="page"></pagination>
	</div>
</div>


