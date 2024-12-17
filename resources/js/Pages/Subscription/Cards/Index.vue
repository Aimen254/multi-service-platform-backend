<template>

    <Head title="Payment Method" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Payment Method`" :path="`Subscriptions`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button v-if="checkUserPermissions('add_payment_method')" @click="openModal()"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'" />
                    </span>
                    Add Payment Method
                </button>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <SearchInput :callType="type" :searchedKeyword="searchedKeyword"/>
                </h3>
            </div>
            <div class="card-body py-3 pt-10">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-50px rounded-start">Default</th>
                                <th class="min-w-120px">Title</th>
                                <th class="min-w-120px">Brand</th>
                                <th class="min-w-120px">Last 4 Digits</th>
                                <th class="min-w-120px">Expiry Date</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="creditCards && creditCards.data.length > 0">
                            <template v-for="(card, index) in creditCards.data" :key="index">
                                <tr>
                                    <td class="px-4" style="width: 76px;">
                                        <div class="d-flex align-items-center">
                                            <label class="radio">
                                                <input class="form-check-input" type="radio" name="default"
                                                    @click="selectDefaultcard(card.id)" :checked="card.default == 1">
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column"><span
                                                class="text-dark fw-bolder text-hover-primary mb-1 fs-6">{{ ellipsis(card.user_name) }}</span><span
                                                class="text-muted d-block fs-6"><small>{{card.save_card ? '' : 'For One Time Use Only.'}}</small></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary text-capitalize"> {{ card.brand }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            {{ card.last_four }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            {{ getMonth(card.expiry_month) }} {{ card.expiry_year }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <delete-section permission="delete_payment_method"
                                                :url="route('dashboard.subscription.payment-method.destroy', card.id)" />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                    </table>
                </div>
            </div>
            <pagination :meta="cards" :keyword="searchedKeyword"/>
        </div>
    </AuthenticatedLayout>
    <CreditCardModal :stripeKey="this.stripeKey.value"></CreditCardModal>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head } from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Toggle from '@/Components/ToggleButton.vue'
import CreditCardModal from './CreditCardModal.vue'
import SearchInput from '@/Components/SearchInput.vue'
import Pagination from '@/Components/Pagination.vue'
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import moment from 'moment';

export default {
    props: ['cards', 'stripeKey', 'searchedKeyword'],

    components: {
        AuthenticatedLayout,
        Breadcrumbs,
        Head,
        InlineSvg,
        Toggle,
        CreditCardModal,
        SearchInput,
        Pagination,
        EditSection,
        DeleteSection
    },

    data() {
        return {
            creditCards: this.cards,
            type: 'card',
            showModal: false,
        }
    },

    watch: {
        cards: {
            handler(cards) {
                this.creditCards = cards
            },
            deep: true
        }
    },

    methods: {
        openModal(card = null) {
            this.emitter.emit("credit_card_model", {
                card: card
            });
        },
        selectDefaultcard(id) {
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Default Card</h1><p class='text-base'>Are you sure want to make this card default?</p>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Yes",
                customClass: {
                    confirmButton: 'danger'
                }
            }).then((result) => {
                if (result.value) {
                    this.$inertia.put(route('dashboard.subscription.payment-method.update', id), {
                        preserveScroll: false,
                        onSuccess: (result) => { },
                        onError: (errors) => { console.log(errors) }
                    })
                }
            });
        },
        getMonth(month){
            return moment(month, 'M').format('MMMM')
        }
    },
    mixins: [Helpers]
}
</script>