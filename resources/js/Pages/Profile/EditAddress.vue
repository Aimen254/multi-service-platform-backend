<template>
    <form @submit.prevent="submit">
        <div class="row">
            <div class="row">
                <div class="col-md-6 ">
                    <h3 class="mb-5">Address Information</h3>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <div @click="addAddress()">
                        <div class="
                btn btn-icon btn-bg-light btn-active-color-primary btn-sm
                me-1
              ">
                            <span class="svg-icon svg-icon-3">
                                <inline-svg :src="'/images/icons/add_more.svg'" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5" v-for="(address, index) in form.addresses" :key="index">
            <div class="row">
                <hr v-if="index > 0">
                <div class="col-md-3">
                    <Input id="name" type="text" 
                      :class="{'is-invalid border border-danger' : form.errors[`addresses.${index}.name`]}"
                      autofocus autocomplete="zipcode" placeholder="Address type"
                        v-model="address.name" />
                    <error :message="form.errors[`addresses.${index}.name`]"></error>
                </div>
                <div class="col-md-5">
                    <GMapAutocomplete class="form-control form-control-lg form-control-solid"
                      :class="{'is-invalid border border-danger' : form.errors[`addresses.${index}.address`]}"
                      placeholder="Type your address" @place_changed="getAddressData" @change="activeIndex = index"
                      autocomplete="off" v-model="address.address" :value="address.address">
                    </GMapAutocomplete>
                    <error :message="form.errors[`addresses.${index}.address`]"></error>
                </div>
                <div class="col-md-3 pt-4">
                    <label>
                        <input v-model="address.is_default" type="radio"
                            :checked="address.is_default == 1 ? true : false" :value="address.is_default"
                            class="form-check-input" @click="setValue(index)" />
                        Mark default
                    </label>
                </div>
                <div class="col-md-1">
                    <div @click="removeAddress(index)" v-if="index > 0">
                        <div class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                            <i class="fa fa-trash-alt text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-ms-12">
                    <textarea v-model="address.note" class="form-control form-control-lg form-control-solid"
                        placeholder="Special address instructions (between streets, apartments numbers, etc)"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="row">
                <div class="col-md-12 mt-5 d-flex justify-content-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing">Save</span>
                        <span class="indicator-progress" v-if="form.processing">
                            <span class="spinner-border spinner-border-sm align-middle"></span>
                        </span>
                    </Button>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    import Input from "@/Components/Input.vue";
    import Label from "@/Components/Label.vue";
    import Button from "@/Components/Button.vue";
    import Error from "@/Components/InputError.vue";
    import Helpers from "@/Mixins/Helpers";
    import {
        useForm
    } from "@inertiajs/inertia-vue3";
    import InlineSvg from "vue-inline-svg";

    export default {
        props: ["user"],
        components: {
            Input,
            Label,
            Button,
            Error,
            InlineSvg,
        },
        data() {
            var address_list = [];
            this.user.addresses.forEach((value) => {
                address_list.push(value);
            });
            return {
                form: useForm({
                    addresses: address_list.length > 0 ?
                        address_list :
                        [{
                            name: "",
                            address: "",
                            latitude: "",
                            longitude: "",
                            note: "",
                            is_default: 0,
                        }, ],
                }),
                url: null,
                marker: {
                    position: {
                        lat: null,
                        lng: null,
                    },
                },
                isOpened: false,
                isSaved: false,
                activeIndex: -1,
            };
        },
        methods: {
            addAddress() {
                this.form.addresses.push({
                    name: "",
                    address: "",
                    latitude: "",
                    longitude: "",
                    note: "",
                    is_default: 0,
                });
            },
            removeAddress(index) {
                this.form.addresses.splice(index, 1);
            },
            getAddressData(addressData) {
                this.form.addresses[this.activeIndex].address =
                    addressData.formatted_address;
                this.form.addresses[this.activeIndex].latitude =
                    addressData.geometry.location.lat();
                this.form.addresses[this.activeIndex].longitude =
                    addressData.geometry.location.lng();
            },

            submit() {
                this.form.post(route("dashboard.profile.update.address"), {
                    errorBag: "admin",
                    preserveScroll: true,
                });
            },
            setValue(index) {
                this.form.addresses.forEach((value, i) => {
                    value.is_default = i == index ? 1 : 0;
                });
            },
        },
        mixins: [Helpers],
    };
</script>