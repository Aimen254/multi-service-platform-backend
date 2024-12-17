<template>
    <div class="max-h-full overflow-y-auto">
        <modal :show="show" @close="closeModal()">
            <template #title>
                Add Business
            </template>
            <template #content>
                <div class="">
                    <form v-if="form" @submit.prevent="submit" ref="form" class="">
                        <div class="px-6 py-2">
                            <div>
                                <Label for="name" value="Name" />
                                <Input id="name" type="text"
                                    :class="[form.errors.name ? 'border-red-600' : '', 'mt-1 block w-full']"
                                    v-model="form.name" autofocus autocomplete="name" placeholder="Enter name" />
                                <error :message="form.errors.name"></error>
                            </div>
                        </div>
                        <div class="px-6">
                            <div>
                                <Label for="email" value="Admin Email" />
                                <Input id="email" type="email"
                                    :class="[form.errors.email ? 'border-red-600' : '', 'mt-1 block w-full']"
                                    v-model="form.email" autofocus placeholder="Email" />
                                <error :message="form.errors.email"></error>
                            </div>
                        </div>
                        <div class="px-6 py-2">
                            <div>
                                <Label for="slug" value="Slug (https://yourpage.com/slug)" />
                                <Input id="slug" type="text"
                                    :class="[form.errors.slug ? 'border-red-600' : '', 'mt-1 block w-full']"
                                    v-model="form.slug" autofocus placeholder="Slug" />
                                <error :message="form.errors.slug"></error>
                            </div>
                        </div>
                        <div class="px-6 py-2">
                            <div>
                                <Label for="Business Owner" value="Business Owners" />
                                <select2 v-model="form.owner_id" :options="Owners"
                                    :class="[form.errors.owner_id ? 'border-red-600' : '', 'input-form mt-1 block']"
                                    placeholder="Select Business Owner">
                                </select2>
                                <error :message="form.errors.owner_id"></error>
                            </div>
                        </div>
                        <div class="px-6">
                            <div>
                                <Label for="order_notification_email" value="Order Notification Email" />
                                <Input id="order_notification_email" type="email"
                                    :class="[form.errors.order_notification_email ? 'border-red-600' : '', 'mt-1 block w-full']"
                                    v-model="form.order_notification_email" autofocus
                                    placeholder="Order Notification Email" />
                                <error :message="form.errors.order_notification_email"></error>
                            </div>
                        </div>
                        <div class="px-6 py-2">
                            <div>
                                <GMapAutocomplete
                                    class="mt-1 block w-full appearance-none rounded-none relative block px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                    placeholder="Type your business address" @place_changed="getAddressData"
                                    autocomplete="off" v-model="form.address" :value="form.address">
                                </GMapAutocomplete>
                                <error :message="form.errors.address"></error>
                                <GMapMap class="h-60 mt-2" :center="marker.position" :zoom="15" map-type-id="terrain">
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
                        </div>
                        <div
                            class="px-6 bg-gray-100 text-right bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button btnWidth="w-28 ml-auto"
                                class="group relative flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-400 hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 ml-2"
                                @click="closeModal">
                                <span class="ml-2">Cancel</span>
                            </button>
                            <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                btnWidth="w-28 ml-auto">
                                <span class="ml-2">Save</span>
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                </span>
                            </Button>
                        </div>
                    </form>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'
import Modal from '@/Components/Modal.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import Select2 from 'vue3-select2-component';
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'

export default {
    components: {
        Modal,
        Label,
        Input,
        Select2,
        Error,
        Button
    },
    
    mounted() {
        this.emitter.on('business-modal', (args) => {
             this.form = useForm({
                 name: '',
                 email: '',
                 slug: '',
                 order_notification_email: '',
                 owner_id: '',
                 address: '',
                latitude: '',
                longitude: '',
             });
             this.Owners = args.Owners,
             this.show = true,
             this.marker.position.lat = this.getUserLatLong('latitude'),
             this.marker.position.lng = this.getUserLatLong('longitude')
        })
    },
    data() {
        return {
            show: false,
            maxWidth: '2xl',
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
        closeModal() {
            this.show = false
        },
        submit() {
            this.form.latitude = this.marker.position.lat;
            this.form.longitude = this.marker.position.lng;
            this.form.post(route('dashboard.businesses.store'), {
                errorBag: 'business',
                preserveScroll: true,
                onSuccess: () => this.closeModal()
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
    }
}
</script>