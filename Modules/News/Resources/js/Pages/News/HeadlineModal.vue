<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">Headline News</h4>
        </template>
        <template #content>
            <form @submit.prevent="submit">
                <div class="row">
                    <div class="col-12 mb-4">
                        <Label for="stock" class="required" value="Headline Type" />
                        <select v-model="form.type" class="form-select text-capitalize form-select-solid" :class="{'is-invalid border border-danger': form.errors.type,}">
                            <option class="text-capitalize fw-bold">Select</option>
                            <option class="text-capitalize">Primary</option>
                            <option class="text-capitalize">Secondary</option>
                        </select>
                        <error :message="form.errors.type"></error>
                    </div>
                    <div class="text-end">
                        <Button type="submit" :disabled="isLoading">Submit</Button>
                    </div>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import Label from '@/Components/Label.vue';
import Button from "@/Components/Button.vue";
import Error from '@/Components/InputError.vue';
import { useForm } from "@inertiajs/inertia-vue3";

export default {
    props: ['url'],

    components: {
        Modal,
        Label,
        Error,
        Button
    },

    data() {
        return {
            form: {
                type: 'Select',
                errors: ''
            },
            isLoading: false
        };
    },

    methods: {
        submit() {
            this.isLoading = true;
            this.form.post(
                this.url,
                {
                    errorBag: "groups",
                    preserveScroll: true,
                    onSuccess: (e) => {
                        $("#genericModal").modal("hide");
                        this.form.errors = null;
                        this.isLoading = false;
                    },
                    onError: errors => {
                        console.log(errors);
                        this.form.errors = errors
                        this.form.processing = false;
                        this.isLoading = false;
                    },
                }
            );
        }
    },

    mounted() {
        this.emitter.on("headline-modal", (args) => {
            this.form = useForm({
                product_id: args?.product_id,
                level_two_tag_id: args?.level_two_tag_id,
                type: this.form?.type
            });
            $("#genericModal").modal("show");
        });
    }
}

</script>
