<template>
  <Head title="Businesses Edit" />
  <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Businesses Edit`" :path="`Businesses`" :subTitle="`Edit`"></Breadcrumbs>
        </template>
    <!-- cover, thumbnail, logo section -->
    <div class="bg-white ps-2 pt-4 rounded">
    <image-section :business="business" :token="token" :mediaLogoSizes="mediaLogoSizes" :mediaThumbnailSizes="mediaThumbnailSizes" :mediaBannerSizes="mediaBannerSizes" :mediaSecondaryBannerSizes="mediaSecondaryBannerSizes" ></image-section>
    </div>
    <!-- form section -->
    <div class="bg-white p-2 pt-4 rounded mt-5">
      <business-info-form
        :business="business"
        :businessOwner="businessOwners"
        :title="title"
      />
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import ImageSection from "@/Pages/Business/ImageSection/Form.vue";
import BusinessInfoForm from "./BusinessInfo/BusinessInfoForm";
import BusinessMenu from "./Includes/BusinessMenu.vue";
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

export default {
  components: {
    Head,
    AuthenticatedLayout,
    ImageSection,
    BusinessInfoForm,
    BusinessMenu,
    Breadcrumbs,
  },
  props: [
    "business",
    "businessOwners",
    "sideBarCategoriesListing",
    "mediaLogoSizes",
    "mediaThumbnailSizes",
    "mediaBannerSizes",
    'token',
    "mediaSecondaryBannerSizes"
  ],
  data() {
    return {
      deliveryZones: this.business,
      form: null,
      title: "Business Info",
    };
  },
  watch: {
    business: {
      handler(business) {
        this.deliveryZones = business;
      },
      deep: true,
    },
  },
  mounted() {
    this.deliveryZones = this.business;
  },
};
</script>
