<template>
    <form class="form" v-if="form" @submit.prevent="submit">
        <div class="card card-flush py-4">
            <div class="card-body py-3 row">
                <Toggle class="ms-auto" v-if="checkUserPermissions('edit_delivery_zones')"
                    :status="booleanStatusValue(this.form.status)" @click.prevent="changeStatus(this.form.id)" />
                <div class="row">
                    <div class="row mb-2">
                        <div class="col-lg-12 mb-2 fv-row fv-plugins-icon-container">
                            <Label for="fee_type" value="Custom Delivery Fee" />
                            <select v-model="form.fee_type" class="form-select text-capitalize form-select-solid"
                                :class="{'is-invalid border border-danger' : form.errors.fee_type }"
                                @change="changeType($event, 'fee_type')">
                                <option class="text-capitalize" selected>Delivery fee by mileage</option>
                                <option class="text-capitalize">
                                    Delivery fee as percentage of sale
                                </option>
                            </select>
                            <error :message="form.errors.fee_type"></error>
                        </div>
                    </div>
                    <h4 class="py-6" v-if="selectedFeeType == 'delivery_fee_by_mileage'">
                        Delivery fee by mileage
                    </h4>
                    <div class="row mb-4 align-items-center" v-if="selectedFeeType == 'delivery_fee_by_mileage'">
                        <Label class="col-12" for="mileage_fee" value="Mileage Fee of $" />
                        <div class="col-4 ps-4">
                            <Input type="number" :class="{'is-invalid border border-danger' : form.errors.mileage_fee}"
                                v-model="form.mileage_fee" autofocus placeholder="Enter Mileage Fee" />
                            <error :message="form.errors.mileage_fee"></error>
                        </div>
                        <Label class="col-2 px-6 fs-7" for="mileage_distance" value="for first" />
                        <div class="col-4">
                            <Input type="number"
                                :class="{'is-invalid border border-danger' : form.errors.mileage_distance}"
                                v-model="form.mileage_distance" autofocus placeholder="Enter Mileage Distance" />
                            <error :message="form.errors.mileage_distance"></error>
                        </div>
                        <Label class="px-6 col-2" for="mileage_distance" value="miles" />
                    </div>
                    <div class="row align-items-center" v-if="selectedFeeType == 'delivery_fee_by_mileage'">
                        <Label for="mileage_fee col-12" value="Mileage Fee of $" />
                        <div class="ps-4 col-7">
                            <Input type="number"
                                :class="{'is-invalid border border-danger': form.errors.extra_mileage_fee}"
                                v-model="form.extra_mileage_fee" autofocus placeholder="Enter Mileage Fee" />
                            <error :message="form.errors.extra_mileage_fee"></error>
                        </div>
                        <Label class="px-6 col-5" for="mileage_distance" value="per mile thereafter" />
                    </div>
                    <h4 class="py-6" v-if="selectedFeeType == 'delivery_fee_as_percentage_of_sale'">
                        Delivery fee as percentage of sale
                    </h4>
                    <div class="d-flex align-items-center"
                        v-if="selectedFeeType == 'delivery_fee_as_percentage_of_sale'">
                        <Label for="mileage_fee" value="$" />
                        <div class="ps-4">
                            <Input type="number" :class="{'is-invalid border border-danger' : form.errors.fixed_amount}"
                                v-model="form.fixed_amount" autofocus placeholder="Enter Fee in $" />
                            <error :message="form.errors.fixed_amount"></error>
                        </div>
                        <Label class="px-6" for="mileage_distance" value="or" />
                        <div>
                            <Input type="number"
                                :class="{'is-invalid border border-danger' : form.errors.percentage_amount }"
                                v-model="form.percentage_amount" autofocus placeholder="Enter Fee in $" />
                            <error :message="form.errors.percentage_amount"></error>
                        </div>
                        <Label class="px-6" for="mileage_distance" value="% of each sale, whichever is greater" />
                    </div>
                </div>

                <div class="mt-2">
                    <div class="text-end">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing"> Update </span>
                            <span class="indicator-progress" v-if="form.processing">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" v-model="form.type" />
    </form>
</template>

<script>
    import {
        Link
    } from "@inertiajs/inertia-vue3";
    import Helpers from "@/Mixins/Helpers";
    import Label from "@/Components/Label.vue";
    import Input from "@/Components/Input.vue";
    import {
        useForm
    } from '@inertiajs/inertia-vue3'
    import Button from '@/Components/Button.vue'
    import Error from '@/Components/InputError.vue'
    import Toggle from '@/Components/ToggleButton.vue';

    export default {
        components: {
            Link,
            Toggle,
            Button,
            Label,
            Input,
            Error,
        },

        props: ["deliveryZone"],

        data() {
            return {
                form: null,
                selectedFeeType: null,
                type: null,
                locationData: this.locationInfo,
                deliveryZoneData: this.deliveryZone,
                types: this.type
            };
        },

        watch: {
            deliveryZone: {
                handler(deliveryZone) {
                    this.deliveryZoneData = deliveryZone
                },
                deep: true
            }
        },
        methods: {
            submit() {
                this.form.put(route('dashboard.settings.deliveyzone.update', [this.form.id]), {
                    preserveScroll: false,
                    onError: (response) => {},
                })
            },
            changeType(event, type) {
                switch (type) {
                    case 'fee_type':
                        this.selectedFeeType = this.setTypeFormat(event.target.value)
                        break;
                    default:
                        break;
                }
            },
            setTypeFormat(type) {
                return type.toLowerCase().split(" ").join("_");
            },
            changeStatus(id) {
                this.swal.fire({
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Change Status</h1><p class='text-base'>Are you sure you want to change status?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'No',
                    confirmButtonText: "Yes",
                    customClass: {
                        confirmButton: 'danger'
                    }
                }).then((result) => {
                    if (result.value) {
                        showWaitDialog()

                        this.$inertia.visit(route('dashboard.settings.deliveryzone.status', id), {
                            preserveScroll: false,
                            onSuccess: () => hideWaitDialog()
                        })
                    }
                })
            }

        },
        mounted() {
            this.form = useForm({
                id: this.deliveryZoneData.id,
                fee_type: this.deliveryZoneData.fee_type ? this.deliveryZoneData.fee_type :
                    'delivery_fee_by_mileage',
                mileage_fee: this.deliveryZoneData.mileage_fee,
                extra_mileage_fee: this.deliveryZoneData.extra_mileage_fee,
                mileage_distance: this.deliveryZoneData.mileage_distance,
                percentage_amount: this.deliveryZoneData.percentage_amount,
                fixed_amount: this.deliveryZoneData.fixed_amount,
                status: this.deliveryZoneData.status
            });
            this.selectedFeeType = this.form.fee_type ? this.setTypeFormat(this.form.fee_type) : this.form.fee_type;
        },
        mixins: [Helpers],
    };
</script>