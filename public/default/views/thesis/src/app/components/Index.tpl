<div id="content" class="main">
	<div id="content-block" class="clearfix">
		<div class="funny-things clearfix" v-for="things in funnyThingsList">
			<div class="author clearfix">
				<a href="index.php#!/userHome" target="_blank">
					<img :src="things.user_info.user_photo" alt="用户头像">
				</a>
				<a href="index.php#!/userHome" target="_blank"><h2>{{things.user_info.user_name}}</h2></a>
			</div>
			<a href="index.php#!/comment" class="contentHerf">
				<div class="funny-content">
					<p>{{things.things_content}}</p>
				</div>
			</a>
			<div class="thumb" v-if="things.things_image">
				<a href="index.php#!/comment" target="_blank">
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
					<a href="index.php#!/comment">
						<i class="number">{{things.unfunny_num}}</i>
						评论
					</a>
				</span>
			</div>
			<div class="stats-buttons clearfix">
				<ul class="clearfix">
					<li class="up">
						<a href="javascript:;" class="voting" @click="praiseUp(things.things.id)"><i></i></a>
					</li>
					<li class="down">
						<a href="javascript:;" class="voting"  @click="trampDown(things.things.id)><i></i></a>
					</li>
					<li class="comments">
						<a href="" class="voting"><i></i></a>
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


<!-- 
		<div class="funny-things clearfix">
			<div class="author clearfix">
				<a href="index.php#!/userHome" target="_blank"><img src="../../../static/image/usercover.jpg" alt="用户头像"></a>
				<a href="index.php#!/userHome" target="_blank"><h2>挖鼻孔的老虎</h2></a>
			</div>
			<a href="index.php#!/comment" class="contentHerf">
				<div class="funny-content">
					<p>借了老板的大奔去同学聚会。<br>
					   刚停好车就碰到了班花，然后她一晚上粘着我，聚会结束了让我送她回家，还请我进屋坐坐……<br>
					   从她家出来，我手上多了一个三百多块的汽车香水座，她老公推销的。
					</p>
				</div>
			</a>
			<div class="stats">
				<span class="stats-vote">
					<i class="number">4030</i>
					好笑
				</span>
				<span class="stats-comments">
					<i class="dash">·</i>
					<a href="index.php#!/comment">
						<i class="number">110</i>
						评论
					</a>
				</span>
			</div>
			<div class="stats-buttons clearfix">
				<ul class="clearfix">
					<li class="up">
						<a href="javascript:;" class="voting"><i></i></a>
					</li>
					<li class="down">
						<a href="javascript:;" class="voting"><i></i></a>
					</li>
					<li class="comments">
						<a href="javascript:;" class="voting"><i></i></a>
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

		<div class="funny-things clearfix">
			<div class="author clearfix">
				<a href="index.php#!/userHome" target="_blank"><img src="../../../static/image/usercover.jpg" alt="用户头像"></a>
				<a href="index.php#!/userHome" target="_blank"><h2>挖鼻孔的老虎</h2></a>
			</div>
			<a href="index.php#!/comment" class="contentHerf">
				<div class="funny-content">
					<p>借了老板的大奔去同学聚会。<br>
					   刚停好车就碰到了班花，然后她一晚上粘着我，聚会结束了让我送她回家，还请我进屋坐坐……<br>
					   从她家出来，我手上多了一个三百多块的汽车香水座，她老公推销的。
					</p>
				</div>
			</a>
			<div class="thumb">
				<a href="index.php#!/comment" target="_blank">
				<img src="http://pic.qiushibaike.com/system/pictures/11803/118031114/medium/app118031114.jpg" alt="朋友圈都乱了">
				</a>
			</div>
			<div class="stats">
				<span class="stats-vote">
					<i class="number">4030</i>
					好笑
				</span>
				<span class="stats-comments">
					<i class="dash">·</i>
					<a href="index.php#!/comment">
						<i class="number">110</i>
						评论
					</a>
				</span>
			</div>
			<div class="stats-buttons clearfix">
				<ul class="clearfix">
					<li class="up">
						<a href="javascript:;" class="voting"><i></i></a>
					</li>
					<li class="down">
						<a href="javascript:;" class="voting"><i></i></a>
					</li>
					<li class="comments">
						<a href="javascript:;" class="voting"><i></i></a>
					</li>
				</ul>
			</div>
			<div class="single-share">
				<a href="javascript:;" class="share-wechat" title="分享到微信"></a>
				<a href="javascript:;" class="share-qq" title="分享到QQ"></a>
				<a href="javascript:;" class="share-qzone" title="分享到空间"></a>
				<a href="javascript:;" class="share-weibo" title="分享到微博"></a>
			</div>
		</div> -->

	</div>
</div>

