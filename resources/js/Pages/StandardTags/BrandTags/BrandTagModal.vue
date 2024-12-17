
<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <Label for="tags" value="Select Tags" />
                    <div class="fv-row mb-4" @click="focusTag()">
                        <VueTagsInput id="tagsInput"
                            class="tags-input form-control form-control-md form-control-solid w-full bg-transparent"
                            v-model="tags"
                            :placeholder="'Select tags'"
                            :tags="form.tags"
                            :autocomplete-items="all_tags"
                            :autocomplete-always-open="showDropDown"
                            :add-only-from-autocomplete="true"
                            :autocomplete-min-length="1"
                            @tags-changed="updateTags"                            
                            @focus="focus"
                            @blur="focus"
                            @keyup="getAutoComplete($event)"
                        />
                    </div>         
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing || form.tags.length == 0"
                        >
                        <span class="indicator-label" v-if="!form.processing">Save</span>
                        <span class="indicator-progress" v-if="form.processing">
                            <span class="spinner-border spinner-border-sm align-middle"></span>
                        </span>
                    </Button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'
import Modal from '@/Components/Modal.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
// import Select2 from 'vue3-select2-component'
import VueTagsInput  from '@sipec/vue3-tags-input';
import Helpers from "@/Mixins/Helpers";


export default {
    components: {
        Modal,
        Label,
        Input,
        Error,
        Button,
        VueTagsInput
    },

    data() {
        return {
            title:null,
            maxWidth: '2xl',
            form: null,
            tags: '',
            tagsArray: [],
            showDropDown: false,
            selected: 0,
            nodes: null,
            item: null
        }
    },
     props: ["allTags"],

    methods: {
        submit() {
                this.form.post(route('dashboard.brandTag.store'), {
                    errorBag: 'groups',
                    preserveScroll: true,
                    onSuccess: () => {
                        $('#genericModal').modal('hide')
                    }
                })
            
        },
        updateTags(event) {
            this.form.tags = event
        },
       focus(){
            setTimeout(()=>{
                this.showDropDown = this.showDropDown ? false : true
            },400)
        },

        focusTag() {
            document.getElementById("tagsInput").focus();
        },
        getAutoComplete(e) {
            this.item = document.querySelector('.ti-autocomplete ul');
            this.nodes = document.querySelectorAll('.ti-autocomplete li');
            if (e.key == 'ArrowUp') { // up
                this.selectItem(this.nodes[this.selected - 1], e);
            }
            if (e.key == 'ArrowDown') { // down
                this.selectItem(this.nodes[this.selected + 1], e);
            }
        },
        
        selectItem(el, e) {
            var s = [].indexOf.call(this.nodes, el);
            if (s === -1) return;                
            this.selected = s;
            var elHeight = $(el).height();
            var scrollTop = $(this.item).scrollTop();
            var viewport = scrollTop + $(this.item).height();
            var elOffset = elHeight * this.selected;
            if (e.key == 'ArrowUp') { // up
                this.item.scrollBy(0, -30);
            }
            if (e.key == 'ArrowDown') { // up
                this.item.scrollBy(0, 30);
            }
        }
    },

    mounted() {
        this.emitter.on('tag-modal', (args) => {
            this.form = useForm({
                tags: []
            })
            this.title = 'Brand Tags';
            $('#genericModal').modal('show')
        })
    },
    computed: {
        all_tags() {
            return this.allTags.filter(i => {
                return i.text.toLowerCase().indexOf(this.tags.toLowerCase()) !== -1;
            });
        }
    },
    mixins: [Helpers],
}
</script>

<style>
    .fv-plugins-icon-container .fv-row .vue-tags-input{
       min-height: 270px;
        border: 1px solid #d8d8d8!important;
        max-height: 100%;
    }
    .fv-plugins-icon-container .fv-row .vue-tags-input .ti-input{
        border: none!important;
    }
    .tags-input{
        max-width: 100%!important;
        background: none!important;
    }
    .dropdown.show > .form-control.form-control-solid, 
    .form-control.form-control-solid:active, 
    .form-control.form-control-solid.active, 
    .form-control.form-control-solid:focus, 
    .form-control.form-control-solid.focus{
        background: none;
    }
    .ti-tags .ti-tag{
        background-color:#009EF7;
        padding:10px;
        border-radius: 8px;
    }
    .ti-selected-item{
        background-color:#009EF7!important;
    } 
    .ti-item{
        background-color:white;
        padding: 3px;
    }
    .ti-item{
        color:black;
    }
    .ti-icon-close{
         color:rgb(255, 255, 255);
    }
    .ti-autocomplete{
        background: white;
        width: 96%!important;
    }
    .ti-autocomplete ul{
        max-height: 40vh!important;
        overflow: scroll;
    }
</style>