<template>
  <Head title="Businesses Settings" />
  <AuthenticatedLayout>

      <template #breadcrumbs>
          <Breadcrumbs :title="`Businesses Settings`" :path="`Businesses`" :subTitle="`Settings`"></Breadcrumbs>
      </template>
    <!-- cover, thumbnail, logo section -->
    <!-- <image-section :business="business"></image-section> -->

    <!-- form section -->
    <div class="bg-white p-2 pt-4 rounded">
      <!-- new code -->
      <form v-if="form" @submit.prevent="submit">
        <!--begin::Layout-->
        <div class="">
          <!--begin::Content-->
          <div class="ms-lg-5">
            <div class="card mb-5 mb-xl-8">
              <div class="card-body pt-10">
                <div class="row row-cols-2">
                  <div v-for="(setting, index) in form.settings" :key="index">
                    <div
                      class="col mt-5 mb-5"
                      v-if="setting.key == 'minimum_purchase'"
                    >
                      <Label value="Minimum Purchase" />
                      <Input
                      type="number"
                        :class="[
                          form.errors[`settings.${index}.value`]
                            ? 'border border-danger'
                            : '',
                        ]"
                        v-model="setting.value"
                      />
                      <error
                        :message="form.errors[`settings.${index}.value`]"
                      ></error>
                    </div>
                    <div
                      class="col mt-5 mb-5"
                      v-if="setting.key == 'global_price'"
                    >
                      <Label value="Global Price %" />
                      <Input
                        type="number"
                        step="0.5"
                        :class="[
                          form.errors[`settings.${index}.value`]
                            ? 'border border-danger'
                            : '',
                        ]"
                        v-model="setting.value"
                      />
                      <error
                        :message="form.errors[`settings.${index}.value`]"
                      ></error>
                    </div>
                    <div
                      class="col mt-5 mb-5"
                      v-if="setting.key == 'delivery_time'"
                    >
                      <Label value="Delivery Time" :class="`d-block`" />
                        <div class="p-1 d-flex" style="width:100%;">
                          <input type="number" class="form-control mx-2" v-model="delivery_time['DD']" placeholder="Day" style="width:30%;"/>
                          <input type="number" class="form-control mx-2" v-model="delivery_time['HH']" placeholder="HH" max="23"  style="width:30%;"/>
                          <input type="number" class="form-control mx-2" v-model="delivery_time['MM']" placeholder="MM" max="59"  style="width:30%;"/>
                        </div>
                      <error
                        :message="form.errors[`settings.${index}.value`]"
                      ></error>
                    </div>
                    <div
                      class="col mt-5 mb-5"
                      v-if="setting.key == 'pickup_time'"
                    >
                      <Label value="Pickup Time" :class="`d-block`" />
                      <vue-timepicker
                        v-model="setting.value"
                      ></vue-timepicker>
                      <error
                        :message="form.errors[`settings.${index}.value`]"
                      ></error>
                    </div>
                    <div class="col mt-5 mb-5" v-if="setting.key == 'tax_apply'">
                        <Label for="tax_apply" class="" value="Tax Apply" />
                        <select class="form-select text-capitalize form-select-solid"
                            :class="{'is-invalid border border-danger' : form.errors[`settings.${index}.value`]}"
                            v-model="setting.value">
                            <option class="text-capitalize" value="1">
                                Yes
                            </option>
                            <option class="text-capitalize" value="0">
                                No
                            </option>
                        </select>
                        <error :message="form.errors[`settings.${index}.value`]"></error>
                    </div>
                    <div class="col mt-5 mb-5"
                        v-if="setting.key == 'tax_percentage'">
                        <Label for="tax_type" class="" value="Tax Percentage" />
                        <Input type="number" :class="[
                            form.errors[`settings.${index}.value`]
                                ? 'border border-danger'
                                : '',]" v-model="setting.value" />
                        <error :message="form.errors[`settings.${index}.value`]"></error>
                    </div>
                  </div>
                </div>
                <div class="row mt-5">
                  <div class="col">
                    <Label value="Business Url" />
                    <Input
                      type="text"
                      :class="[
                        form.errors.businessUrl ? 'border border-danger' : '',
                      ]"
                      v-model="form.businessUrl"
                    />
                    <error :message="form.errors.businessUrl"></error>
                  </div>
                </div>
                <div class="row mt-7">
                  <div class="col">
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        value=""
                        id="flexCheckDefault"
                        :checked="form.isFeatured"
                        v-model="form.isFeatured"
                      />
                      <label class="form-check-label fw-bolder" for="flexCheckDefault">
                        Featured Business
                      </label>
                    </div>
                  </div>

                  <div class="col">
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        value=""
                        id="flexCheckDefault"
                        :checked="form.deliverable"
                        v-model="form.deliverable"
                      />
                      <label class="form-check-label fw-bolder" for="flexCheckDefault">
                        Deliverable Business
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mx-5">
                <div class="col-md-12">
                  <div class="d-flex col-md-12 justify-content-end">
                    <Button
                      type="submit"
                      :class="{ 'opacity-25': form.processing }"
                      :disabled="form.processing"
                    >
                      <span class="indicator-label" v-if="!form.processing">{{
                        this.user ? "Update" : "Save"
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
        </div>
        <!--end::Layout-->
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Error from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import BusinessMenu from "@/Pages/Business/Includes/BusinessMenu.vue";
import ImageSection from "@/Pages/Business/ImageSection/Form.vue";
import VueTimepicker from "vue3-timepicker";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

// CSS
import "vue3-timepicker/dist/VueTimepicker.css";

export default {
  props: ["business"],

  components: {
    Head,
    AuthenticatedLayout,
    Input,
    Label,
    Button,
    Error,
    BusinessMenu,
    ImageSection,
    VueTimepicker,
    Breadcrumbs,
  },

  data() {
    return {
      form: null,
      businessUuid: null,
      title: "Settings",
      delivery_time:{
        DD:0,
        HH:0,
        MM:0,
      },
    };
  },

  methods: {
    submit() {
      this.form.put(
        route("dashboard.business.settings.update", [
          this.businessUuid,
          this.businessUuid,
        ]),
        {
          preventScroll: true,
        }
      );
      
    },
  },

  mounted() {
    let deliverable = this.business.settings.find(element => element.key == 'deliverable');  
    this.form = useForm({
      settings: (this.settings = this.business.settings),
      delivery_time: (
        this.delivery_time = { 
          DD:this.business.settings[4].value.split(":")[0] == null ? 0 : this.business.settings[4].value.split(":")[0],
          HH:this.business.settings[4].value.split(":")[1] == null ? 0 : this.business.settings[4].value.split(":")[1],
          MM:this.business.settings[4].value.split(":")[2] == null ? 0 : this.business.settings[4].value.split(":")[2],
          }
        ),
      businessUrl: this.business.url,
      isFeatured: this.business.is_featured ? true : false,
      deliverable:  deliverable ? deliverable.value == 1 ? true : false : false
    });
    this.businessUuid = this.business.uuid;
    
  },
};
</script>