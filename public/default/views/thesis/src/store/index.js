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
store.setUserInfo = () => {
    store.userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}');
}

store.getUserInfo = () => {
    return JSON.parse(localStorage.getItem('userInfo') || '{}');
}

store.showLoginForm = false;



store.setToInsightInfo = (selectList) => {
    selectList=JSON.stringify(selectList);
    localStorage.setItem('searchToInsight',selectList);
};
store.getToInsightInfo = () => {
    return JSON.parse(localStorage.getItem('searchToInsight') || '{}');
};
store.setToSearch = (selectList) => {
    selectList=JSON.stringify(selectList);
    localStorage.setItem('insightToSearch',selectList);
};
store.getToSearch = () => {
    return JSON.parse(localStorage.getItem('insightToSearch') || '{}');
};


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
