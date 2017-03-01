import {
    EventEmitter
}
from 'events';
import util from '../libs/js/util.js';
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

store.setUserInfo = (userInfo) => {
    try{
        localStorage.setItem('userInfo', JSON.stringify(userInfo));

    }catch(e){
        store.userInfo = {
            user_id:'',
            user_name:'',
            user_photo:'',
        }
    }
}

store.clearUserInfo = () => {
    store.userInfo = {
        user_id:'',
        user_name:'',
        user_photo:'',
    };
    localStorage.clear();;
}

store.getUserInfo = () => {
    try{
        var userInfo = JSON.parse(localStorage.getItem('userInfo'));
        store.userInfo = userInfo;
    }catch(e){
        var userInfo = JSON.parse('{}');
    }
    return userInfo;   
}

store.showLoginForm = false;
store.isLogin = false;

/**
 * 存放页面信息
 */

function isEmptyObject(obj) {
  for (var key in obj) {
    return false;
  }
  return true;
}