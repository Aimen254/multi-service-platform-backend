
<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" @submit.prevent="submit" >
                <h6>Businesses</h6>
                <p class="fw-bold text-primary">You can only active {{ allowedBusinesses }} businesses.</p>
                <div class="form-check mt-5" v-for="(business, index) in allBusinesses" :key="index">
                    <input class="form-check-input" type="checkbox" :value="business.id" v-model="selectedBusinesses">
                    <label class="form-check-label" for="flexCheckIsFeatured">
                        {{ business.name }}
                    </label>
                </div>
                <div class="text-end">
                    <Button type="submit" :disabled="selectedBusinesses.length > allowedBusinesses || selectedBusinesses.length == 0 ">
                        <span class="indicator-label">Save</span>
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
import Select2 from 'vue3-select2-component'

export default {
    components: {
        Modal,
        Label,
        Input,
        Error,
        Button,
        Select2
    },

    data() {
        return {
            title: 'Businesses',
            maxWidth: '2xl',
            allBusinesses: null,
            selectedBusinesses: [],
            allowedBusinesses: null
        }
    },

    methods: {
        submit() {
            if (this.selectedBusinesses.length <= this.allowedBusinesses && this.selectedBusinesses.length != 0) {
                this.emitter.emit('selected_active_businesses', {
                    businesses: this.selectedBusinesses
                })
                $('#genericModal').modal('hide')
            }
        }
    },

    mounted() {
        this.emitter.on('active_businesses', (args) => {
            this.allBusinesses = args.businesses
            this.allowedBusinesses = args.allowedBusinesses
            $('#genericModal').modal('show');
        })
    }
}
</script>
