<template>

    <Head title="Businesses Create Business" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create Business`" :path="`Businesses`" :subTitle="`Create Business`"></Breadcrumbs>
        </template>
        <div class="card">
            <div class="card-body p-9 pt-2">
                <form v-if="form" @submit.prevent="submit" ref="form">
                    <div class="row mb-2">
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="name" class="required" value="Name" />
                            <Input id="name" type="text"
                                :class="{'is-invalid border border-danger' : form.errors.name}" v-model="form.name"
                                autofocus autocomplete="name" placeholder="Enter Name" />
                            <error :message="form.errors.name"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="email" class="required" value="Admin Email" />
                            <Input id="email" type="email"
                                :class="{'is-invalid border border-danger' : form.errors.email}" v-model="form.email"
                                autofocus placeholder="Email" />
                            <error :message="form.errors.email"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="slug" class="required" value="Slug (https://yourpage.com/slug)" />
                            <Input id="slug" type="text"
                                :class="{'is-invalid border border-danger' : form.errors.slug}" v-model="form.slug"
                                autofocus placeholder="Slug" />
                            <error :message="form.errors.slug"></error>
                        </div>

                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="Business Owner" class="required" value="Business Owner" />
                            <select2 v-model="form.owner_id" :options="Owners"
                                :class="{'is-invalid border border-danger' : form.errors.owner_id}"
                                placeholder="Select Business Owner">
                            </select2>
                            <error :message="form.errors.owner_id"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="name" class="required" value="Module Tag" />
                            <multiselect :class="{
                                    'is-invalid border border-danger':
                                        form.errors.module,
                                }"
                                v-model="form.module"
                                :options="moduleTags"
                                :multiple="true"
                                placeholder="Select Module Tags"
                                track-by="id"
                                label="text">
                            </multiselect>
                            <error :message="form.errors.module"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="minimum_purchase" value="Minimum Purchase" />
                            <Input id="minimum_purchase" type="number"
                                :class="{'is-invalid border border-danger' : form.errors.minimum_purchase}"
                                v-model="form.minimum_purchase" autofocus
                                placeholder="Minimum Purchase" />
                            <error :message="form.errors.minimum_purchase"></error>
                        </div>
                    </div>
                    <div>
                        <GMapAutocomplete
                            class="form-control form-control-lg form-control-solid"
                            :class="{'is-invalid border border-danger' : form.errors[`addresses.${index}.address`]}"
                            placeholder="Type Your Business Address" @place_changed="getAddressData" autocomplete="off"
                            v-model="form.address" :value="form.address">
                        </GMapAutocomplete>
                        <error :message="form.errors.address"></error>
                        <GMapMap class="map-height-normal mt-4" :center="marker.position" :zoom="15" map-type-id="terrain">
                            <GMapMarker :position="marker.position" :clickable="true" :draggable="true"
                                @dragend="dragEnd" @click="isOpened = !isOpened">
                                <GMapInfoWindow :opened="isOpened">
                                    <div>
                                        <p class="font-medium">Address: {{ form.address }}</p>
                                        <small>
                                            Coordinates: {{ this.marker.position.lat }} ,
                                            {{ this.marker.position.lng }}
                                        </small>
                                    </div>
                                </GMapInfoWindow>
                            </GMapMarker>
                        </GMapMap>
                    </div>
                    <div class="text-end mt-4">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing"> {{ this.user ? 'Update' : 'Save' }}
                            </span>
                            <span class="indicator-progress" v-if="form.processing">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
    import { Head, Link } from '@inertiajs/inertia-vue3'
    import Label from '@/Components/Label.vue'
    import Input from '@/Components/Input.vue'
    import Select2 from 'vue3-select2-component'
    import Error from '@/Components/InputError.vue'
    import Button from '@/Components/Button.vue'
    import Helpers from '@/Mixins/Helpers'
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'

    import {
        useForm
    } from '@inertiajs/inertia-vue3'

    export default {
        props: ['businessOwners', 'moduleTags'],
        components: {
            AuthenticatedLayout,
            Head,
            Link,
            Label,
            Input,
            Select2,
            Error,
            Button,
            Breadcrumbs
        },

        data() {
            return {
                form: null,
                marker: {
                    position: {
                        lat: null,
                        lng: null
                    },
                },
                isOpened: false,
                isSaved: false,
                Owners: '',
            }
        },

        methods: {
            submit() {
                this.form.latitude = this.marker.position.lat;
                this.form.longitude = this.marker.position.lng;
                this.form.post(route('dashboard.businesses.store'), {
                    errorBag: 'business',
                    preserveScroll: false,
                })
            },
            getAddressData(addressData) {
                this.form.address = addressData.formatted_address;
                this.marker.position.lat = addressData.geometry.location.lat()
                this.marker.position.lng = addressData.geometry.location.lng()
            }, 
            getUserLatLong (type) {
                var value = '';
                if (type == 'latitude') {
                    value = this.form && this.form.latitude
                        ? parseFloat(this.form.latitude) : 51.093048;
                }
                if (type == 'longitude') {
                    value = this.form && this.form.longitude
                        ? parseFloat(this.form.longitude) : 6.842120;
                }
                return value;
            },
            dragEnd(event) {
                this.marker.position.lat = event.latLng.lat();
                this.marker.position.lng = event.latLng.lng();
                new google.maps.Geocoder().geocode({
                    location: event.latLng
                }).then(response => {
                    this.form.address = response.results[0].formatted_address;
                })
                
            },
        },

        mounted() {
            this.form = useForm({
                name: '',
                email: '',
                slug: '',
                owner_id: '',
                address: '',
                latitude: '',
                longitude: '',
                minimum_purchase: '',
                module: []
            });
            this.Owners = this.businessOwners,
            this.marker.position.lat = this.getUserLatLong('latitude'),
            this.marker.position.lng = this.getUserLatLong('longitude')
        },

        mixins: [Helpers]
    }
</script>