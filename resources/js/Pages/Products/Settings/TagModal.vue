<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">Chose parent</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" >
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <Label for="Parent Tags" class="required" value="Select paret tags"/>
                        <select 
                            class="form-select text-capitalize form-select-solid" 
                            :class="{'is-invalid border border-danger' : form.errors.parent_id}"
                            v-model="form.parent_id"
                        >
                            <option class="text-capitalize" value="" >Select parent tags</option>
                            <option class="text-capitalize" v-for="parent in parents" :key="parent.id" :value="parent.id">{{ parent.name }}</option>
                        </select>
                    <error :message="form.errors.parent_id"></error>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing || disableSubmit"
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

<script setup>
import Modal from '@/Components/Modal.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
import Helpers from "@/Mixins/Helpers";
import { useForm } from '@inertiajs/inertia-vue3'
</script>
<script>
export default {
    props: ['id', 'uuid'],
    data(){
        return {
            parents: [],
            form: null
        }
    },
    methods: {
        submit(){
            this.form.post(route("dashboard.modules.business.product.product-tags.parent", [
                this.getSelectedModuleValue(),
                this.getSelectedBusinessValue(),
                this.uuid,    
                this.id
                ]), {
                    errorBag: 'parent_id',
                    preserveScroll: true,
                    onSuccess: () => {
                        $('#genericModal').modal('hide')
                        window.location.reload();
                    }
                });
        }
    },
    mounted(){
        window.axios.get(
                route("dashboard.modules.business.product.product-tags.parent", [
                this.getSelectedModuleValue(),
                this.getSelectedBusinessValue(),
                this.uuid,    
                this.id
                ])
            )
            .then((response) => {
                this.parents = response.data
            });

        this.form = useForm({
            parent_id: ""
        });

        $('#genericModal').modal('show')
    },
    mixins: [Helpers]
}
</script>
