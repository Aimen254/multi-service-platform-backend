<template>
  <div
      class="menu menu-sub menu-sub-dropdown w-250px w-md-600px "
      data-kt-menu="true"
  >
      <div class="px-7 py-5">
        <div class="fs-5 text-dark fw-bolder">Select Variants</div>
      </div>
      <div class="separator border-gray-200"></div>
      <form  @submit.prevent="submit()"> 
        <div class="px-7 py-5 product-variant-generate-scroll">
          <!--Slot-->
            <div class="mb-5">
                <label class="form-label fw-bolder">Sizes:</label>
                <div class="row p-2">
                  <div class="col-12">
                    <multiselect 
                        v-model="form.sizes"
                        :options="sizes"
                        :multiple="true"
                        placeholder="Select sizes"
                        track-by="id"
                        label="text">
                    </multiselect>
                  </div>
                </div>
            </div>
            <div class="mb-5">
                <label class="form-label fw-bolder">Colors:</label>
                <div class="row p-2">
                  <div class="col-12">
                    <multiselect 
                        v-model="form.colors"
                        :options="colors"
                        :multiple="true"
                        placeholder="Select colors"
                        track-by="id"
                        label="text">
                    </multiselect>
                  </div>
                </div>
            </div>
          <!-- slot end -->
          <div class="d-flex justify-content-end">
              <button
                type="reset"
                class="btn btn-sm btn-white btn-active-light-primary me-2"
                data-kt-menu-dismiss="false" @click="resetForm()">
                Reset
              </button>
              <button
                type="submit" class="btn btn-sm btn-primary"
                data-kt-menu-dismiss="true" :disabled="form.sizes.length == 0 && form.colors.length == 0">
                {{ form.sizes.length > 0 || form.colors.length > 0 ? 'Generate' : 'Generate All'}}
              </button>
          </div>
        </div>
      </form> 
  </div>

</template>

<script>
import Helpers from '@/Mixins/Helpers'
export default {
  props:['sizes', 'colors' , 'product'],
  data() {
    return {
        type : this.callType,
        searchedKeyword : null,
        form : {
          colors: [],
          sizes: [],
        },
        formError: '',
        isDisabled: false,
    }
  },

  methods: {

    submit(){
      this.$inertia.post(route('retail.dashboard.product.variant.auto.generate', [this.getSelectedModuleValue(), this.product.uuid]),
        this.form , {
          onSuccess: () => {
            this.resetForm()
          },
        });
    },
    resetForm(){
      this.form.colors = []
      this.form.sizes = []
    }

  },
  mixins: [Helpers]
};
</script>
