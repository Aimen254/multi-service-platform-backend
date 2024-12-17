<template>
    <div class="d-flex flex-column gap-7 gap-lg-10 w-25 bg-white me-lg-10 pb-5" :class="width">

        <div class="d-flex justify-content-center px-5">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-column">
                <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('events.dashboard.events.edit', [getSelectedModuleValue(), events.uuid])"
                        :class="{ 'active': route().current('events.dashboard.events.edit') }">
                    <i class="fas fa-edit px-2 fs-2"></i> Basic Information
                    </Link>
                </li>

                <li class="nav-item mt-2" v-if="checkUserPermissions('view_product_images')">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('events.dashboard.events.media.index', [getSelectedModuleValue(), events.uuid])"
                        :class="{ 'active': route().current('events.dashboard.events.media.index') }">
                    <i class="far fa-images px-2 fs-2"></i> Images
                    </Link>

                </li>
                <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('events.dashboard.events.tags.index', [getSelectedModuleValue(), events.uuid])"
                        :class="{ 'active': route().current('events.dashboard.events.tags.index') }">
                    <i class="fas fa-tags px-2 fs-2"></i> Assign Tags
                    </Link>
                </li>
                <li class="nav-item mt-2" v-if="checkUserPermissions('view_attributes')">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('events.dashboard.events.attribute-tags.index', [getSelectedModuleValue(), events.uuid])"
                        :class="{ 'active': route().current('events.dashboard.events.attribute-tags.index') }">
                    <i class="fas fa-table px-2 fs-2"></i> Attribute Tags
                    </Link>
                </li>

            </ul>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import Helpers from '@/Mixins/Helpers';

export default {
    props: ['events', 'width'],
    components: {
        Link
    },

    mixins: [Helpers]
}
</script>
