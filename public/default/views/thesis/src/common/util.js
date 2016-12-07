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
        $('#nvwa_backdrop_loading').css('display', 'block');
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
            $('#nvwa_backdrop_loading').css('display', 'none');
        }
    });;

    return defer.promise();
}


/**
 * 对话框
     data.msg 为提示信息，是个数组，没个元素占一行。^M
     data.title 为标题^M
     data.size 弹框大小，xs为大-用于提示信息比较多，sm为小-用于简单的提示
     data.model 为显示的格式，text 为纯文本，tip为黑色背景小长方形，form... 为带input的格式 。^M
     强烈建议不用这个组件，因为重用性太低^M
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
        }
}


/**
 * [nodeList 递归遍历标签树生成节点数组]
 * @param  {[type]} msg      [标签数据源]
 * @param  {[type]} list     [要返回的标签数组]
 * @param  {[type]} showLeaf [是否显示叶子标签]
 * @param  {[type]} parentId [父ID]
 * @return {[type]} list     [返回生成的节点数组]
 */
util.nodeList = (msg, dataList, showLeaf, parentId) => {
    var nodes = msg.nodes; //标签的一级标签
    var leafs = msg.leaf; //标签的叶子标签
    var isRoot = !msg.type; //根节点没有tyep，借此判断是否为根节点
    var parentId = parentId || 0; //父id
    var jobs = msg.jobs; //叶子标签的作业标签
    var hasNodeOrLeaf = (typeof msg.nodes !== 'undefined') || (typeof msg.leaf !==
        'undefined');
    // console.log(JSON.stringify(nodes));

    if (!msg.infoCompeleted) { //信息完整没有写则表示是完整的
        msg.infoCompeleted = ['完整'];
    }
    if (!nodes && !leafs && !msg.isLeaf) { //信息完整没有写则表示是完整的
        msg.infoCompeleted = ['未添加叶子标签'];
    }

    if (!jobs || msg.isLeaf) {
        switch (msg.type) {
            case 'CLASS':
                msg.category = '类别';
                break;
            case 'SIGN':
                msg.category = '标记';
                break;
            case 'RAW':
                msg.category = '原始值';
                break;
            case 'DYNC':
                msg.category = '动态子标记';
                break;
            default:
                break;
        }

        dataList.labelList.push({
            id: msg.id,
            name: msg.name,
            type: msg.type,
            desc: msg.desc,
            status: msg.status,
            category: msg.category,
            infoCompeleted: msg.infoCompeleted,
            createTime: msg.createTime,
            effectTime: msg.effectTime,
            level: msg.level,
            isLeaf: msg.isLeaf,
            isOnline: msg.isOnline,
            lastUpdateTime: msg.lastUpdateTime,
            isAlive: msg.isAlive,
            parentId: parentId + '----' + msg.id,
            hasNodeOrLeaf: hasNodeOrLeaf
        });
    }
    //显示遍历节点标签
    if (nodes) {
        nodes.forEach(function(item) {
            if (item.isLeaf && !showLeaf) { //叶子标签是一级标签

            } else {
                util.nodeList(item, dataList, showLeaf, parentId +
                    '----' +
                    msg.id);
            }
        });
    }

    //显示遍历叶子标签
    if (showLeaf && leafs) {
        leafs.forEach(function(item) {
            util.nodeList(item, dataList, showLeaf, parentId +
                '----' +
                msg.id);
        });
    }
    if (jobs) {
        jobs.forEach(function(item) {
            dataList.jobs.push({
                manager: item.manager,
                jobId: item.jobId,
                lastSuccessTime: item.lastSuccessTime,
                lastUpdateTime: item.lastUpdateTime,
                projName: item.projName,
                lastRunStatus: item.lastRunStatus,
                jobName: item.jobName,
                parentId: msg.id,
                lastRunTime: item.lastRunTime
            });
        });
    }
    return dataList;
};

/**
 * [toggleFolder 点击收起/展开标签详细
 * @param  {[type]} pId       [点击的标签的所有父标签]
 * @param  {[type]} className [点击图标的类名]
 * @param  {[type]} list      [所有标签数组]
 * @param  {[type]} index     [点击的当前标签在数组中的索引下标]
 * @return {[boolean]}        [true:收起，false:展开]
 */
util.toggleFolder = (event, labelId, list, index, level) => {
    var el = event.srcElement ? event.srcElement : event.target; //获取点击的对象
    var className = $(el).attr('class'); //获取点击的标签的类名
    if (className.indexOf('fa-minus-square-o') >= 0) { //如果标签明细是展开，则往上收起点击的标签的子标签
        while (list[++index]) {
            let parentId = list[index].parentId.split('----');
            if (parentId[level + 1] == labelId) {
                $("#label_" + list[index].id).hide();
            } else { //下一个已经是不相等的时候就退出循环
                break;
            }
        }
        $(el).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
    } else { //标签是收起的则往下展开标签
        while (list[++index]) {
            let parentId = list[index].parentId.split('----');
            if (parentId[level + 1] == labelId) {
                $("#label_" + list[index].id).show();
            } else { //下一个已经是不相等的时候就退出循环
                break;
            }

        }
        $(el).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
    }
};


/**
 * [builTree 生成标签树函数]
 * @param  {[type]} data  [要生成的标签树数据源]
 * @param  {[type]} $tree [要生成的标签树]
 * @return {[type]}       [description]
 */
util.buildTree = ({
    data, $tree, pLabelId
}) => {
    var div = $('<div class="tab-div">');
    var elseUl = $tree.find('div');

    elseUl.remove();
    var nodes = data.nodes; //节点标签
    var leafs = data.leaf; //叶子标签

    //点击一级标签，显示二级标签
    if (nodes) {
        let nodeLen = nodes.length;
        let data = {};
        nodes.forEach(function(node, index) {
            if (!node.isLeaf) { //非叶子标签
                $tree.append(div);
                var span = $(
                    '<span class="tab-span forbid-select"></span>'
                );
                let nodeId = node.id + '--' + node.level;
                span.text(node.name);
                span.attr('id', nodeId);
                span.data('msg', node); //设置li的数据
                div.append(span);
                if (pLabelId) { //编辑标签显示一系列父标签
                    let len = pLabelId.length;
                    if (len === 0) {
                        util.parentName = '根目录'
                    } else if (pLabelId[node.level - 1] == node
                        .id) {
                        data = node;
                        util.parentName = span.text();
                        span.addClass('tab-li-select');
                    }
                    if (len - node.level >= 0 && nodeLen - 1 ==
                        index) { //当父标签不是根目录以及上一级标签已经遍历完后
                        util.buildTree({
                            data: data,
                            $tree: span.parent(
                                'div'),
                            pLabelId: pLabelId
                        });
                    }
                }
                span.on('click', function() {
                    var $self = $(this);

                    $self.addClass('tab-li-select')
                        .siblings('span').removeClass(
                            'tab-li-select');

                    util.buildTree({
                        data: node,
                        $tree: $self.parent(
                            'div')
                    });

                    // // 已经选择，关闭下一级标签
                    // if ($self.hasClass('tab-li-select')) {
                    //     $self.removeClass(
                    //             'tab-li-select').parent()
                    //         .find('div').remove();
                    // }
                    // //没有选择则显示下一级标签
                    // else {
                    //     $self.addClass('tab-li-select')
                    //         .siblings('span').removeClass(
                    //             'tab-li-select');
                    //
                    //     util.buildTree({
                    //         data: node,
                    //         $tree: $self.parent(
                    //             'div')
                    //     });
                    // }
                });
            }
        });
    }
};

/**
 * 构造一颗标签树
 * @param  {Object} options.data            树形结构数据
 * @param  {[type]} options.$tree           构件树的jquery对象
 * @param  {[type]} options.dataList        树形结构数据处理成数组结构
 * @param  {[type]} options.hideLeaf        是否隐藏叶子节点
 * @param  {[type]} options.hasCheckbox     是否有勾选框
 * @param  {[type]} options.layer           当前节点在树的第几层
 * @return {[type]}                         $tree
 * 不懂找jiabin
 */
util.newTree = ({
    data, $tree, dataList, hideLeaf, hasCheckbox, layer, showLayer
}) => {
    let list = data.nodes;
    let leafs = data.leaf;
    let $parent = $tree.closest('.tree-folder').parent();
    let checkboxHtml = hasCheckbox ?
        '<input type="checkbox" class="tree-checkbox" />' : '';
    let _layer = layer || 0;
    let treeContentDisplay = _layer >= showLayer ? 'none' : 'block';
    let aceIconClass = _layer >= showLayer ? 'tree-plus' : 'tree-minus';

    if (list) {
        for (let i = 0, max = list.length; i < max; i++) {
            // 可展开
            if (list[i].nodes || (list[i].leaf && !hideLeaf)) {
                let $treeNode = $(
                    '<div class="tree-folder" style="display:block" id="treeNode_' +
                    list[i].id + '">' +
                    '<div class="tree-folder-header">' +
                    '<i class="ace-icon ' + aceIconClass + '"></i>' +
                    checkboxHtml + '<div class="tree-folder-name">' +
                    list[
                        i].name + '</div>' + '</div>' + '</div>');
                let $treeContent = $(
                    '<div class="tree-folder-content" style="display:' +
                    treeContentDisplay + '"></div>').appendTo($treeNode);
                $tree.append($treeNode);
                // 当前节点在dataList中的index
                $treeNode.data('dataIndex', dataList.length);
                dataList.push({
                    id: list[i].id,
                    name: list[i].name,
                    type: list[i].type,
                    level: list[i].level,
                    layer: _layer,
                    isleaf: false
                });
                $tree = util.newTree({
                    data: list[i],
                    $tree: $treeContent,
                    dataList: dataList,
                    hideLeaf: hideLeaf,
                    hasCheckbox: hasCheckbox,
                    layer: _layer + 1,
                    showLayer: showLayer
                });
                // 当前节点及其子节点的数量
                $treeNode.data('dataLength', dataList.length - $treeNode.data(
                    'dataIndex'))
            } else {
                // 不可展开
                let $node = $(
                    '<div class="tree-item" style="display: block;" id="treeNode_' +
                    list[i].id + '">' + checkboxHtml +
                    '<div class="tree-item-name" data-type=' + list[i].type +
                    ' data-value=' + (list[i].rawValueType ? list[i].rawValueType :
                        'none') +
                    '>' + list[i].name +
                    '</div>' + '</div>');
                dataList.push({
                    id: list[i].id,
                    name: list[i].name,
                    type: list[i].type,
                    level: list[i].level,
                    layer: _layer,
                    isleaf: true
                });
                $tree.append($node);
                $node.data('dataIndex', dataList.length - 1)
                    .data('dataLength', 1);
            }

        }
    }
    if (leafs && !hideLeaf) {
        for (let i = 0, max = leafs.length; i < max; i++) {
            let $leaf = $(
                '<div class="tree-item" style="display:block" id="treeNode_' +
                leafs[i].id + '">' + checkboxHtml +
                '<div class="tree-item-name" data-type=' + leafs[i].type +
                ' data-value=' + (leafs[i].rawValueType ? leafs[i].rawValueType :
                    'none') +
                '>' + leafs[i].name +
                '</div>' +
                '</div>');
            $tree.append($leaf);
            $leaf.data('dataIndex', dataList.length)
                .data('dataLength', 1);
            dataList.push({
                id: leafs[i].id,
                name: leafs[i].name,
                type: leafs[i].type,
                desc: leafs[i].desc,
                createTime: leafs[i].createTime,
                effectTime: leafs[i].effectTime,
                level: 5,
                layer: _layer,
                isleaf: true
            });
        }
    }

    if ($parent.length > 0) {
        return $parent;
    } else {
        return $tree;
    }
};

util.showUserType = (str) => {
    var userType = '';
    switch (str) {
        case 'super_admin':
            userType = '超级管理员';
            break;
        case 'system_admin':
            userType = '系统管理员';
            break;
        case 'group_manager':
            userType = '组负责人';
            break;
        case 'normal_user':
            userType = '普通用户';
            break;
        default:
            break;
    }
    return userType;
};


/**
 * 将选择的包含标签列表转化为后台接口
 * @param  {json数组} selectList [已选择的包含标签列表]
 * @return {json数组的字符串}   [include字段要求的格式]
 */
util.includeJsonToString = (selectList) =>{
    var includeArray=[];
    selectList.map(function(item){
        var labelJson={
            id:item.id,
            type:item.type,
        };
        if (item.type=='SIGN') {
            labelJson.logic='EQ';
            labelJson.value=item.name.labelName;
        }
        else{
            if (item.logic=='EQ'||item.logic=='NE'||item.logic=='IN'
                ||item.logic=='NIN'||item.logic=='CONTAIN'||item.logic=='GT'
                ||item.logic=='LT'||item.logic=='GE'||item.logic=='LE') {
                labelJson.logic=item.logic;
                labelJson.value=item.beginValue;
            }
            else if(item.logic!=''){
                labelJson.logic=item.logic;
                labelJson.value=item.beginValue+','+item.endValue;
            }
        }
        includeArray.push(labelJson);
    });
    return JSON.stringify(includeArray);
};


/**
 * 当从关注人群页面跳转到人群检索和人群洞察时，
 * 将selectCrowd接口返回的include字段的数据（logic、value）添加到已选择的包含标签列表selectList
 * @param  {json数组} selectList [已选择的包含标签列表]
 * @param  {json数组} include    [selectCrowd接口返回的include字段]
 * @return {json数组} 
 */
util.appendInfoToSelect = (selectList,include) =>{
    selectList.map(function(item,index){
        item.logic=include[index].logic||'';
        if (item.logic=='EQ'||item.logic=='NE'||item.logic=='IN'
            ||item.logic=='NIN'||item.logic=='CONTAIN'||item.logic=='GT'
            ||item.logic=='LT'||item.logic=='GE'||item.logic=='LE') {
            item.beginValue=include[index].value||'';
        }
        else if(item.logic!=''){
            item.beginValue = include[index].value.split(',')[0]||'';
            item.endValue = include[index].value.split(',')[1]||'';
        }
    });
    return selectList;
};


util.saveOldDate = (oldArr,newArr) =>{
    newArr.map(function(item,newIndex){
        oldArr.map(function(list){
            if (item.id==list.id) {
                newArr.splice(newIndex,1,list);
                return;
            }
        });
    });
    return newArr;
};



util.getId = ($dom) => {
    var idArray = $dom.map(function(index, dom) {
        return $(dom).parent().prop('id').replace('treeNode_', '')
    });
    return [].join.call(idArray, ',');
};

util.getIdByArray = ($dom) => {
    var jsonArray = [];
    var idArray = $dom.map(function(index, dom) {
        return $(dom).parent().prop('id').replace('treeNode_', '')
    });
    for (var i = 0, len = idArray.length; i < len; i++) {
        jsonArray.push({
            "id": parseInt(idArray[i])
        });
    }
    return jsonArray;
};

util.arrayToObject = (array) => {
    var obj = {};
    array = array || [];
    for (var i = 0, len = array.length; i < len; i++) {
        obj[array[i].split('=')[0]] = array[i].split('=')[1];
    }
    return obj;
};

//数组去重
util.uniqueArr = (arr) => {
    // console.log(JSON.stringify(arr));
    let newArr = [];
    newArr.push(arr[0]);
    for (let i = 1, len = arr.length; i < len; i++) {
        if (newArr.indexOf(arr[i]) === -1) {
            newArr.push(arr[i]);
        }
    }
    // console.log(JSON.stringify(newArr));
    return newArr;
}

//收缩表格行
util.toggleDesc = (event) => {
    var el = event.srcElement ? event.srcElement : event.target;
    if (!$(el).parents('tr').next('tr').hasClass('slideDown')) {
        $(el).removeClass('fa-angle-right').addClass('fa-angle-down').parents(
            'tr').next('tr').addClass('slideDown').slideDown(200);
    } else {
        $(el).removeClass('fa-angle-down').addClass('fa-angle-right').parents(
            'tr').next('tr').removeClass('slideDown').slideUp(200);
    }
}

//把事务流程标号还原为文字
util.maptransFlow = (data) => {
    data.forEach(function(item) {
        switch (item.transFlow) {
            case 1:
                item.flow = '全流程';
                break;
            case 2:
                item.flow = '仅预处理过程';
                break;
            case 3:
                item.flow = '仅打标签过程';
                break;
            case 4:
                item.flow = '仅同步过程';
                break;
            case 5:
                item.flow = '仅预处理和打标签过';
                break;
            case 6:
                item.flow = '打标签和同步过程';
                break;
            default:
                break;
        }
    });
    return data;
}
