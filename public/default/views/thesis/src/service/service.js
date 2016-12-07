import {
    EventEmitter
}
from 'events';
import util from 'common/util.js';
import $ from 'jquery';

const service = new EventEmitter();

export default service;

/**
 * 获取趣事列表
 */
service.getFunnyThingsList = (page,type) => {
    return util.ajax({
        url: '/home/getFunnyThingsList',
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
        url: '/home/praiseUp',
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
        url: '/home/trampDown',
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
        url: '/home/favorite',
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
        url: '/home/favorite',
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
        url: '/home/getFunnyThingsDetail',
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
        url: '/home/getFunnyThingsDetail',
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
        url: '/home/getPersonalDetail',
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
        url: '/home/getMyFavorite',
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
        url: '/home/getMyComment',
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
        url: '/home/editMyData',
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
        url: '/home/sendThings',
        data: {
            user_id: data.uid,
            content:data.content,
            photo:data.img,
        }
    });
}