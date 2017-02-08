<div id="content" class="main">
	<div id="content-block" class="clearfix">
		<div class="funny-things clearfix" v-for="things in funnyThingsList">
			<div class="author clearfix">
				<a v-link="{name:'userHome',params:{user_id:things.user_id}}" target="_blank">
					<img :src="things.user_info.user_photo" alt="用户头像">
				</a>
				<a v-link="{name:'userHome',params:{user_id:things.user_id}}" target="_blank"><h2>{{things.user_info.user_name}}</h2></a>
			</div>
			<a v-link="{name:'comment',params:{thing_id:things.comment_param}}" class="contentHerf">
				<div class="funny-content">
					<p>{{things.things_content}}</p>
				</div>
			</a>
			<div class="thumb" v-if="things.things_image">
				<a v-link="{name:'comment',params:{thing_id:things.comment_param}}" target="_blank">
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
					<a v-link="{name:'comment',params:{thing_id:things.comment_param}}">
						<i class="number">{{things.comment_num}}</i>
						评论
					</a>
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
						<a v-link="{name:'comment',params:{thing_id:things.comment_param}}" class="voting"><i></i></a>
					</li>
				</ul>
			</div>
			<div class="single-share">
				<a href="" class="share-wechat" title="分享到微信"></a>
				<a href="" class="share-qq" title="分享到QQ"></a>
				<a href="" class="share-qzone" title="分享到空间"></a>
				<a href="" class="share-weibo" title="分享到微博"></a>
			</div>
		</div>
	</div>
</div>


