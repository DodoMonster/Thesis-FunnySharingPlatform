<div class="site-header">
    <nav class="navbar navbar-dark">
        <ul class="nav navbar-nav">
            <li class="nav-item m-r-1 hidden-lg-up">
            <a class="nav-link collapse-button" href="/admin/index">
                <i class="ti-menu"></i>
            </a>
            </li>
        </ul>
        <ul class="nav navbar-nav pull-xs-right">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" data-toggle="dropdown" aria-expanded="false">
                    <i class="ti-user"></i> 管理员信息
                </a>
                <div class="dropdown-menu dropdown-menu-right animated flipInY">
<!--                     <span class="dropdown-item">
                        <i class="fa fa-user"></i> 
                        <em>{{userInfo.admin_id}}</em>
                    </span> -->
                    <span class="dropdown-item">
                        <i class="fa fa-user"></i>
                        <em>{{userInfo.admin_name}}</em>
                    </span>                   
                    <div class="dropdown-divider"></div>
                     <a class="dropdown-item"  @click="showEditPwdModal()">
                        <i class="fa fa-cog m-r-0-5"></i>修改密码
                    </a>
                    <a class="dropdown-item" href="javascript:;"><i class="ti-help m-r-0-5"></i> Help</a>
                </div>
            </li>
            <li class="nav-item">
              <a class="nav-link site-sidebar-second-toggle" @click="logout()" data-toggle="collapse">
                <i class="ti-power-off"></i>
              </a>
            </li>
        </ul>
    </nav>
  </div>

  <modal :modal.sync="editPwdModalMsg">
    <div slot="modal-body">
        <form class="p-a-0-75">
            <div class="form-group row">
                <label class="col-xs-3 reset-password">原密码<i class="text-danger font-style-normal">*</i>：</label>
                <div class="col-xs-9">
                    <input type="password" class="form-control" placeholder="请输入原来的密码"
                    v-model="editPwdData.originPwd">
                </div>              
            </div>
            <div class="form-group row">
                <label class="col-xs-3 edit-password">密码<i class="text-danger font-style-normal">*</i>：</label>
                <div class="col-xs-9">
                    <input type="password" class="form-control" placeholder="请输入密码"
                    v-model="editPwdData.pwd">
                </div>              
            </div>
            <div class="form-group row">
                <label class="col-xs-3">重复密码<i class="text-danger font-style-normal">*</i>：</label>
                <div class="col-xs-9">
                    <input type="password" class="form-control" placeholder="请重复输入密码"
                    v-model="editPwdData.againPwd">
                </div>              
            </div>          
        </form>
    </div>
    <span slot="modal-footer">
        <button type="button" class="btn btn-primary" @click="editPwd"><i class="ti-back-right"></i>修改</button>
    </span> 
</modal>