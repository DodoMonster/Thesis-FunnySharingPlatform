import { EventEmitter } from 'events';

const store = new EventEmitter();

export default store;

/**
 * 存放页面信息
 */
store.editPwdModalMsg = {
    show : false
},

store.pageData = {
    links: [{
        title: '用户管理',
        icon:'ti-package',
        links:[{
                title: '用户列表',
                url: '/userList',
            }]
    }, {
        title: '趣事管理',
        icon:'ti-game',
        links:[{
                title: '趣事列表',
                url: '/thingsList',
                id:'20001',
                visible:false,
            }]        
    }, {
        title: '系统管理',
        icon:'ti-id-badge',
        links:[{
                title: '管理员列表',
                url: '/adminList',
            }] 
    }, {
        title: '评论管理',
        icon:'ti-comments',
        links:[{
                title: '评论列表',
                url: '/commentList',
            }] 
    }]
};