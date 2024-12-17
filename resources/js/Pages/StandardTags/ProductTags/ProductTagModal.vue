
<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" >
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="name" class="required" value="Name" />
                        <Input id="name" type="text"
                            :class="{'is-invalid border border-danger' : form.errors.name}" :value="form.name" v-model="form.name" placeholder="Enter name" />
                        <error :message="form.errors.name"></error>
                    </div>
                </div>
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="tags" value="Select Tags" />
                        <VueTagsInput
                            class="tags-input form-control form-control-md form-control-solid w-full bg-transparent"
                            v-model="tags"
                            :placeholder="'add tags'"
                            :tags="form.tags"
                            :autocomplete-items="all_tags"
                            :autocomplete-always-open="showDropDown"
                            :add-only-from-autocomplete="true"
                            :autocomplete-min-length="1"
                            @tags-changed="updateTags"                            
                            @focus="focus"
                            @blur="focus"
                        />
                    </div>         
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        >
                        <span class="indicator-label" v-if="!form.processing"> {{form.id ? 'Update' : 'Save'}}</span>
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

    props: ['allTags'],

    data() {
        return {
            title:null,
            maxWidth: '2xl',
            form: null,
            tags: '',
            tagsArray: [],
            showDropDown: false
        }
    },

    methods: {
        submit() {
            this.form.tags = this.tagsArray;
            if (!this.form.id) {
                this.form.post(route('dashboard.productTag.store'), {
                    errorBag: 'groups',
                    preserveScroll: true,
                    onSuccess: () => {
                        $('#genericModal').modal('hide')
                    }
                })
            } else {
                if (this.form.type != 'variant') {
                    this.form.sub_type = null
                } 
                this.form.put(route('dashboard.productTag.update', [this.form.id]), {
                    errorBag: 'groups',
                    preserveScroll: true,
                    onSuccess: () => {
                        $('#genericModal').modal('hide')
                    }
                })
            }
        },
        updateTags(event) {
            this.tagsArray = event
        },
        focus(){
            this.showDropDown = this.showDropDown ? false : true
        }
    },

    mounted() {
        this.emitter.on('tag-modal', (args) => {
            this.form = useForm({
                id: args.tag ? args.tag.id : '',
                name: args.tag ? args.tag.name : '',
                tags: args.tag ? args.tag.tags : '',
                type: args.tag ? args.tag.type : '',
            })
            this.title = args.tag.id ? 'Edit Product Tag' : 'Create Product Tag';
            this.form.type = 'product'
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
        background-color:#009EF7!important;
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
        max-height: 100%;
        overflow: scroll;
    }
</style>