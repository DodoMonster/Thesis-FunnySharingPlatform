
/* start responsible-table */
(function ($) {
    'use strict';

    // RESPONSIVE TABLE CLASS DEFINITION
    // ==========================

    var ResponsiveTable = function(element, options) {
        var that = this;

        this.options = options;
        this.$tableWrapper = null; //defined later in wrapTable
        this.$tableScrollWrapper = $(element); //defined later in wrapTable
        this.$table = $(element).find('table');

        if(this.$table.length !== 1) {
            throw new Error('Exactly one table is expected in a .table-responsive div.');
        }

        //apply pattern option as data-attribute, in case it was set via js
        this.$tableScrollWrapper.attr('data-pattern', this.options.pattern);

        //if the table doesn't have a unique id, give it one.
        //The id will be a random hexadecimal value, prefixed with id.
        //Used for triggers with displayAll button.
        this.id = this.$table.prop('id') || this.$tableScrollWrapper.prop('id') || 'id' + Math.random().toString(16).slice(2);

        this.$tableClone = null; //defined farther down
        this.$stickyTableHeader = null; //defined farther down

        //good to have - for easy access
        this.$thead = this.$table.find('thead');
        this.$tbody = this.$table.find('tbody');
        this.$hdrCells = this.$thead.find('th');
        this.$bodyRows = this.$tbody.find('tr');

        //toolbar and buttons
        this.$btnToolbar = null; //defined farther down
        this.$dropdownGroup = null; //defined farther down
        this.$dropdownBtn = null; //defined farther down
        this.$dropdownContainer = null; //defined farther down

        this.$displayAllBtn = null; //defined farther down

        this.$focusGroup = null; //defined farther down


        //misc
        this.displayAllTrigger = 'display-all-' + this.id + '.responsive-table';
        this.idPrefix = this.id + '-col-';

        // Check if iOS
        // property to save performance
        this.iOS = isIOS();
      
        // Setup table
        // -------------------------
      
        //wrap table
        this.wrapTable();

        //create toolbar with buttons
        this.createButtonToolbar();

        // Setup cells
        // -------------------------

        //setup header cells
        this.setupHdrCells();

        //setup standard cells
        this.setupStandardCells();

        //create sticky table head
        if(this.options.stickyTableHeader){
            this.createStickyTableHeader();
        }

        // hide toggle button if the list is empty
        if(this.$dropdownContainer.is(':empty')){
            this.$dropdownGroup.hide();
        }

        // Event binding
        // -------------------------

        // on orientchange, resize and displayAllBtn-click
        $(window).bind('orientationchange resize ' + this.displayAllTrigger, function(){

            //update the inputs' checked status
            that.$dropdownContainer.find('input').trigger('updateCheck');

            //update colspan and visibility of spanning cells
            $.proxy(that.updateSpanningCells(), that);

        });
    };

    ResponsiveTable.DEFAULTS = {
        pattern: 'priority-columns',
        fixedNavbar: '.navbar-fixed-top',  // Is there a fixed navbar? The stickyTableHeader needs to know about it!
        addDisplayAllBtn: true, // should it have a display-all button?
        i18n: {
            display   : '显示项',
            displayAll: '显示全部'
        }
    };

    // Wrap table
    ResponsiveTable.prototype.wrapTable = function() {        
        this.$tableScrollWrapper.wrap('<div class="table-wrapper"/>');
        this.$tableWrapper = this.$tableScrollWrapper.parent();
    };

    // Create toolbar with buttons
    ResponsiveTable.prototype.createButtonToolbar = function() {
        var that = this;

        this.$btnToolbar = $('<div class="btn-toolbar" />');

        this.$dropdownGroup = $('<div class="btn-group dropdown-btn-group pull-xs-right" />');
        this.$dropdownBtn = $('<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">' + this.options.i18n.display + ' <span class="caret"></span></button>');
        this.$dropdownContainer = $('<ul class="dropdown-menu"/>');

         // Display-all btn
        if(this.options.addDisplayAllBtn) {
            // Create display-all btn
            this.$displayAllBtn = $('<button class="btn btn-secondary">' + this.options.i18n.displayAll + '</button>');
            // Add display-all btn to dropdown-btn-group
            this.$dropdownGroup.append(this.$displayAllBtn);

            if (this.$table.hasClass('display-all')) {
                // add 'btn-primary' class to btn to indicate that display all is activated
                this.$displayAllBtn.addClass('btn-primary');
            }

            // bind click on display-all btn
            this.$displayAllBtn.click(function(){
                $.proxy(that.displayAll(null, true), that);
            });
        }

        //add dropdown btn and menu to dropdown-btn-group
        this.$dropdownGroup.append(this.$dropdownBtn).append(this.$dropdownContainer);

        //add dropdown group to toolbar
        this.$btnToolbar.append(this.$dropdownGroup);

        // add toolbar above table
        this.$tableScrollWrapper.before(this.$btnToolbar);
    };

    

    /**
     * @param activate Forces the displayAll to be active or not. If anything else than bool, it will not force the state so it will toggle as normal.
     * @param trigger Bool to indicate if the displayAllTrigger should be triggered.
     */
    ResponsiveTable.prototype.displayAll = function(activate, trigger) {
        if(this.$displayAllBtn){
            // add 'btn-primary' class to btn to indicate that display all is activated
            this.$displayAllBtn.toggleClass('btn-primary', activate);
        }

        this.$table.toggleClass('display-all', activate);
        if(this.$tableClone){
            this.$tableClone.toggleClass('display-all', activate);
        }

        if(trigger) {
            $(window).trigger(this.displayAllTrigger);
        }
    };

    ResponsiveTable.prototype.preserveDisplayAll = function() {
        var displayProp = 'table-cell';
        if($('html').hasClass('lt-ie9')){
            displayProp = 'inline';
        }

        $(this.$table).find('th, td').css('display', displayProp);
        if(this.$tableClone){
            $(this.$tableClone).find('th, td').css('display', displayProp);
        }
    };

    // Setup header cells
    ResponsiveTable.prototype.setupHdrCells = function() {
        var that = this;

        // for each header column
        that.$hdrCells.each(function(i){
            var $th = $(this),
                id = $th.prop('id'),
                thText = $th.text();

            // assign an id to each header, if none is in the markup
            if (!id) {
                id = that.idPrefix + i;
                $th.prop('id', id);
            }

            if(thText === ''){
                thText = $th.attr('data-col-name');
            }

            // create the hide/show toggle for the current column
            if ( $th.is('[data-priority]') ) {
                var $toggle = $('<li class="checkbox-row"><input type="checkbox" name="toggle-'+id+'" id="toggle-'+id+'" value="'+id+'" /> <label for="toggle-'+id+'">'+ thText +'</label></li>');
                var $checkbox = $toggle.find('input');

                that.$dropdownContainer.append($toggle);

                $toggle.click(function(){
                    // console.log("cliiiick!");
                    $checkbox.prop('checked', !$checkbox.prop('checked'));
                    $checkbox.trigger('change');
                });

                //Freakin' IE fix
                if ($('html').hasClass('lt-ie9')) {
                    $checkbox.click(function() {
                        $(this).trigger('change');
                    });
                }

                $toggle.find('label').click(function(event){
                    event.stopPropagation();
                });

                $toggle.find('input')
                    .click(function(event){
                        event.stopPropagation();
                    })
                .change(function(){ // bind change event on checkbox
                    var $checkbox = $(this),
                        val = $checkbox.val(),
                        //all cells under the column, including the header and its clone
                        $cells = that.$tableWrapper.find('#' + val + ', #' + val + '-clone, [data-columns~='+ val +']');

                    //if display-all is on - save state and carry on
                    if(that.$table.hasClass('display-all')){
                        //save state
                        $.proxy(that.preserveDisplayAll(), that);
                        //remove display all class
                        that.$table.removeClass('display-all');
                        if(that.$tableClone){
                            that.$tableClone.removeClass('display-all');
                        }
                        //switch off button
                        that.$displayAllBtn.removeClass('btn-primary');
                    }

                    // loop through the cells
                    $cells.each(function(){
                        var $cell = $(this);

                        // is the checkbox checked now?
                        if ($checkbox.is(':checked')) {

                            // if the cell was already visible, it means its original colspan was >1
                            // so let's increment the colspan
                            if($cell.css('display') !== 'none'){
                                $cell.prop('colSpan', parseInt($cell.prop('colSpan')) + 1);
                            }

                            // show cell
                            $cell.show();

                        }
                      // checkbox has been unchecked
                      else {
                            // decrement colSpan if it's not 1 (because colSpan should not be 0)
                            if(parseInt($cell.prop('colSpan'))>1){
                                $cell.prop('colSpan', parseInt($cell.prop('colSpan')) - 1);
                            }
                            // otherwise, hide the cell
                            else {
                                $cell.hide();
                            }
                        }
                    });
                })
                .bind('updateCheck', function(){
                    if ( $th.css('display') !== 'none') {
                        $(this).prop('checked', true);
                    }
                    else {
                        $(this).prop('checked', false);
                    }
                })
                .trigger('updateCheck');
            } // end if
        }); // end hdrCells loop 
    };

    // Setup standard cells
    // assign matching "data-columns" attributes to the associated cells "(cells with colspan>1 has multiple columns).
    ResponsiveTable.prototype.setupStandardCells = function() {
        var that = this;

        // for each body rows
        that.$bodyRows.each(function(){
            var idStart = 0;

            // for each cell
            $(this).find('th, td').each(function(){
                var $cell = $(this);
                var columnsAttr = '';

                var colSpan = $cell.prop('colSpan');

                var numOfHidden = 0;
                // loop through columns that the cell spans over
                for (var k = idStart; k < (idStart + colSpan); k++) {
                    // add column id
                    columnsAttr = columnsAttr + ' ' + that.idPrefix + k;

                    // get column header
                    var $colHdr = that.$tableScrollWrapper.find('#' + that.idPrefix + k);

                    // copy data-priority attribute from column header
                    var dataPriority = $colHdr.attr('data-priority');
                    if (dataPriority) { $cell.attr('data-priority', dataPriority); }

                    if($colHdr.css('display')==='none'){
                        numOfHidden++;
                    }

                }

                // if colSpan is more than 1
                if(colSpan > 1) {
                    //give it the class 'spn-cell';
                    $cell.addClass('spn-cell');

                    // if one of the columns that the cell belongs to is visible then show the cell
                    if(numOfHidden !== colSpan){
                        $cell.show();
                    } else {
                        $cell.hide(); //just in case
                    }
                }

                //update colSpan to match number of visible columns that i belongs to
                $cell.prop('colSpan',Math.max((colSpan - numOfHidden),1));

                //remove whitespace in begining of string.
                columnsAttr = columnsAttr.substring(1);

                //set attribute to cell
                $cell.attr('data-columns', columnsAttr);

                //increment idStart with the current cells colSpan.
                idStart = idStart + colSpan;
            });
        });
    };

    // Update colspan and visibility of spanning cells
    ResponsiveTable.prototype.updateSpanningCells = function() {
        var that = this;

        // iterate through cells with class 'spn-cell'
        that.$table.find('.spn-cell').each( function(){
            var $cell = $(this);
            var columnsAttr = $cell.attr('data-columns').split(' ');

            var colSpan = columnsAttr.length;
            var numOfHidden = 0;
            for (var i = 0; i < colSpan; i++) {
                if($('#' + columnsAttr[i]).css('display')==='none'){
                    numOfHidden++;
                }
            }

            // if one of the columns that the cell belongs to is visible then show the cell
            if(numOfHidden !== colSpan){
                $cell.show();
            } else {
                $cell.hide(); //just in case
            }

            // console.log('numOfHidden: ' + numOfHidden);
            // console.log("new colSpan:" +Math.max((colSpan - numOfHidden),1));

            //update colSpan to match number of visible columns that i belongs to
            $cell.prop('colSpan',Math.max((colSpan - numOfHidden),1));
        });
    };

    // RESPONSIVE TABLE PLUGIN DEFINITION
    // ===========================

    var old = $.fn.responsiveTable;

    $.fn.responsiveTable = function (option) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('responsiveTable');
            var options = $.extend({}, ResponsiveTable.DEFAULTS, $this.data(), typeof option === 'object' && option);

            if(options.pattern === '') {
                return;
            }

            if (!data) {
                $this.data('responsiveTable', (data = new ResponsiveTable(this, options)));
            }
            if (typeof option === 'string') {
                data[option]();
            }
        });
    };

    $.fn.responsiveTable.Constructor = ResponsiveTable;


    // RESPONSIVE TABLE NO CONFLICT
    // =====================

    $.fn.responsiveTable.noConflict = function () {
        $.fn.responsiveTable = old;
        return this;
    };

    // RESPONSIVE TABLE DATA-API
    // ==================

    $(document).on('ready.responsive-table.data-api', function () {
        $('[data-pattern]').each(function () {
            var $tableScrollWrapper = $(this);
            $tableScrollWrapper.responsiveTable($tableScrollWrapper.data());
        });
    });


    // DROPDOWN
    // ==========================

    // Prevent dropdown from closing when toggling checkbox
    $(document).on('click.dropdown.data-api', '.dropdown-menu .checkbox-row', function (e) {
        e.stopPropagation();
    });

    // FEATURE DETECTION (instead of Modernizr)
    // ==========================

    // media queries
    function mediaQueriesSupported() {
        return (typeof window.matchMedia !== 'undefined' || typeof window.msMatchMedia !== 'undefined' || typeof window.styleMedia !== 'undefined');
    }

    // touch
    function hasTouch() {
        return 'ontouchstart' in window;
    }

    // Checks if current browser is on IOS.
    function isIOS() {
        return !!(navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i));
    }


    $(document).ready(function() {
        // Change `no-js` to `js`
        $('html').removeClass('no-js').addClass('js');

        // Add mq/no-mq class to html
        if(mediaQueriesSupported()) {
            $('html').addClass('mq');
        } else {
            $('html').addClass('no-mq');
        }

        // Add touch/no-touch class to html
        if(hasTouch()) {
            $('html').addClass('touch');
        } else {
            $('html').addClass('no-touch');
        }
    });
})(jQuery);
/* end responsible-table */


/* start editable-table */

/*global $, window*/
// $.fn.editableTableWidget = function (options) {
//     'use strict';
//     return $(this).each(function () {
//         var buildDefaultOptions = function () {
//                 var opts = $.extend({}, $.fn.editableTableWidget.defaultOptions);
//                 opts.editor = opts.editor.clone();
//                 return opts;
//             },
//             activeOptions = $.extend(buildDefaultOptions(), options),
//             ARROW_LEFT = 37, ARROW_UP = 38, ARROW_RIGHT = 39, ARROW_DOWN = 40, ENTER = 13, ESC = 27, TAB = 9,
//             element = $(this),
//             editor = activeOptions.editor.css('position', 'absolute').hide().appendTo(element.parent()),
//             active,
//             showEditor = function (select) {
//                 active = element.find('td:focus');
//                 if (active.length) {
//                     editor.val(active.text())
//                         .removeClass('error')
//                         .show()
//                         .offset(active.offset())
//                         .css(active.css(activeOptions.cloneProperties))
//                         .width(active.width())
//                         .height(active.height())
//                         .focus();
//                     if (select) {
//                         editor.select();
//                     }
//                 }
//             },
//             setActiveText = function () {
//                 var text = editor.val(),
//                     evt = $.Event('change'),
//                     originalContent;
//                 if (active.text() === text || editor.hasClass('error')) {
//                     return true;
//                 }
//                 originalContent = active.html();
//                 active.text(text).trigger(evt, text);
//                 if (evt.result === false) {
//                     active.html(originalContent);
//                 }
//             },
//             movement = function (element, keycode) {
//                 if (keycode === ARROW_RIGHT) {
//                     return element.next('td');
//                 } else if (keycode === ARROW_LEFT) {
//                     return element.prev('td');
//                 } else if (keycode === ARROW_UP) {
//                     return element.parent().prev().children().eq(element.index());
//                 } else if (keycode === ARROW_DOWN) {
//                     return element.parent().next().children().eq(element.index());
//                 }
//                 return [];
//             };
//         editor.blur(function () {
//             setActiveText();
//             editor.hide();
//         }).keydown(function (e) {
//             if (e.which === ENTER) {
//                 setActiveText();
//                 editor.hide();
//                 active.focus();
//                 e.preventDefault();
//                 e.stopPropagation();
//             } else if (e.which === ESC) {
//                 editor.val(active.text());
//                 e.preventDefault();
//                 e.stopPropagation();
//                 editor.hide();
//                 active.focus();
//             } else if (e.which === TAB) {
//                 active.focus();
//             } else if (this.selectionEnd - this.selectionStart === this.value.length) {
//                 var possibleMove = movement(active, e.which);
//                 if (possibleMove.length > 0) {
//                     possibleMove.focus();
//                     e.preventDefault();
//                     e.stopPropagation();
//                 }
//             }
//         })
//         .on('input paste', function () {
//             var evt = $.Event('validate');
//             active.trigger(evt, editor.val());
//             if (evt.result === false) {
//                 editor.addClass('error');
//             } else {
//                 editor.removeClass('error');
//             }
//         });
//         element.on('click keypress dblclick', showEditor)
//         .css('cursor', 'pointer')
//         .keydown(function (e) {
//             var prevent = true,
//                 possibleMove = movement($(e.target), e.which);
//             if (possibleMove.length > 0) {
//                 possibleMove.focus();
//             } else if (e.which === ENTER) {
//                 showEditor(false);
//             } else if (e.which === 17 || e.which === 91 || e.which === 93) {
//                 showEditor(true);
//                 prevent = false;
//             } else {
//                 prevent = false;
//             }
//             if (prevent) {
//                 e.stopPropagation();
//                 e.preventDefault();
//             }
//         });

//         element.find('td').prop('tabindex', 1);

//         $(window).on('resize', function () {
//             if (editor.is(':visible')) {
//                 editor.offset(active.offset())
//                 .width(active.width())
//                 .height(active.height());
//             }
//         });
//     });

// };
// $.fn.editableTableWidget.defaultOptions = {
//     cloneProperties: ['padding', 'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
//                       'text-align', 'font', 'font-size', 'font-family', 'font-weight',
//                       'border', 'border-top', 'border-bottom', 'border-left', 'border-right'],
//     editor: $('<input>')
// };

/* end editable-table */

/**
 
 @Name : layDate v1.1 日期控件
 @Author: 贤心
 @Date: 2014-06-25
 @QQ群：176047195
 @Site：http://sentsin.com/layui/laydate
 
 */

;!function(a){var b={path:"",defSkin:"default",format:"YYYY-MM-DD",min:"1900-01-01 00:00:00",max:"2099-12-31 23:59:59",isv:!1},c={},d=document,e="createElement",f="getElementById",g="getElementsByTagName",h=["laydate_box","laydate_void","laydate_click","LayDateSkin","skins/","/laydate.css"];a.laydate=function(b){b=b||{};try{h.event=a.event?a.event:laydate.caller.arguments[0]}catch(d){}return c.run(b),laydate},laydate.v="1.1",c.getPath=function(){var a=document.scripts,c=a[a.length-1].src;return b.path?b.path:c.substring(0,c.lastIndexOf("/")+1)}(),c.use=function(a,b){var f=d[e]("link");f.type="text/css",f.rel="stylesheet",f.href=c.getPath+a+h[5],b&&(f.id=b),d[g]("head")[0].appendChild(f),f=null},c.trim=function(a){return a=a||"",a.replace(/^\s|\s$/g,"").replace(/\s+/g," ")},c.digit=function(a){return 10>a?"0"+(0|a):a},c.stopmp=function(b){return b=b||a.event,b.stopPropagation?b.stopPropagation():b.cancelBubble=!0,this},c.each=function(a,b){for(var c=0,d=a.length;d>c&&b(c,a[c])!==!1;c++);},c.hasClass=function(a,b){return a=a||{},new RegExp("\\b"+b+"\\b").test(a.className)},c.addClass=function(a,b){return a=a||{},c.hasClass(a,b)||(a.className+=" "+b),a.className=c.trim(a.className),this},c.removeClass=function(a,b){if(a=a||{},c.hasClass(a,b)){var d=new RegExp("\\b"+b+"\\b");a.className=a.className.replace(d,"")}return this},c.removeCssAttr=function(a,b){var c=a.style;c.removeProperty?c.removeProperty(b):c.removeAttribute(b)},c.shde=function(a,b){a.style.display=b?"none":"block"},c.query=function(a){var e,b,h,i,j;return a=c.trim(a).split(" "),b=d[f](a[0].substr(1)),b?a[1]?/^\./.test(a[1])?(i=a[1].substr(1),j=new RegExp("\\b"+i+"\\b"),e=[],h=d.getElementsByClassName?b.getElementsByClassName(i):b[g]("*"),c.each(h,function(a,b){j.test(b.className)&&e.push(b)}),e[0]?e:""):(e=b[g](a[1]),e[0]?b[g](a[1]):""):b:void 0},c.on=function(b,d,e){return b.attachEvent?b.attachEvent("on"+d,function(){e.call(b,a.even)}):b.addEventListener(d,e,!1),c},c.stopMosup=function(a,b){"mouseup"!==a&&c.on(b,"mouseup",function(a){c.stopmp(a)})},c.run=function(a){var d,e,g,b=c.query,f=h.event;try{g=f.target||f.srcElement||{}}catch(i){g={}}if(d=a.elem?b(a.elem):g,f&&g.tagName){if(!d||d===c.elem)return;c.stopMosup(f.type,d),c.stopmp(f),c.view(d,a),c.reshow()}else e=a.event||"click",c.each((0|d.length)>0?d:[d],function(b,d){c.stopMosup(e,d),c.on(d,e,function(b){c.stopmp(b),d!==c.elem&&(c.view(d,a),c.reshow())})})},c.scroll=function(a){return a=a?"scrollLeft":"scrollTop",d.body[a]|d.documentElement[a]},c.winarea=function(a){return document.documentElement[a?"clientWidth":"clientHeight"]},c.isleap=function(a){return 0===a%4&&0!==a%100||0===a%400},c.checkVoid=function(a,b,d){var e=[];return a=0|a,b=0|b,d=0|d,a<c.mins[0]?e=["y"]:a>c.maxs[0]?e=["y",1]:a>=c.mins[0]&&a<=c.maxs[0]&&(a==c.mins[0]&&(b<c.mins[1]?e=["m"]:b==c.mins[1]&&d<c.mins[2]&&(e=["d"])),a==c.maxs[0]&&(b>c.maxs[1]?e=["m",1]:b==c.maxs[1]&&d>c.maxs[2]&&(e=["d",1]))),e},c.timeVoid=function(a,b){if(c.ymd[1]+1==c.mins[1]&&c.ymd[2]==c.mins[2]){if(0===b&&a<c.mins[3])return 1;if(1===b&&a<c.mins[4])return 1;if(2===b&&a<c.mins[5])return 1}else if(c.ymd[1]+1==c.maxs[1]&&c.ymd[2]==c.maxs[2]){if(0===b&&a>c.maxs[3])return 1;if(1===b&&a>c.maxs[4])return 1;if(2===b&&a>c.maxs[5])return 1}return a>(b?59:23)?1:void 0},c.check=function(){var a=c.options.format.replace(/YYYY|MM|DD|hh|mm|ss/g,"\\d+\\").replace(/\\$/g,""),b=new RegExp(a),d=c.elem[h.elemv],e=d.match(/\d+/g)||[],f=c.checkVoid(e[0],e[1],e[2]);if(""!==d.replace(/\s/g,"")){if(!b.test(d))return c.elem[h.elemv]="",c.msg("日期不符合格式，请重新选择。"),1;if(f[0])return c.elem[h.elemv]="",c.msg("日期不在有效期内，请重新选择。"),1;f.value=c.elem[h.elemv].match(b).join(),e=f.value.match(/\d+/g),e[1]<1?(e[1]=1,f.auto=1):e[1]>12?(e[1]=12,f.auto=1):e[1].length<2&&(f.auto=1),e[2]<1?(e[2]=1,f.auto=1):e[2]>c.months[(0|e[1])-1]?(e[2]=31,f.auto=1):e[2].length<2&&(f.auto=1),e.length>3&&(c.timeVoid(e[3],0)&&(f.auto=1),c.timeVoid(e[4],1)&&(f.auto=1),c.timeVoid(e[5],2)&&(f.auto=1)),f.auto?c.creation([e[0],0|e[1],0|e[2]],1):f.value!==c.elem[h.elemv]&&(c.elem[h.elemv]=f.value)}},c.months=[31,null,31,30,31,30,31,31,30,31,30,31],c.viewDate=function(a,b,d){var f=(c.query,{}),g=new Date;a<(0|c.mins[0])&&(a=0|c.mins[0]),a>(0|c.maxs[0])&&(a=0|c.maxs[0]),g.setFullYear(a,b,d),f.ymd=[g.getFullYear(),g.getMonth(),g.getDate()],c.months[1]=c.isleap(f.ymd[0])?29:28,g.setFullYear(f.ymd[0],f.ymd[1],1),f.FDay=g.getDay(),f.PDay=c.months[0===b?11:b-1]-f.FDay+1,f.NDay=1,c.each(h.tds,function(a,b){var g,d=f.ymd[0],e=f.ymd[1]+1;b.className="",a<f.FDay?(b.innerHTML=g=a+f.PDay,c.addClass(b,"laydate_nothis"),1===e&&(d-=1),e=1===e?12:e-1):a>=f.FDay&&a<f.FDay+c.months[f.ymd[1]]?(b.innerHTML=g=a-f.FDay+1,a-f.FDay+1===f.ymd[2]&&(c.addClass(b,h[2]),f.thisDay=b)):(b.innerHTML=g=f.NDay++,c.addClass(b,"laydate_nothis"),12===e&&(d+=1),e=12===e?1:e+1),c.checkVoid(d,e,g)[0]&&c.addClass(b,h[1]),c.options.festival&&c.festival(b,e+"."+g),b.setAttribute("y",d),b.setAttribute("m",e),b.setAttribute("d",g),d=e=g=null}),c.valid=!c.hasClass(f.thisDay,h[1]),c.ymd=f.ymd,h.year.value=c.ymd[0]+"年",h.month.value=c.digit(c.ymd[1]+1)+"月",c.each(h.mms,function(a,b){var d=c.checkVoid(c.ymd[0],(0|b.getAttribute("m"))+1);"y"===d[0]||"m"===d[0]?c.addClass(b,h[1]):c.removeClass(b,h[1]),c.removeClass(b,h[2]),d=null}),c.addClass(h.mms[c.ymd[1]],h[2]),f.times=[0|c.inymd[3]||0,0|c.inymd[4]||0,0|c.inymd[5]||0],c.each(new Array(3),function(a){c.hmsin[a].value=c.digit(c.timeVoid(f.times[a],a)?0|c.mins[a+3]:0|f.times[a])}),c[c.valid?"removeClass":"addClass"](h.ok,h[1])},c.festival=function(a,b){var c;switch(b){case"1.1":c="元旦";break;case"3.8":c="妇女";break;case"4.5":c="清明";break;case"5.1":c="劳动";break;case"6.1":c="儿童";break;case"9.10":c="教师";break;case"10.1":c="国庆"}c&&(a.innerHTML=c),c=null},c.viewYears=function(a){var b=c.query,d="";c.each(new Array(14),function(b){d+=7===b?"<li "+(parseInt(h.year.value)===a?'class="'+h[2]+'"':"")+' y="'+a+'">'+a+"年</li>":'<li y="'+(a-7+b)+'">'+(a-7+b)+"年</li>"}),b("#laydate_ys").innerHTML=d,c.each(b("#laydate_ys li"),function(a,b){"y"===c.checkVoid(b.getAttribute("y"))[0]?c.addClass(b,h[1]):c.on(b,"click",function(a){c.stopmp(a).reshow(),c.viewDate(0|this.getAttribute("y"),c.ymd[1],c.ymd[2])})})},c.initDate=function(){var d=(c.query,new Date),e=c.elem[h.elemv].match(/\d+/g)||[];e.length<3&&(e=c.options.start.match(/\d+/g)||[],e.length<3&&(e=[d.getFullYear(),d.getMonth()+1,d.getDate()])),c.inymd=e,c.viewDate(e[0],e[1]-1,e[2])},c.iswrite=function(){var a=c.query,b={time:a("#laydate_hms")};c.shde(b.time,!c.options.istime),c.shde(h.oclear,!("isclear"in c.options?c.options.isclear:1)),c.shde(h.otoday,!("istoday"in c.options?c.options.istoday:1)),c.shde(h.ok,!("issure"in c.options?c.options.issure:1))},c.orien=function(a,b){var d,e=c.elem.getBoundingClientRect();a.style.left=e.left+(b?0:c.scroll(1))+"px",d=e.bottom+a.offsetHeight/1.5<=c.winarea()?e.bottom-1:e.top>a.offsetHeight/1.5?e.top-a.offsetHeight+1:c.winarea()-a.offsetHeight,a.style.top=d+(b?0:c.scroll())+"px"},c.follow=function(a){c.options.fixed?(a.style.position="fixed",c.orien(a,1)):(a.style.position="absolute",c.orien(a))},c.viewtb=function(){var a,b=[],f=["日","一","二","三","四","五","六"],h={},i=d[e]("table"),j=d[e]("thead");return j.appendChild(d[e]("tr")),h.creath=function(a){var b=d[e]("th");b.innerHTML=f[a],j[g]("tr")[0].appendChild(b),b=null},c.each(new Array(6),function(d){b.push([]),a=i.insertRow(0),c.each(new Array(7),function(c){b[d][c]=0,0===d&&h.creath(c),a.insertCell(c)})}),i.insertBefore(j,i.children[0]),i.id=i.className="laydate_table",a=b=null,i.outerHTML.toLowerCase()}(),c.view=function(a,f){var i,g=c.query,j={};f=f||a,c.elem=a,c.options=f,c.options.format||(c.options.format=b.format),c.options.start=c.options.start||"",c.mm=j.mm=[c.options.min||b.min,c.options.max||b.max],c.mins=j.mm[0].match(/\d+/g),c.maxs=j.mm[1].match(/\d+/g),h.elemv=/textarea|input/.test(c.elem.tagName.toLocaleLowerCase())?"value":"innerHTML",c.box?c.shde(c.box):(i=d[e]("div"),i.id=h[0],i.className=h[0],i.style.cssText="position: absolute;",i.setAttribute("name","laydate-v"+laydate.v),i.innerHTML=j.html='<div class="laydate_top"><div class="laydate_ym laydate_y" id="laydate_YY"><a class="laydate_choose laydate_chprev laydate_tab"><cite></cite></a><input id="laydate_y" readonly><label></label><a class="laydate_choose laydate_chnext laydate_tab"><cite></cite></a><div class="laydate_yms"><a class="laydate_tab laydate_chtop"><cite></cite></a><ul id="laydate_ys"></ul><a class="laydate_tab laydate_chdown"><cite></cite></a></div></div><div class="laydate_ym laydate_m" id="laydate_MM"><a class="laydate_choose laydate_chprev laydate_tab"><cite></cite></a><input id="laydate_m" readonly><label></label><a class="laydate_choose laydate_chnext laydate_tab"><cite></cite></a><div class="laydate_yms" id="laydate_ms">'+function(){var a="";return c.each(new Array(12),function(b){a+='<span m="'+b+'">'+c.digit(b+1)+"月</span>"}),a}()+"</div>"+"</div>"+"</div>"+c.viewtb+'<div class="laydate_bottom">'+'<ul id="laydate_hms">'+'<li class="laydate_sj">时间</li>'+"<li><input readonly>:</li>"+"<li><input readonly>:</li>"+"<li><input readonly></li>"+"</ul>"+'<div class="laydate_time" id="laydate_time"></div>'+'<div class="laydate_btn">'+'<a id="laydate_clear">清空</a>'+'<a id="laydate_today">今天</a>'+'<a id="laydate_ok">确认</a>'+"</div>"+(b.isv?'<a href="http://sentsin.com/layui/laydate/" class="laydate_v" target="_blank">laydate-v'+laydate.v+"</a>":"")+"</div>",d.body.appendChild(i),c.box=g("#"+h[0]),c.events(),i=null),c.follow(c.box),f.zIndex?c.box.style.zIndex=f.zIndex:c.removeCssAttr(c.box,"z-index"),c.stopMosup("click",c.box),c.initDate(),c.iswrite(),c.check()},c.reshow=function(){return c.each(c.query("#"+h[0]+" .laydate_show"),function(a,b){c.removeClass(b,"laydate_show")}),this},c.close=function(){c.reshow(),c.shde(c.query("#"+h[0]),1),c.elem=null},c.parse=function(a,d,e){return a=a.concat(d),e=e||(c.options?c.options.format:b.format),e.replace(/YYYY|MM|DD|hh|mm|ss/g,function(){return a.index=0|++a.index,c.digit(a[a.index])})},c.creation=function(a,b){var e=(c.query,c.hmsin),f=c.parse(a,[e[0].value,e[1].value,e[2].value]);c.elem[h.elemv]=f,b||(c.close(),"function"==typeof c.options.choose&&c.options.choose(f))},c.events=function(){var b=c.query,e={box:"#"+h[0]};c.addClass(d.body,"laydate_body"),h.tds=b("#laydate_table td"),h.mms=b("#laydate_ms span"),h.year=b("#laydate_y"),h.month=b("#laydate_m"),c.each(b(e.box+" .laydate_ym"),function(a,b){c.on(b,"click",function(b){c.stopmp(b).reshow(),c.addClass(this[g]("div")[0],"laydate_show"),a||(e.YY=parseInt(h.year.value),c.viewYears(e.YY))})}),c.on(b(e.box),"click",function(){c.reshow()}),e.tabYear=function(a){0===a?c.ymd[0]--:1===a?c.ymd[0]++:2===a?e.YY-=14:e.YY+=14,2>a?(c.viewDate(c.ymd[0],c.ymd[1],c.ymd[2]),c.reshow()):c.viewYears(e.YY)},c.each(b("#laydate_YY .laydate_tab"),function(a,b){c.on(b,"click",function(b){c.stopmp(b),e.tabYear(a)})}),e.tabMonth=function(a){a?(c.ymd[1]++,12===c.ymd[1]&&(c.ymd[0]++,c.ymd[1]=0)):(c.ymd[1]--,-1===c.ymd[1]&&(c.ymd[0]--,c.ymd[1]=11)),c.viewDate(c.ymd[0],c.ymd[1],c.ymd[2])},c.each(b("#laydate_MM .laydate_tab"),function(a,b){c.on(b,"click",function(b){c.stopmp(b).reshow(),e.tabMonth(a)})}),c.each(b("#laydate_ms span"),function(a,b){c.on(b,"click",function(a){c.stopmp(a).reshow(),c.hasClass(this,h[1])||c.viewDate(c.ymd[0],0|this.getAttribute("m"),c.ymd[2])})}),c.each(b("#laydate_table td"),function(a,b){c.on(b,"click",function(a){c.hasClass(this,h[1])||(c.stopmp(a),c.creation([0|this.getAttribute("y"),0|this.getAttribute("m"),0|this.getAttribute("d")]))})}),h.oclear=b("#laydate_clear"),c.on(h.oclear,"click",function(){c.elem[h.elemv]="",c.close()}),h.otoday=b("#laydate_today"),c.on(h.otoday,"click",function(){c.elem[h.elemv]=laydate.now(0,c.options.format),c.close()}),h.ok=b("#laydate_ok"),c.on(h.ok,"click",function(){c.valid&&c.creation([c.ymd[0],c.ymd[1]+1,c.ymd[2]])}),e.times=b("#laydate_time"),c.hmsin=e.hmsin=b("#laydate_hms input"),e.hmss=["小时","分钟","秒数"],e.hmsarr=[],c.msg=function(a,d){var f='<div class="laydte_hsmtex">'+(d||"提示")+"<span>×</span></div>";"string"==typeof a?(f+="<p>"+a+"</p>",c.shde(b("#"+h[0])),c.removeClass(e.times,"laydate_time1").addClass(e.times,"laydate_msg")):(e.hmsarr[a]?f=e.hmsarr[a]:(f+='<div id="laydate_hmsno" class="laydate_hmsno">',c.each(new Array(0===a?24:60),function(a){f+="<span>"+a+"</span>"}),f+="</div>",e.hmsarr[a]=f),c.removeClass(e.times,"laydate_msg"),c[0===a?"removeClass":"addClass"](e.times,"laydate_time1")),c.addClass(e.times,"laydate_show"),e.times.innerHTML=f},e.hmson=function(a,d){var e=b("#laydate_hmsno span"),f=c.valid?null:1;c.each(e,function(b,e){f?c.addClass(e,h[1]):c.timeVoid(b,d)?c.addClass(e,h[1]):c.on(e,"click",function(){c.hasClass(this,h[1])||(a.value=c.digit(0|this.innerHTML))})}),c.addClass(e[0|a.value],"laydate_click")},c.each(e.hmsin,function(a,b){c.on(b,"click",function(b){c.stopmp(b).reshow(),c.msg(a,e.hmss[a]),e.hmson(this,a)})}),c.on(d,"mouseup",function(){var a=b("#"+h[0]);a&&"none"!==a.style.display&&(c.check()||c.close())}).on(d,"keydown",function(b){b=b||a.event;var d=b.keyCode;13===d&&c.creation([c.ymd[0],c.ymd[1]+1,c.ymd[2]])})},c.init=function(){c.use("need"),c.use(h[4]+b.defSkin,h[3]),c.skinLink=c.query("#"+h[3])}(),laydate.reset=function(){c.box&&c.elem&&c.follow(c.box)},laydate.now=function(a,b){var d=new Date(0|a?function(a){return 864e5>a?+new Date+864e5*a:a}(parseInt(a)):+new Date);return c.parse([d.getFullYear(),d.getMonth()+1,d.getDate()],[d.getHours(),d.getMinutes(),d.getSeconds()],b)},laydate.skin=function(a){c.skinLink.href=c.getPath+h[4]+a+h[5]}}(window);
