<div id="breadcrumb">
	<h4>管理员列表</h4>
	<ol class="breadcrumb no-bg m-b-1">
		<li class="breadcrumb-item">系统管理</li>
		<li class="breadcrumb-item active">管理员列表</li>
</div>

<div class="box box-block bg-white">	
	<button type="button" @click="showEditDialog(true)"
		class="btn btn-outline-primary m-b-0-75 waves-effect waves-light">
		<i class="ti-plus"></i> 添加管理员
	</button>	               
		
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>序号</th>
				<th>用户id</th>
				<th>用户名称</th>
				<th>手机</th>
				<th>注册时间</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="admin in adminList">
				<td>{{$index+1}}</td>
				<td>{{admin.admin_id}}</td>
				<td>{{admin.admin_name}}</td>
				<td>{{admin.cellphone || '--'}}</td>
				<td>{{admin.register_time}}</td>
				<td>			
					<a href="javascript:;" title="编辑" @click="showEditDialog(false,admin)" > 
						<i class="fa-lg ti-pencil-alt"></i>
					</a>
					<a href="javascript:;" title="删除" @click="deleteAdmin(admin.admin_id)" > 
						<i class="fa-lg ti-trash m-l-15 text-danger"></i>
					</a>							
				</td>
			</tr>				
		</tbody>
	</table>

<!-- 	<pagination></pagination> -->
</div>

<!--start 弹窗-->
<modal :modal.sync="adminBoxMsg">
	<div slot="modal-body">
		<form class="p-a-0-75">
			<div class="form-group row">
				<label class="col-xs-3">管理员名称 <i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="text" class="form-control" placeholder="请输入管理员名称"
					v-model="adminData.admin_name">
				</div>								
			</div>
			<div class="form-group row" v-show="!showPwd || !isEditAdmin">
				<label class="col-xs-3">密码<i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="password" class="form-control" placeholder="请输入密码"
					v-model="adminData.password">
				</div>				
			</div> 
			<div class="form-group row" v-show="!showPwd || !isEditAdmin">
				<label class="col-xs-3">重复密码<i class="text-danger font-style-normal">*</i>：</label>
				<div class="col-xs-9">
					<input type="password" class="form-control" placeholder="请重复输入密码"
					v-model="adminData.againPwd">
				</div>				
			</div>
			<div class="form-group row">
				<label class="col-xs-3">手机号码：</label>
				<div class="col-xs-9">
					<input type="text" class="form-control" placeholder="请输入手机号码"
					v-model="adminData.cellphone">
				</div>				
			</div>
		</form>
		<div class="form-group row" >
			<div class="col-xs-3"></div>
			<div class="col-xs-9">
				<button type="button" class="btn btn-primary" 
				v-show="isEditAdmin&&showPwd"
				@click="showResetPwd()">重置密码</button>
				<button type="button" class="btn btn-primary" 
				v-show="isEditAdmin&&!showPwd"
				@click="hideResetPwd()">取消重置密码</button>
			</div>				
		</div>
			
	</div>
	<span slot="modal-footer">
	    <button type="button" class="btn btn-primary" @click="submitAdminData"> 
	    	<i class="ti-check"></i> 提交
	    </button>
	</span>	
</modal>
<!--end 弹窗-->

