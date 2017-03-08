<div id="breadcrumb">
	<h4>评论列表</h4>
	<ol class="breadcrumb no-bg m-b-1">
		<li class="breadcrumb-item">评论管理</li>
		<li class="breadcrumb-item active">评论列表</li>
	</ol>
</div>

<div class="box box-block bg-white chenyou-box long-search">		    	    
	<div class="row m-x-0 m-b-0-25">
		<form class="clearfix">
			<button type="button" class="btn pull-xs-right m-l-15 btn-primary" @click="getCommentList(true)">
				<i class="ti-search"></i> 搜索
			</button>
			<div class="form-inline m-b-1 pull-right">
				<div class="form-group">
					<label for="content">评论内容：</label>
					<input type="text" class="form-control" placeholder="评论内容"
					v-model="searchParam.content">
				</div>
			</div>					
		</form>
	</div>
		
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>序号</th>
				<th>评论id</th>
				<th>评论内容</th>
				<th>作者</th>
				<th>评论时间</th>
				<th>趣事id</th>
				<th>趣事内容</th>
				<th>趣事图片</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="comment in commentList">
				<td>{{$index+1}}</td>
				<td>{{comment.comment_id}}</td>
				<td>{{comment.content}}</td>
				<td>{{comment.userInfo.user_name || '--'}}</td>
				<td>{{comment.comment_time || '--'}}</td>
				<td>{{comment.things_id || '--'}}</td>				
				<td>{{comment.thingInfo.things_content || '--'}}</td>								
				<td>
					<img :src="comment.thingInfo.things_image" alt="趣事图片" style="width:5rem;">
				</td>
				<td>
					<a @click="deleteComment(comment.comment_id)"
							href="javascript:;" title="删除评论">
						<i class="fa-lg ti-trash text-danger"></i> 
					</a>
			<!-- 		<a @click="approvalcomments(comment.comments_id)"
						v-if="comment.is_approval == 0"
							href="javascript:;" title="审核评论">
						<i class="fa-lg ti-zoom-in m-l-15 text-success"></i> 
					</a> -->
				</td>
			</tr>			
		</tbody>
	</table>
	<pagination :page.sync="page"></pagination>
</div>


