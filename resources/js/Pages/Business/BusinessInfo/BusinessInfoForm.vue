<template>
  <form v-if="form" @submit.prevent="submit">
    <!-- new code -->
    <!--begin::Layout-->
    <!--begin::Content-->
    <div class="ms-lg-5">
      <div class="card mb-5 mb-xl-8">
        <div class="card-body">
          <!-- <image-section :business="business"></image-section> -->
          <h2 class="mb-10">Business Info</h2>
          <div class="row mb-4">
            <div class="col-md-6">
              <Label for="name" value="Name" />
              <Input id="name" type="text" :class="[form.errors.name ? 'border border-danger' : '']" :value="form.name"
                v-model="form.name" autofocus autocomplete="name" placeholder="Enter name" />
              <error :message="form.errors.name"></error>
            </div>
            <div class="col-md-6">
              <Label for="slug" value="Slug (https://yourpage.com/slug)" />
              <Input id="slug" type="text" :class="[form.errors.slug ? 'border border-danger' : '']" :value="form.slug"
                v-model="form.slug" autofocus autocomplete="slug" placeholder="Enter slug" />
              <error :message="form.errors.slug"></error>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-6">
              <Label for="email" class="required" value="Admin Email" />
              <Input id="email" type="email" :class="[form.errors.email ? 'border border-danger' : '']"
                v-model="form.email" autofocus autocomplete="email" placeholder="Enter email" />
              <error :message="form.errors.email"></error>
            </div>
            <div class="col-md-6">
              <Label for="Business Owner" value="Business Owners" />
              <select2 v-model="form.owner_id" :options="businessOwner" class="rounded"
                :class="[form.errors.owner_id ? 'border border-danger' : '']" placeholder="Select Business Owner">
              </select2>
              <error :message="form.errors.owner_id"></error>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-6">
              <Label for="order_notification_email" value="Order Notification Email" />
              <Input id="order_notification_email" type="text"
                :class="[form.errors.order_notification_email ? 'border border-danger' : '']"
                :value="form.order_notification_email" v-model="form.order_notification_email" autofocus
                autocomplete="order_notification_email" placeholder="Enter Notification Email" />
              <error :message="form.errors.order_notification_email"></error>
            </div>
            <div class="col-md-6">
              <Label for="phone" class="required" value="Phone Number" />
              <Input id="phone" type="text" :class="[form.errors.phone ? 'border border-danger' : '']" :value="form.phone"
                v-model="form.phone" autofocus autocomplete="phone" placeholder="Enter Phone Number" />
              <error :message="form.errors.phone"></error>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-6">
              <Label for="mobile" value="Mobile Number" />
              <Input id="mobile" type="text" :class="[form.errors.mobile ? 'border border-danger' : '']"
                :value="form.mobile" v-model="form.mobile" autofocus autocomplete="mobile"
                placeholder="Enter Mobile Number" />
              <error :message="form.errors.mobile"></error>
            </div>
            <div class="col-md-6">
              <Label for="short_description" value="Business Short Description" />
              <Input id="short_description" type="text" :class="[
                form.errors.short_description ? 'border border-danger' : '',
              ]" :value="form.short_description" v-model="form.short_description" autofocus
                autocomplete="short_description" placeholder="Enter Business Short Description" />
              <error :message="form.errors.short_description"></error>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-12">
              <Label for="message" value="Business Message" />
              <Input id="message" type="text" :class="[form.errors.message ? 'border border-danger' : '']"
                :value="form.message" v-model="form.message" autofocus autocomplete="message"
                placeholder="Enter Business Message" />
              <error :message="form.errors.message"></error>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-6">
              <Label class="required" for="shipping_and_return_policy_short">
                Shipping and return Policy <small>(Short)</small>
              </Label>
              <textarea class="form-control form-control-lg form-control-solid"
                v-model="form.shipping_and_return_policy_short" :class="[
                  form.errors.shipping_and_return_policy_short
                    ? 'border border-danger'
                    : '',
                ]" rows="4" placeholder="Shipping and return Policy"></textarea>
              <error :message="form.errors.shipping_and_return_policy_short"></error>
            </div>
            <div class="col-md-6">
              <Label for="long_description" value="Business Long Description" />
              <textarea class="form-control form-control-lg form-control-solid" v-model="form.long_description" :class="[
                form.errors.long_description ? 'border border-danger' : '',
              ]" rows="4" placeholder="Business Long Description"></textarea>
              <error :message="form.errors.long_description"></error>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-12">
              <Label class="required" for="shipping_and_return_policy">
                Shipping and return Policy <small>(long)</small>
              </Label>
              <textarea class="form-control form-control-lg form-control-solid" v-model="form.shipping_and_return_policy"
                :class="[
                  form.errors.shipping_and_return_policy
                    ? 'border border-danger'
                    : '',
                ]" rows="4" placeholder="Shipping and return Policy"></textarea>
              <error :message="form.errors.shipping_and_return_policy"></error>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-12">
              <GMapAutocomplete class="
                    form-control form-control-lg form-control-solid
                    mt-10
                    mb-10
                  " :class="[
                    form.errors.address
                      ? 'border border-danger'
                      : '',
                  ]" placeholder="Type your address" @place_changed="getAddressData" autocomplete="off"
                v-model="form.address" :value="form.address">
              </GMapAutocomplete>
              <error :message="form.errors.address"></error>
              <GMapMap class="map-height-normal mt-4" :center="marker.position" :zoom="15" map-type-id="terrain">
                <GMapMarker :position="marker.position" :clickable="true" :draggable="true" @dragend="dragEnd"
                  @click="isOpened = !isOpened">
                  <GMapInfoWindow :opened="isOpened">
                    <div>
                      <p class="">Address: {{ form.address }}</p>
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
        </div>
        <div class="row mx-5">
          <div class="col-md-12 ">
            <div class="d-flex col-md-12  justify-content-end">
              <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <span class="indicator-label" v-if="!form.processing">{{
                  form.id ? "Update" : "Save"
                }}</span>
                <span class="indicator-progress" v-if="form.processing">
                  <span class="spinner-border spinner-border-sm align-middle"></span>
                </span>
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--end::Content-->
    <!--end::Layout-->
  </form>
</template>

<script>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Error from "@/Components/InputError.vue";
import Helpers from "@/Mixins/Helpers";
import Select2 from "vue3-select2-component";
import { useForm } from "@inertiajs/inertia-vue3";
import ImageSection from "@/Pages/Business/ImageSection/Form.vue";

export default {
  props: ["business", "businessOwner", "title"],
  components: {
    Input,
    Label,
    Button,
    Error,
    Select2,
    ImageSection,
  },
  data() {
    return {
      form: null,
      marker: {
        position: {
          lat: 51.093048,
          lng: 6.84212,
        },
      },
      isOpened: false,
    };
  },
  methods: {
    submit() {
      this.form.latitude = this.marker.position.lat;
      this.form.longitude = this.marker.position.lng;
      this.form.put(route("retail.dashboard.businesses.update", [this.getSelectedModuleValue(), this.form.id]), {
        preventScroll: true,
      });
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
    getUserLatLong(type) {
      var value = "";
      if (type == "latitude") {
        value =
          this.business && this.business.latitude
            ? parseFloat(this.business.latitude)
            : 51.093048;
      }
      if (type == "longitude") {
        value =
          this.business && this.business.longitude
            ? parseFloat(this.business.longitude)
            : 6.84212;
      }
      return value;
    },
  },
  mounted() {
    this.form = useForm({
      id: this.business ? this.business.uuid : "",
      owner_id: this.business ? this.business.owner_id : "",
      order_notification_email: this.business
        ? this.business.order_notification_email
        : "",
      email: this.business ? this.business.email : "",
      name: this.business ? this.business.name : "",
      slug: this.business ? this.business.slug : "",
      address: this.business ? this.business.address : "",
      latitude: this.business ? this.business.latitude : "",
      longitude: this.business ? this.business.longitude : "",
      phone: this.business ? this.business.phone : "",
      mobile: this.business ? this.business.mobile : "",
      short_description: this.business ? this.business.short_description : "",
      long_description: this.business ? this.business.long_description : "",
      shipping_and_return_policy: this.business
        ? this.business.shipping_and_return_policy
        : "",
      shipping_and_return_policy_short: this.business
        ? this.business.shipping_and_return_policy_short
        : "",
      message: this.business ? this.business.message : "",
      group: "business_info",
    });
    this.marker.position.lat = this.getUserLatLong("latitude");
    this.marker.position.lng = this.getUserLatLong("longitude");
  },
  mixins: [Helpers],
};
</script>
