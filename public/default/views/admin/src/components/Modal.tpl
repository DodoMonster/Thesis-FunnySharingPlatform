<div class="modal-open" v-show="modal.show">
    <div class="modal chenyou-modal default-modal" @click="cancel">
        <div class="modal-dialog" :class="[modal.size == 'sm' ? 'modal-sm' : '', modal.size == 'lg' ? 'modal-lg' : '']">
            <div class="modal-content" @click="cancelBubble($event)">
                <div class="modal-header">
                    <button type="button" class="close" @click="cancel" >
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel" v-if="title !== ''">{{modal.title || '提示'}}</h4>
                </div>

                <div class="modal-body">
                    <slot name="modal-body"></slot>
                </div>

                <div class="modal-footer">                
                    <button type="button" class="btn btn-danger" data-dismiss="modal" @click="cancel">
                        <i class="ti-close"></i> {{ modal.cancelBtnText || '取消'}}</button>
                    <slot name="modal-footer"></slot>
                </div>
            </div>
        </div>
    </div>
</div>
