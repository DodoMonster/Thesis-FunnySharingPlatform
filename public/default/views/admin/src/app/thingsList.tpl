<div id="breadcrumb">
	<h4>趣事列表</h4>
	<ol class="breadcrumb no-bg m-b-1">
		<li class="breadcrumb-item">趣事管理</li>
		<li class="breadcrumb-item active">趣事列表</li>
	</ol>
</div>

<div class="box box-block bg-white chenyou-box long-search">		    	    
	<div class="row m-x-0 m-b-0-25">
		<form class="clearfix">
			<button type="button" class="btn pull-xs-right m-l-15 btn-primary" @click="getcommentsList(true)">
				<i class="ti-search"></i> 搜索
			</button>
			<div class="form-inline m-b-1 pull-right">
				<div class="form-group">
					<label for="content">趣事内容：</label>
					<input type="text" class="form-control" placeholder="趣事内容"
					v-model="searchParam.content">
				</div>
			</div>					
		</form>
	</div>
		
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>序号</th>
				<th>趣事id</th>
				<th>趣事内容</th>
				<th>作者</th>
				<th>发布时间</th>
				<th>趣事图片</th>
				<th>点赞数</th>
				<th>踩数</th>
				<th>收藏数</th>
				<th>评论数</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="comment in commentsList">
				<td>{{$index+1}}</td>
				<td>{{comment.comments_id}}</td>
				<td>{{comment.comments_content}}</td>
				<td>{{comment.userInfo.user_name || '--'}}</td>
				<td>{{comment.publish_time || '--'}}</td>
				<td>
					<img :src="comment.comments_image" alt="趣事图片" style="width:5rem;">
				</td>
				<td>{{comment.funny_num || '--'}}</td>
				<td>{{comment.unfunny_num || '--'}}</td>
				<td>{{comment.favorite_num || '--'}}</td>
				<td>{{comment.comment_num || '--'}}</td>
				<td>
					<a @click="deletecomments(comment.comments_id)"
							href="javascript:;" title="删除趣事">
						<i class="fa-lg ti-trash text-danger"></i> 
					</a>
	<!-- 				<a @click="approvalcomments(comment.comments_id)"
						v-if="comment.is_approval == 0"
							href="javascript:;" title="审核趣事">
						<i class="fa-lg ti-zoom-in m-l-15 text-success"></i> 
					</a> -->
				</td>
			</tr>			
		</tbody>
	</table>
	<pagination :page.sync="page"></pagination>
</div>


