import {
    EventEmitter
}
from 'events';

import $ from 'jquery';
const util = new EventEmitter();

export default util;

util.ajax = (opt, other) => {
    var defer = $.Deferred();
    if (!opt.dataType) {
        opt.dataType = 'json';
    }
    if (other && other.loading) {
        $('.preloader').css('display', 'block');
    }
    $.ajax(opt).done((result) => {
        if (opt.dataType === 'json' && result.code !== 0) {
            if (result.code === -1) {
                localStorage.setItem('prevUrl', window.location.href);
                window.location.href = '/login.html';
            } else if (result.code === 2) {
                // code = 2 用于比较复杂的报错
                defer.reject(result);
            } else {
                defer.reject(result);
            }
        } else {
            defer.resolve(result);
        }
    }).fail(() => {
        defer.reject({
            msg: '网络出错'
        });
    }).always(() => {
        if (other && other.loading) {
            $('.preloader').css('display', 'none');
        }
    });;

    return defer.promise();
}


/*
	对话框
	data.msg	- 提示信息
	data.title	- 标题
	data.size	-	弹框大小，默认为sm
	data.modal	-	显示的弹框类型	
*/

util.dialog = {
    	show(data) {
            util.removeAllListeners('confirm-dialog').removeAllListeners(
                'cancel-dialog');
            util.emit('show-dialog', data);
            return this;
        },
        alert(data) {
            util.removeAllListeners('confirm-dialog').removeAllListeners(
                'cancel-dialog');
            util.emit('alert-dialog', data);
            return this;
        },
        confirm(fn) {
            util.removeAllListeners('confirm-dialog');
            util.on('confirm-dialog', fn);
        },
        cancel(fn) {
            util.on('cancel-dialog', fn);
            return this;
        },
}