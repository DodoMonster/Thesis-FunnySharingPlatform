<template src="./Modal.tpl"></template>

<script>
    export default {
        // 该组件只是简单的弹窗盒子
        props:['modal'],
        data () {
            return {
                
            }
        },
        ready(){
        },
        methods:{
            cancel:function(){
                // 如果需要跟父级组件有除了显示与hide之外的逻辑可在这里冒泡
                this.modal.show = false;
            },

            //阻止冒泡
            cancelBubble:function(e){
                if(e && e.stopPropagation){
                    e.stopPropagation();
                }else{
                    window.event.cancelBubble = true;
                }
            }
        }
    }
</script>