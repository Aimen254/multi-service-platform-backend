<template>

    <Head title="Business Delivery Settings" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Businesses Delivery Settings`" :path="`Businesses`" :subTitle="`Delivery Settings`"></Breadcrumbs>
        </template>
        <div>
            <!-- cover, thumbnail, logo section -->
            <!-- <image-section :business="business"></image-section> -->

            <!-- form section -->
            <div class="card">
                <div class="card-body p-9 pt-5">
                    <form v-if="form" @submit.prevent="submit">
                        <div class="row mb-2">
                            <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                                <Label value="Select Delivery Type" />
                                <select :disabled="deliveryFlag" v-model="form.delivery_type" class="form-select text-capitalize form-select-solid"
                                    :class="{'is-invalid border border-danger' : form.errors.first_name}"
                                    @change="changeType($event, 'delivery_type')">
                                    <option class="text-capitalize">No delivery</option>
                                    <option class="text-capitalize">Platform delivery</option>
                                    <option class="text-capitalize">Self delivery</option>
                                </select>
                                <error :message="form.errors.delivery_type"></error>
                            </div>

                            <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container" v-if="selectedType == 'platform_delivery'">
                                <Label value="Select Delivery Type" />
                                <select v-model="form.platform_delivery_type" class="form-select text-capitalize form-select-solid"
                                    :class="{'is-invalid border border-danger' : form.errors.platform_delivery_type}"
                                    @change="changeType($event, 'platform_delivery_type')">
                                    <option class="text-capitalize" v-for="type in platformType" :key="type.id">{{ removeFormat(type.platform_delivery_type) }}</option>
                                </select>
                                <error :message="form.errors.platform_delivery_type"></error>
                            </div>

                            <div v-if="selectedType == 'self_delivery'"
                                class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                                <Label for="zone_type" value="Zone Type" />
                                <select v-model="form.zone_type" @change="deliveryZoneType($event)"
                                    class="form-select form-select-solid text-capitalize"
                                    :class="{'is-invalid border border-danger' : form.errors.zone_type}">
                                    <option class="text-capitalize">circle</option>
                                    <option class="text-capitalize">polygon</option>
                                </select>
                                <error :message="form.errors.zone_type"></error>
                            </div>
                            <div v-if="selectedType == 'self_delivery'"
                                class="col-lg-12 mb-2 fv-row fv-plugins-icon-container">
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
                        <div class="d-flex mb-4 align-items-center"
                            v-if="selectedFeeType == 'delivery_fee_by_mileage' && selectedType == 'self_delivery'">
                            <Label for="mileage_fee" value="Mileage Fee of $" />
                            <div class="ps-4">
                                <Input type="number" step="0.01"
                                    :class="{'is-invalid border border-danger' : form.errors.mileage_fee}"
                                    v-model="form.mileage_fee" autofocus placeholder="Enter Mileage Fee" />
                                <error :message="form.errors.mileage_fee"></error>
                            </div>
                            <Label class="px-6" for="mileage_distance" value="for first" />
                            <div>
                                <Input type="number" step="0.01"
                                    :class="{'is-invalid border border-danger' : form.errors.mileage_distance}"
                                    v-model="form.mileage_distance" autofocus placeholder="Enter Mileage Distance" />
                                <error :message="form.errors.mileage_distance"></error>
                            </div>
                            <Label class="px-6" for="mileage_distance" value="miles" />
                        </div>
                        <div class="d-flex align-items-center"
                            v-if="selectedFeeType == 'delivery_fee_by_mileage' && selectedType == 'self_delivery'">
                            <Label for="mileage_fee" value="Mileage Fee of $" />
                            <div class="ps-4">
                                <Input type="number" step="0.01"
                                    :class="{'is-invalid border border-danger': form.errors.extra_mileage_fee}"
                                    v-model="form.extra_mileage_fee" autofocus placeholder="Enter Mileage Fee" />
                                <error :message="form.errors.extra_mileage_fee"></error>
                            </div>
                            <Label class="px-6" for="mileage_distance" value="per mile thereafter" />
                        </div>
                        <div class="d-flex align-items-center"
                            v-if="selectedFeeType == 'delivery_fee_as_percentage_of_sale' && selectedType == 'self_delivery'">
                            <Label for="mileage_fee" value="$" />
                            <div class="ps-4">
                                <Input type="number" step="0.01"
                                    :class="{'is-invalid border border-danger' : form.errors.fixed_amount}"
                                    v-model="form.fixed_amount" autofocus placeholder="Enter Fee in $" />
                                <error :message="form.errors.fixed_amount"></error>
                            </div>
                            <Label class="px-6" for="mileage_distance" value="or" />
                            <div>
                                <Input type="number"
                                    :class="{'is-invalid border border-danger' : form.errors.percentage_amount }"
                                    v-model="form.percentage_amount" autofocus placeholder="Enter Fee in %" />
                                <error :message="form.errors.percentage_amount"></error>
                            </div>
                            <Label class="px-6" for="mileage_distance"
                                value="% of each sale, whichever is greater" />
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-12 position-relative" v-if="selectedType == 'self_delivery'">
                                <GMapMap class="mt-2 map-height" ref="map" :center="marker.position" :zoom="11"
                                    map-type-id="terrain">
                                    <GMapMarker :position="marker.position" :clickable="true" :draggable="true"
                                        @dragend="dragEnd" @click="isOpened = !isOpened">
                                    </GMapMarker>
                                </GMapMap>
                                <div class="bg-white p-3 text-center position-absolute clear-btn" title="Clear"
                                    @click="clear()">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="text-end">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing" ref="submitButton">
                                    <span class="indicator-label" v-if="!form.processing"> Update </span>
                                    <span class="indicator-progress" v-if="form.processing">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <DeliveryZoneModal></DeliveryZoneModal>
    </AuthenticatedLayout>
</template>

<script>
    import {
        Head
    } from '@inertiajs/inertia-vue3'
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
    import Button from '@/Components/Button.vue'
    import BusinessMenu from '@/Pages/Business/Includes/BusinessMenu.vue'
    import ImageSection from '@/Pages/Business/ImageSection/Form.vue'
    import {
        useForm
    } from '@inertiajs/inertia-vue3'
    import Label from '@/Components/Label.vue'
    import Input from '@/Components/Input.vue'
    import Error from '@/Components/InputError.vue'
    import {
        parseZone
    } from 'moment'
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import Helpers from '@/Mixins/Helpers'


    export default {

        props: ['business', 'polygon', 'platformType', 'subscriptionDeliveryFlag'],

        components: {
            Head,
            AuthenticatedLayout,
            Button,
            BusinessMenu,
            ImageSection,
            Label,
            Error,
            Input,
            Breadcrumbs
        },

        data() {
            return {
                form: null,
                selectedType: null,
                selectedFeeType: null,
                type: 'circle',
                businessData: this.business,
                polygonData: this.polygon,
                marker: {
                    position: {
                        lat: 51.093048,
                        lng: 6.842120
                    },
                },
                drawingManager: null,
                poly: null,
                circle: null,
                deliveryFlag: this.subscriptionDeliveryFlag ? false : true
            }
        },

        methods: {
            submit() {
                if (this.deliveryFlag) {
                    this.$notify({
                        group: "toast",
                        type: "error",
                        text: 'Upgrade Subscription to Unlock Delivery Options.',
                    },3000); // 3s
                } else {
                    this.form.platform_delivery_type = this.form.platform_delivery_type ? this.setTypeFormat(this.form.platform_delivery_type) : null
                    this.form.put(route('retail.dashboard.business.deliveryzones.update', [this.getSelectedModuleValue(), this.business.uuid, this.business.delivery_zone.id
                    ]), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.form.platform_delivery_type = this.form.platform_delivery_type ? this.removeFormat(this.form.platform_delivery_type) : null
                            },
                        }
                    )
                }
            },
            getUserLatLong(type, business) {
                var value = '';
                if (type == 'latitude') {
                    value = business && business.latitude ?
                        parseFloat(business.latitude) : 51.093048;
                }
                if (type == 'longitude') {
                    value = business && business.longitude ?
                        parseFloat(business.longitude) : 6.842120;
                }
                return value;
            },
            savePolygon(polygon) {
                this.form.polygon = polygon
            },
            saveCircle(radius, center) {
                this.form.radius = radius
                this.form.center = center
            },
            clear() {
                this.savePolygon([]);
                if (this.poly != null) {
                    this.poly.setMap(null)
                }
                this.saveCircle(null, null)
                if (this.circle != null) {
                    this.circle.setMap(null)
                }
                this.deliveryZoneType()
            },
            deliveryZoneType(event = null) {
                if (event != null) {
                    this.type = event.target.value
                }
                const map = this.$refs.map.$mapObject
                let self = this

                if (this.form.polygon.length > 0 && this.type == 'polygon') {
                    self.saveCircle(null, null)
                    if (this.circle != null) {
                        this.circle.setMap(null)
                    }
                    this.poly = new window.google.maps.Polygon({
                        paths: this.form.polygon,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillOpacity: 0.35,
                        editable: true
                    })
                    this.poly.setMap(map)
                    this.mapEvents(this.poly.getPath())
                } else if (this.form.radius && this.type == 'circle') {
                    this.savePolygon([]);
                    if (this.poly != null) {
                        this.poly.setMap(null)
                    }
                    if (this.circle != null) {
                        this.circle.setMap(null)
                    }
                    this.circle = new google.maps.Circle({
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillOpacity: 0.35,
                        center: this.form.center,
                        radius: parseFloat(this.form.radius),
                        editable: true
                    });
                    this.circle.setMap(map)
                    this.mapEvents(this.circle)
                } else {
                    if (this.drawingManager != null) {
                        this.drawingManager.setOptions({
                            drawingControl: false
                        })
                    }
                    this.savePolygon([]);
                    if (this.poly != null) {
                        this.poly.setMap(null)
                    }
                    this.saveCircle(null, null)
                    if (this.circle != null) {
                        this.circle.setMap(null)
                    }
                    this.drawingManager = new window.google.maps.drawing.DrawingManager({
                        drawingControl: true,
                        drawingControlOptions: {
                            position: window.google.maps.ControlPosition.TOP_CENTER,
                            drawingModes: this.type == "polygon" ? [
                                window.google.maps.drawing.OverlayType.POLYGON,
                            ] : [window.google.maps.drawing.OverlayType.CIRCLE]
                        },
                        polygonOptions: {
                            editable: true
                        },
                        circleOptions: {
                            editable: true
                        }
                    });
                    this.drawingManager.setMap(map)
                    window.google.maps.event.addListener(this.drawingManager, 'overlaycomplete', function (event) {
                        self.drawingManager.setDrawingMode(null);
                        self.drawingManager.setOptions({
                            drawingControl: false
                        })
                        if (self.type == 'polygon') {
                            self.poly = event.overlay
                            var paths = event.overlay.getPath()
                            var polygon = self.polygonPaths(paths)
                            self.mapEvents(paths)
                            self.savePolygon(polygon);
                        } else {
                            self.circle = event.overlay
                            var radius = event.overlay.getRadius()
                            var latitude = self.circle.getCenter().lat();
                            var longitude = self.circle.getCenter().lng();
                            var center = {
                                lat: latitude,
                                lng: longitude
                            }
                            self.saveCircle(radius, center)
                            self.mapEvents(self.circle)
                        }
                    });
                }
            },
            polygonPaths(paths) {
                let polygon = []
                for (let i = 0; i < paths.getLength(); i++) {
                    const xy = paths.getAt(i);
                    polygon.push({
                        lat: xy.lat(),
                        lng: xy.lng()
                    })
                }
                return polygon
            },
            mapEvents(zone) {
                const self = this
                let polygon = []
                if (this.type == 'circle') {
                    window.google.maps.event.addListener(zone, 'radius_changed', function () {
                        var radius = this.getRadius()
                        var latitude = this.getCenter().lat();
                        var longitude = this.getCenter().lng();
                        var center = {
                            lat: latitude,
                            lng: longitude
                        }
                        self.saveCircle(radius, center)
                    })
                } else {
                    window.google.maps.event.addListener(zone, 'set_at', function () {
                        polygon = self.polygonPaths(parseZone)
                        self.savePolygon(polygon);
                    })
                    window.google.maps.event.addListener(zone, 'insert_at', function () {
                        polygon = self.polygonPaths(zone)
                        self.savePolygon(polygon);
                    })
                    window.google.maps.event.addListener(zone, 'remove_at', function () {
                        polygon = self.polygonPaths(zone)
                        self.savePolygon(polygon);
                    })
                }
            },
            changeType(event, type) {
                switch (type) {
                    case 'delivery_type':
                        this.selectedType = this.setTypeFormat(event.target.value)
                        if (this.selectedType == 'self_delivery') {
                            setTimeout(() => {
                                // this.deliveryZoneType()
                            }, 100)
                        }
                        break;

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
            removeFormat(type){
                return type.toLowerCase().split("_").join(" ");
            },
        },
        watch: {
            business: {
                handler(business) {
                    this.businessData = business
                },
                deep: true
            }
        },
        watch: {
            polygon: {
                handler(polygon) {
                    this.polygonData = polygon
                },
                deep: true
            }
        },

        mounted() {
            this.marker.position.lat = this.getUserLatLong('latitude', this.businessData);
            this.marker.position.lng = this.getUserLatLong('longitude', this.businessData);
            let latitude = this.businessData.delivery_zone.latitude ? this.business.delivery_zone.latitude : this.marker
                .position.lat
            let longitude = this.businessData.delivery_zone.longitude ? this.business.delivery_zone.longitude : this
                .marker.position.lng
            this.form = useForm({
                delivery_type: this.businessData.delivery_zone.delivery_type,
                zone_type: this.businessData.delivery_zone.zone_type,
                fee_type: this.businessData.delivery_zone.fee_type ? this.businessData.delivery_zone.fee_type : null,
                mileage_fee: this.businessData.delivery_zone.mileage_fee,
                extra_mileage_fee: this.businessData.delivery_zone.extra_mileage_fee,
                mileage_distance: this.businessData.delivery_zone.mileage_distance,
                percentage_amount: this.businessData.delivery_zone.percentage_amount,
                fixed_amount: this.businessData.delivery_zone.fixed_amount,
                center: {
                    lat: latitude,
                    lng: longitude
                },
                radius: this.businessData.delivery_zone.radius ? this.businessData.delivery_zone.radius : null,
                polygon: this.polygonData ? this.polygonData : [],
                platform_delivery_type: this.businessData.delivery_zone.platform_delivery_type ? this.removeFormat(this.businessData.delivery_zone.platform_delivery_type) : null
            });         
            this.selectedType = this.setTypeFormat(this.form.delivery_type);
            this.selectedFeeType = this.form.fee_type ?
                this.setTypeFormat(this.form.fee_type) : null;
            this.type = this.businessData.delivery_zone.zone_type ? this.businessData.delivery_zone.zone_type : 'circle'
            if (this.selectedType === 'self_delivery') {
                setTimeout(() => {
                    this.deliveryZoneType()
                }, 3000)
            }
        },
        mixins: [Helpers],
    }
</script>