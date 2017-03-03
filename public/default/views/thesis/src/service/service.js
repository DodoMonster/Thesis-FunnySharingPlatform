import {
    EventEmitter
}
from 'events';
import util from 'libs/js/util.js';
import $ from 'jquery';

const service = new EventEmitter();

export default service;


/**
 * 注册
 */
service.register = (data) => {
    return util.ajax({
        url: '/thesis/register',
        type:'POST',
        data: {
            username:data.username,
            password:data.password
        }
    });
}

/**
 * 重置密码
 */
service.reset = (data) => {
    return util.ajax({
        url: '/thesis/reset',
        type:'POST',
        data: {
            username:data.username,
            password:data.password
        }
    });
}

/**
 * 登录
 */
service.login = (data) => {
    return util.ajax({
        url: '/thesis/login',
        type:'POST',
        data: {
            username:data.username,
            password:data.password
        }
    });
}

/**
 * 登出
 */
service.logout = () => {
    return util.ajax({
        url: '/thesis/opLogout',
        type:'GET',
    });
}

/**
 * 获取趣事列表
 */
service.getFunnyThingsList = (page,user_id) => {
    return util.ajax({
        url: '/thesis/getFunnyThingsList',
        data: {
            page:page,
            user_id:user_id
        }
    });
}

/**
 * 点赞
 */
service.praiseUp = (id,user_id) => {
    return util.ajax({
        url: '/thesis/praiseUp',
        type:'POST',        
        data: {
            things_id: id,
            user_id:user_id
        }
    });
}

/**
 * 踩
 */
service.trampDown = (id,user_id) => {
    return util.ajax({
        url: '/thesis/trampDown',
        type:'POST',        
        data: {
            things_id: id,
            user_id:user_id
        }
    });
}

/**
 * 收藏
 */
service.favorite = (id,user_id) => {
    return util.ajax({
        url: '/thesis/favorite',
        type:'POST',        
        data: {
            things_id: id,
            user_id:user_id
        }
    });
}

/**
 * 取消收藏
 */
service.cancelFavorite = (id,user_id) => {
    return util.ajax({
        url: '/thesis/cancelFavorite',
        type:'POST',        
        data: {
            things_id: id,
            user_id:user_id
        }
    });
}

/**
 * 获取趣事详情以及评论
 */
service.getFunnyThingsDetail = (id) => {
    return util.ajax({
        url: '/thesis/getFunnyThingsDetail',
        data: {
            things_id: id,
        }
    });
}

/**
 * 获取趣事详情
 */
service.getFunnyThingsDetail = (data) => {
    return util.ajax({
        url: '/thesis/getFunnyThingsDetail',
        type:'POST',
        data: {
            user_id: data.uid,
            things_id:data.thingsId,
            comment:data.comment,
        }
    });
}

/**
 * 获取个人详细信息
 */
service.getUserInfo = (id) => {
    return util.ajax({
        url: '/thesis/getUserInfo',
        data: {
            user_id: id,
        }
    });
}

/**
 * 修改密码
 */
service.changePwd = (id,data) => {
    return util.ajax({
        url: '/thesis/changePwd',
        type:'POST',
        data: {
            user_id:id,
            originPwd:data.originPwd,
            password: data.newPwd,
        }
    });
}

/**
 * 修改用户名
 */
service.changeUname = (id,data) => {
    return util.ajax({
        url: '/thesis/changeUname',
        type:'POST',
        data: {
            user_id:id,
            uname:data
        }
    });
}

/**
 * 获取我的收藏
 */
service.getMyFavorite = (uid) => {
    return util.ajax({
        url: '/thesis/getMyFavorite',
        data: {
            user_id: uid,
        }
    });
}


/**
 * 获取我发表的趣事
 */
service.getUserThing = (uid,page,other) => {
    return util.ajax({
        url: '/thesis/getUserThing',
        data: {
            user_id: uid,
            page:page,
            other_user:other
        }
    });
}

/**
 * 获取我发表的评论
 */
service.getUserComment = (uid,page) => {
    return util.ajax({
        url: '/thesis/getUserComment',
        data: {
            user_id: uid,
            page:page
        }
    });
}


/**
 * 获取我的收藏
 */
service.getUserFavorite = (uid,page,other) => {
    return util.ajax({
        url: '/thesis/getUserFavorite',
        data: {
            user_id: uid,
            page:page,
            other_user:other
        }
    });
}

/**
 * 修改资料
 */
service.editMyData = (data) => {
    return util.ajax({
        url: '/thesis/editMyData',
        data: {
            user_id: data.uid,
            user_name:data.uname,
            photo:data.img,
            password:data.pwd,
        }
    });
}

/**
 * 发表趣事
 */
service.sendThings = (data) => {
    return util.ajax({
        url: '/thesis/sendThings',
        data: {
            user_id: data.uid,
            content:data.content,
            photo:data.img,
        }
    });
}

/**
 * 获取趣事信息
 */
service.getThingInfo = (id) => {    
    return util.ajax({
        url: '/thesis/getThingInfo',
        data: {
            thing_id: id
        }
    });
}

/**
 * 获取评论列表
 */
service.getCommentsList = (page,id) => {    
    return util.ajax({
        url: '/thesis/getCommentsList',
        data: {
            page: page,
            thing_id:id
        }
    });
}

/**
 * 评论趣事
 */
service.comment = (id,content,user_id) => {    
    return util.ajax({
        url: '/thesis/comment',
        type:'POST',
        data: {
            content: content,
            thing_id:id,
            user_id:user_id
        }
    });
}

/**
 * 回复评论
 */
 service.replyComment = (user,data) => {    
    return util.ajax({
        url: '/thesis/replyComment',
        type:'POST',
        data: {
            comment_id:data.comment_id,
            reply_user:user.user_id,
            reply_user_name:user.user_name,
            replied_user:data.replied_id,
            replied_user_name:data.replied_name,
            reply_content:data.content
        }
    });
}
