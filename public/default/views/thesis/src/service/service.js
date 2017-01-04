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
        type:'POST',
    });
}

/**
 * 获取趣事列表
 */
service.getFunnyThingsList = (page,type) => {
    return util.ajax({
        url: '/thesis/getFunnyThingsList',
        data: {
            page:page,
            type:type
        }
    });
}

/**
 * 点赞
 */
service.praiseUp = (id) => {
    return util.ajax({
        url: '/thesis/praiseUp',
        data: {
            things_id: id,
        }
    });
}

/**
 * 踩
 */
service.trampDown = (id) => {
    return util.ajax({
        url: '/thesis/trampDown',
        data: {
            things_id: id,
        }
    });
}

/**
 * 收藏
 */
service.favorite = (id) => {
    return util.ajax({
        url: '/thesis/favorite',
        data: {
            things_id: id,
        }
    });
}


/**
 * 收藏
 */
service.favorite = (id) => {
    return util.ajax({
        url: '/thesis/favorite',
        data: {
            things_id: id,
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
 * 发表评论
 */
service.getFunnyThingsDetail = (data) => {
    return util.ajax({
        url: '/thesis/getFunnyThingsDetail',
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
service.getPersonalDetail = (uid) => {
    return util.ajax({
        url: '/thesis/getPersonalDetail',
        data: {
            user_id: uid,
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
 * 获取和我有关评论
 */
service.getMyComment = (uid) => {
    return util.ajax({
        url: '/thesis/getMyComment',
        data: {
            user_id: uid,
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