<template>
    <div class="d-flex flex-column gap-7 gap-lg-10 bg-white me-lg-10" v-if="deliveryZones"
        :style="{width: route().current('dashboard.settings.deliveyzone.index') ? '196.12px': '57%'}">
        <div class="d-flex overflow-auto justify-content-center px-5">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-column">
                <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('dashboard.settings.deliveyzone.index')"
                        :class="{'active' : route().current('dashboard.settings.deliveyzone.index')}">
                        Location
                    </Link>
                </li>
                <li class="nav-item mt-2" v-for="deliveryZone in deliveryZones" :key="deliveryZone.id">
                    <Link class="nav-link text-active-primary ms-0 py-5 text-capitalize"
                        :href="route('dashboard.settings.deliveyzone.edit', deliveryZone.id)"
                        :class="{'active' : route().current('dashboard.settings.deliveyzone.edit',deliveryZone.id)}">
                        {{ setTypeFormat(deliveryZone.platform_delivery_type) }}
                        
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
    props: ['deliveryZones','width'],
    components: {
        Link
    },
    mixins: [Helpers]
}
</script>