<template>
    <div class="d-flex flex-column gap-7 gap-lg-10 w-25 bg-white me-lg-10"
        :class="width">
        <div class="d-flex overflow-auto justify-content-center px-5">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-column">
                <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.business.products.edit', [getSelectedModuleValue(), getSelectedBusinessValue(), product.uuid])"
                        :class="{'active' : route().current('retail.dashboard.business.products.edit')}">
                        <i class="fas fa-edit px-2 fs-2"></i> Basic Information
                    </Link>
                </li>
                <li class="nav-item mt-2" v-if="checkUserPermissions('view_product_images')">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.product.media.index', [getSelectedModuleValue(), product.uuid])"
                        :class="{'active' : route().current('retail.dashboard.product.media.index')}">
                        <i class="far fa-images px-2 fs-2"></i> Images
                    </Link>
                </li>
                <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.product.product-tags.index', [getSelectedModuleValue(), product.uuid])"
                        :class="{'active' : route().current('retail.dashboard.product.product-tags.index')}">
                        <i class="fas fa-tags px-2 fs-2"></i> Assign Tags
                    </Link>
                </li>
                <li class="nav-item mt-2"  v-if="checkUserPermissions('view_attributes')">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.product.attribute-tags.index', [getSelectedModuleValue(), product.uuid])"
                        :class="{'active' : route().current('retail.dashboard.product.attribute-tags.index')}">
                        <i class="fas fa-table px-2 fs-2"></i> Attribute Tags
                    </Link>
                </li>
                <li class="nav-item mt-2"  v-if="checkUserPermissions('view_product_variants')">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.product.variants.index', [getSelectedModuleValue(), product.uuid])"
                        :class="{'active' : route().current('retail.dashboard.product.variants.*')}">
                        <i class="fas fa-table px-2 fs-2"></i> Variants
                    </Link>
                </li>
                

                <li class="nav-item mt-2" v-if="checkUserPermissions('view_product_coupons')">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.product.coupons.index', [getSelectedModuleValue(), product.uuid])"
                        :class="{'active' : route().current('retail.dashboard.product.coupons.*')}">
                        <i class="fas fa-tag px-2 fs-2"></i> Coupons
                    </Link>
                </li>
                <li class="nav-item mt-2" v-if="checkUserPermissions('view_product_coupons')">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.product.discount', [getSelectedModuleValue(), product.uuid])"
                        :class="{'active' : type == 'discount'}">
                        <i class="fas fa-tags px-2 fs-2"></i> Discount
                    </Link>
                </li>
                <li class="nav-item mt-2 mb-5">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.product.taxes.index', [getSelectedModuleValue(), product.uuid])"
                        :class="{'active' : route().current('retail.dashboard.product.taxes.*')}">
                        <i class="fas fa-tag px-2 fs-2"></i> Product Tax
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers';

export default {
    props: ['product', 'width', 'type'],
    components: {
        Link
    },
    mixins: [Helpers]
}
</script>