import {
	EventEmitter
} from 'events';
import util from '../libs/js/util.js';

const service = new EventEmitter();

export default service;

/**
 * 更改密码
 */
service.resetPwd = (id,data) => {
	return util.ajax({
		url: '/admin/resetOwnPassword',
		type:'POST',
		data:{
			id:id,
			old_pwd:data.originPwd,
			new_pwd:data.pwd,
		}
	});
	
}

/**
 * 获取用户信息
 */
service.getAdminInfo = (data) => {
	return util.ajax({
		url: '/admin/getAdminInfo',
		type:'GET',
	});
	
}

/**
 * 获取用户列表
 */
service.getUserList = (page,uname) => {
	return util.ajax({
		url: '/admin/getUserList',
		type:'GET',
		data:{
			page:page,
			uname:uname
		}
	});
}

/**
 * 重置用户密码
 */
service.resetUserPwd = (data) => {
	return util.ajax({
		url: '/admin/resetUserPwd',
		type:'POST',
		data:{
			uid:data.uid,
			password:data.pwd
		}
	});
}

/**
 * 删除用户
 */
service.deleteUser = (id) => {
	return util.ajax({
		url: '/admin/deleteUser',
		type:'POSt',
		data:{
			uid:id
		}
	});
}

/**
 * 添加用户
 */
service.addUser = (data) => {
	return util.ajax({
		url: '/admin/addUser',
		type:'POSt',
		data:{
			uname:data.user_name,
			password:data.user_password
		}
	});
}

/**
 * 获取管理员列表
 */
service.getAdminList = () => {
	return util.ajax({
		url: '/admin/getAdminList',
		type:'GET',
	});
}

/**
 * 删除管理员
 */
service.deleteAdmin = (id) => {
	return util.ajax({
		url: '/admin/deleteAdmin',
		type:'POST',
		data:{
			uid:id
		}
	});
}

/**
 * 添加或编辑管理员
 */
service.submitAdmin = (flag,data) => {
	if(flag){
		var url = '/admin/editAdmin';
	}else{
		var url = '/admin/addAdmin';
	}
	return util.ajax({
		url: url,
		type:'POST',
		data:{
			uid:data.admin_id,
			uname:data.admin_name,
			password:data.password,
			cellphone:data.cellphone,			
		}
	});
	
}

/**
 * 获取趣事列表
 */
service.getThingsList = (page,content) => {
	return util.ajax({
		url: '/admin/getThingsList',
		type:'GET',
		data:{
			content:content,
			page:page
		}
	});
}

/**
 * 审核趣事
 */
service.approvalThings = (id) => {
	return util.ajax({
		url: '/admin/approvalThings',
		type:'POST',
		data:{
			things_id:id,
		}
	});
}

/**
 * 删除趣事
 */
service.deleteThings = (id) => {
	return util.ajax({
		url: '/admin/deleteThings',
		type:'POST',
		data:{
			things_id:id,
		}
	});
}


/**
 * 获取评论列表
 */
service.getCommentList = (page,content) => {
	return util.ajax({
		url: '/admin/getCommentList',
		type:'GET',
		data:{
			content:content,
			page:page
		}
	});
}

/**
 * 审核评论
 */
service.approvalComment = (id) => {
	return util.ajax({
		url: '/admin/approvalComment',
		type:'POST',
		data:{
			comment_id:id,
		}
	});
}

/**
 * 删除评论
 */
service.deleteComment = (id) => {
	return util.ajax({
		url: '/admin/deleteComment',
		type:'POST',
		data:{
			comment_id:id,
		}
	});
}
