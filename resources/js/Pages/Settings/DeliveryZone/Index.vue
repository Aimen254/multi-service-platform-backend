<template>
  <Head title="Edit delivery" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Delivery Settings`" :path="`Settings`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div @click="openModal()" v-if="checkUserPermissions('add_delivery_zones')"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'" />
                    </span>
                    Add Delivery Type
                </div>
            </div>
        </template>
        <div class="d-flex flex-column flex-lg-row" v-if="deliveryZone">
            <DeliveryZoneSidebar :deliveryZones="types" :width="'w-lg-820px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <Location :deliveryZone="deliveryZone" v-if="route().current('dashboard.settings.deliveyzone.index')"/>
                <Types :deliveryZone="deliveryZone" v-else />
            </div>
        </div>
        <Create/>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Helpers from "@/Mixins/Helpers";
import Toggle from "@/Components/ToggleButton.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import DeliveryZoneSidebar from '@/Components/DeliveryZoneSidebar.vue'
import Location from "./Location.vue";
import Types from "./Types.vue"
import Create from "./Create.vue";
import InlineSvg from 'vue-inline-svg'

export default {
    components: {
    AuthenticatedLayout,
    Head,
    Header,
    Link,
    Toggle,
    Breadcrumbs,
    DeliveryZoneSidebar,
    Location,
    Types,
    Create,
    InlineSvg
},

    props: ["deliveryZone",'type'],

    data() {
        return {
            form: null,
            selectedFeeType: null,
            type: null,
            types: this.type,
            isModalVisible: false,
        };
    },

    watch: {
        deliveryZone: {
            handler(deliveryZone) {
                this.deliveryZoneData = deliveryZone
            },
            deep: true
        }
    },
    watch: {
        type: {
            handler(type) {
                this.types = type
            },
            deep: true
        }
    },
    methods: {
        openModal(deliveryZone = null) {
            this.emitter.emit("delivery-type-create", {
                deliveryZone: deliveryZone,
            });
        },
        changeType(event, type) {
            switch (type) {
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
    },
  mixins: [Helpers],
};
</script>