<template>
    <div v-bind:id="modal_id" aria-labelledby="bsModalLabel " class="modal fade" data-backdrop="static"
        data-keyboard="false" data-toggle="modal" role="dialog">
        <div class="modal-dialog" role="document" @click.stop>
            <div class="modal-content">
                <div class="modal-header">
                    <slot name="header"></slot>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close()">
                    </button>
                </div>
                <div class="modal-body">
                    <slot name="content" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['id', 'selectedId', 'modelType'],
    data() {
        return {
            modal_id: this.id ? this.id : 'genericModal',
            currentSelected: null,
            currentSelectedMultiple: null,
        }
    },
    methods: {
        clickOverlay() {
            this.close()
        },
        close() {
            $('#' + this.modal_id).modal('hide')
        },
    },
    updated() {
        $('.form-select-modal').select2({
            dropdownParent: $('.modal'),
        });
        this.currentSelected = $('.form-select-modal').val();
        if (this.currentSelected) {
            $('.form-select-modal').val(this.currentSelected).change();
        } else {
            $('.form-select-modal').val(this.selectedId).change();
        }
        // multiple select2
        $('.form-select-modal-multiple').select2({
            dropdownParent: $('.modal'),
        });
        this.currentSelectedMultiple = $('.form-select-modal-multiple').val();
        if (this.currentSelectedMultiple && this.currentSelectedMultiple.length > 0) {
            $('.form-select-modal-multiple').val(this.currentSelectedMultiple).change();
        } else {
            $('.form-select-modal-multiple').val(this.selectedId).change();
        }
    },
}
</script>
