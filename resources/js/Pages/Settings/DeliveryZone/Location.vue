<template>
    <form class="form" v-if="form" @submit.prevent="submit">
        <div class="card card-flush py-4">
            <div class="card-body py-3 row">
                <div class="row mb-2">
                    <div class="col-lg-12 fv-row mb-2 fv-plugins-icon-container">
                        <Label for="location" value="Location" />
                        <GMapAutocomplete class="form-control form-control-lg form-control-solid" 
                            :class="{'is-invalid border border-danger' : form.errors.address }"
                            placeholder="Enter your location" @place_changed="getAddressData" autocomplete="off"
                            v-model="form.address" :value="form.address">
                        </GMapAutocomplete>
                        <error :message="form.errors.address"></error>
                    </div>
                    <div class="col-lg-12 fv-row mb-2 fv-plugins-icon-container">
                        <Label for="zone_type" value="Zone Type" />
                        <select v-model="form.zone_type" @change="deliveryZoneType($event)"
                            class="form-select form-select-solid text-capitalize"
                            :class="{'is-invalid border border-danger' : form.errors.zone_type}">
                            <option class="text-capitalize">circle</option>
                            <option class="text-capitalize">polygon</option>
                        </select>
                        <error :message="form.errors.zone_type"></error>
                    </div>
                    <div class="row mt-2" >
                        <error :message="form.errors.radius"></error>
                        <error :message="form.errors.polygon"></error>
                        <div class="col-lg-12 position-relative">
                            <GMapMap class="mt-2 map-height" ref="map" :center="marker.position" :zoom="11"
                                map-type-id="terrain">
                                <GMapMarker :position="marker.position" :clickable="true" :draggable="true"
                                    @dragend="dragEnd" @click="isOpened = !isOpened">
                                </GMapMarker>
                            </GMapMap>
                            <div class="bg-white p-3 text-center cursor-pointer position-absolute clear-btn" title="Clear"
                                @click="clear()">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
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
    </form>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import InlineSvg from "vue-inline-svg";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Button from '@/Components/Button.vue'
import Error from '@/Components/InputError.vue'
import {useForm} from '@inertiajs/inertia-vue3'
import Helpers from "@/Mixins/Helpers";

export default {
    components: {
        Link,
        InlineSvg,
        Button,
        Label,
        Input,
        Error,
    },
    props: ['deliveryZone'],
    data(){
        return {
            deliveryZoneData:this.deliveryZone,
            marker: {
                position: {
                    lat: 51.093048,
                    lng: 6.842120
                },
            },
            form: null,
            drawingManager: null,
            poly: null,
            circle: null,
        }
    },
    methods:{
        submit() {
            this.form.put(route('dashboard.settings.deliveyzone.update', [this.form.id]), {
                preserveScroll: false
            })
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
        getAddressData(addressData) {
            this.form.address = addressData.formatted_address;
            this.marker.position.lat = addressData.geometry.location.lat();
            this.marker.position.lng = addressData.geometry.location.lng();
        },
        dragEnd(event) {
            this.marker.position.lat = event.latLng.lat();
            this.marker.position.lng = event.latLng.lng();
            new google.maps.Geocoder()
                .geocode({
                    location: event.latLng,
                })
                .then((response) => {
                    this.form.address = response.results[0].formatted_address;
                });
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
            if(polygon.length > 0) {
                let latitudes = []
                let longitudes = []
                polygon.forEach((value, index) => {
                    latitudes.push(value.lat)
                    longitudes.push(value.lng)
                });
                latitudes.sort()
                longitudes.sort()
                const lowX = latitudes[0];
                const highX = latitudes[latitudes.length - 1];
                const lowy = longitudes[0];
                const highy = longitudes[latitudes.length - 1];
                const centerX = lowX + ((highX - lowX) / 2);
                const centerY = lowy + ((highy - lowy) / 2);
                let center = {lat: centerX, lng: centerY}
                this.form.center = center
            }
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
                    polygon = self.polygonPaths(zone)
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
    },
    mounted(){
        if(this.deliveryZone){
            this.marker.position.lat = this.getUserLatLong('latitude', this.deliveryZoneData);
            this.marker.position.lng = this.getUserLatLong('longitude', this.deliveryZoneData);
            let latitude = this.deliveryZoneData.latitude ? this.deliveryZoneData.latitude : this.marker
                    .position.lat
            let longitude = this.deliveryZoneData.longitude ? this.deliveryZoneData.longitude : this.marker
                    .position.lng
            this.form = useForm({
                id: this.deliveryZoneData.id,
                zone_type: this.deliveryZoneData.zone_type,
                address: this.deliveryZoneData.address,
                center: {
                    lat: latitude,
                    lng: longitude
                },
                radius: this.deliveryZoneData.radius ? this.deliveryZoneData.radius : null,
                polygon: this.deliveryZoneData.polygon ? JSON.parse(this.deliveryZoneData.polygon) : [],
                location: true
            });
            this.type = this.deliveryZoneData.zone_type ? this.deliveryZoneData.zone_type : 'circle';
            setTimeout(() => {
                this.deliveryZoneType()
            }, 1000)
        }
    },
    mixins: [Helpers],
};
</script>