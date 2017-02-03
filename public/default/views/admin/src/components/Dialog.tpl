<div class="modal-open" v-show="isShow" transition="fade">
    <div class="modal chenyou-modal fade in">
        <div class="modal-dialog" :class="dialogInfo.size == 'lg' ? 'modal-lg' : 'modal-sm'">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" @click="cancel()">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title align-center bolder">{{ dialogInfo.title || '错误提示'}}</h4>
                </div>

                <div class="modal-body">
                    <span class="pull-left">{{{dialogInfo.icon}}}</span>
                    <p v-for="text in dialogInfo.msg">
                        {{text}}
                    </p>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="cancel()">
                        <i class="ti-close"></i> {{ dialogInfo.cancleBtnText || '关闭'}}
                    </button>
                    <button type="button" class="btn btn-primary" v-if="!isAlertDailog"@click="confirm()">
                        <i class="ti-check"></i> {{ dialogInfo.confirmBtnText || '确定'}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
