<div id="content" class="main">
	<div id="content-block" class="clearfix">

		<div class="clearfix mt-l mb-l p-xl b-w bs-l">
		<!-- 返回信息 -->

			<!-- 发表表单 -->
			<div class="post-readme wx250 f-r">
				<h3>投稿须知</h3>
				<ol>
					<li>自己的或朋友的糗事，真实有笑点，不含政治、色情、广告、诽谤、歧视等内容。</li>
					<li>糗事经过审核后发表。</li>
					<li>转载请注明出处。</li>
					<li>我已阅读并同意糗事百科的《
						<a href="http://about.qiushibaike.com/agreement.html" target="_blank" rel="external nofollow">用户协议</a>
						》以及《
						<a href="http://about.qiushibaike.com/policy.html" target="_blank" rel="external nofollow">隐私政策</a>
						》
					</li>
				</ol>
			</div>
			<div class="wx600 f-l">
				<form enctype="multipart/form-data" id="new_article" method="post">
					<textarea id="qiushi_text" class="wx600 p-m fs-s b-f-g b-lg bsi-l" name="article[content]" placeholder="分享一件新鲜事..." rows="15" required="required" v-model="publishData.content"></textarea>
					<div class="clearfix mt-r3 mb-m p-m c-lg b-f-g b-lg">
<!-- 						<div class="f-r">
							<input  type="checkbox" v-model="publishData.is_anonymous" >
							匿名投稿
						</div> -->
						<div class="f-l">
							<label>照片:</label>
							<input type="file" id="article_picture" v-model="publishData.img" accept="image/*">
							<!-- <input type="file" id="article_picture" v-model="publishData.img"> -->
						</div>
					</div>
					<div id="length" class="f-r"></div><!--字数统计-->
					<button type="button" class="p-xl ptb-m b-g fs-s c-w br-s bs-l" id="article_submit" name="commit" @click="publishThings()">
					投递
					</button>
				</form>
			</div>

		</div>
	</div>
</div>
