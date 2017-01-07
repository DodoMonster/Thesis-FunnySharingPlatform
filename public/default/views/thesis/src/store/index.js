import {
    EventEmitter
}
from 'events';
import util from 'common/util.js';
import $ from 'jquery';
import service from 'service/service.js';
const store = new EventEmitter();

export default store;

/**
 * 存放页面信息
 */

store.userInfo = {
    user_id:'',
    user_name:'',
    user_photo:'',
}

store.setUserInfo = () => {
    store.userInfo = JSON.parse(sessionStorage.getItem('userInfo') || '{}');
}

store.clearUserInfo = () => {
    store.userInfo = {
        user_id:'',
        user_name:'',
        user_photo:'',
    };
    sessionStorage.clear();;
}

store.getUserInfo = () => {
    return JSON.parse(sessionStorage.getItem('userInfo') || '{}');
}

store.showLoginForm = false;
store.isLogin = false;

/**
 * 存放页面信息
 */

store.pageData = {   
    setCurrentPath(path) {
        this.currentPath = path;
        store.emit('currentPath-updated');
    },
    setCurrentPageName(name, icon) {
        this.currentPageName = name;
        this.currentPageIcon = icon;
        store.emit('currentPageName-updated');
    },
};

function isEmptyObject(obj) {
  for (var key in obj) {
    return false;
  }
  return true;
}