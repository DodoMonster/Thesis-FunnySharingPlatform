<div id="breadcrumb">
	<h4>用户列表</h4>
	<ol class="breadcrumb no-bg m-b-1">
		<li class="breadcrumb-item">用户管理</li>
		<li class="breadcrumb-item active">用户列表</li>
	</ol>
</div>

<div class="box box-block bg-white chenyou-box long-search">		    	    
	<div class="row m-x-0 m-b-0-25">
		<button type="button" @click="showAddDialog()"
		class="btn btn-outline-primary pull-left m-b-0-75">
			<i class="ti-plus"></i> 添加用户
		</button>
		<div class="form-inline m-b-1 pull-right">
			<div class="form-group">
				<label for="user_name">用户名称：</label>
				<input type="text" class="form-control input-sm" id="user_name" placeholder="用户名称"
				v-model="searchParam.uname">
			</div>
			<button type="button" class="btn m-l-0-5 btn-primary"@click="getUserList(true)">
				<i class="ti-search"></i> 搜索
			</button>
		</div>
	</div>
		
	
	<!-- <div class="table-responsive"> -->
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>序号</th>
					<th>用户id</th>
					<th>用户名称</th>
					<th>注册时间</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="user in userList">
					<td>{{$index+1}}</td>
					<td>{{user.user_id}}</td>
					<td>{{user.user_name}}</td>
					<td>{{user.register_time || '--'}}</td>
				<!-- 	<td>
						<span>{{user.state || '--'}}</span>
						<a v-show="user.state" @click="showEditDiv($event)"
					 		href="javascript:;" title="编辑" class="pull-xs-right"> 
							<i class="fa-lg ti-pencil-alt"></i>
						</a>
						<form class="form-inline overflow-hidden pull-left state-form">
							<button type="button" @click="closeEditDiv($event)"
								class="btn btn-sm pull-xs-right btn-secondary waves-effect waves-light">
								<i class="ti-close"></i>
							</button>
							<button type="button" class="btn pull-xs-right btn-primary btn-sm waves-effect waves-light" @click="editState(user.uid,$event)">
								<i class="ti-check"></i>
							</button>
							<div class="form-group pull-xs-right">
								<select id="state_select">
									<option value="0">正常</option>
									<option value="1">禁封</option>
								</select>
							</div>			
							
						</form>												
					</td> -->
					<td>
						<button type="button"  @click="showResetBox(user.uid)"
							class="btn btn-sm btn-primary btn-rounded">重置密码</button>
						<a @click="deleteUser(user.user_id)"
							href="javascript:;" title="删除用户">
							<i class="m-l-15 fa-lg ti-trash text-danger"></i> 
						</a>
					</td>
				</tr>			
			</tbody>
		</table>
	<!-- <div> -->

	<pagination :page.sync="page"></pagination>
</div>


<!--start 重置密码弹窗-->
<modal :modal.sync="modalMsg">
	<div slot="modal-body">
		<form class="p-a-0-75">
			<div class="form-group row">
				<label class="col-xs-3">密码<i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="password" class="form-control" placeholder="请输入密码"
					v-model="resetPwdData.pwd">
				</div>				
			</div>
			<div class="form-group row">
				<label class="col-xs-3">重复密码<i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="password" class="form-control" placeholder="请重复输入密码"
					v-model="resetPwdData.againPwd">
				</div>				
			</div>			
		</form>
	</div>
	<span slot="modal-footer">
	    <button type="button" class="btn btn-primary" @click="resetPwd"><i class="ti-back-right"></i>重置</button>
	</span>	
</modal>
<!--end 重置密码弹窗-->

<!--start 添加用户弹窗-->
<modal :modal.sync="addDialog">
	<div slot="modal-body">
		<form class="p-a-0-75">
			<div class="form-group row">
				<label class="col-xs-3">用户名<i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="text" class="form-control" placeholder="请输入用户名"
					v-model="addData.user_name">
				</div>				
			</div>
			<div class="form-group row">
				<label class="col-xs-3">密码<i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="password" class="form-control" placeholder="请输入密码"
					v-model="addData.user_password">
				</div>				
			</div>
			<div class="form-group row">
				<label class="col-xs-3">重复密码<i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="password" class="form-control" placeholder="请重复输入密码"
					v-model="addData.user_againtPwd">
				</div>				
			</div>			
		</form>
	</div>
	<span slot="modal-footer">
	    <button type="button" class="btn btn-primary" @click="addUser()"><i class="ti-back-right"></i> 提交</button>
	</span>	
</modal>
<!--end 添加用户弹窗-->
