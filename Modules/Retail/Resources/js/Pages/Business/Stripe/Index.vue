<template>
  <Head title="Connect Stripe" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
      <Breadcrumbs
        :title="`Businesses Settings`"
        :path="`Businesses`"
        :subTitle="`Stripe`"
      ></Breadcrumbs>
    </template>
    <!-- cover, thumbnail, logo section -->
    <!-- <image-section :business="business"></image-section> -->

    <div class="bg-white p-2 pt-4 rounded">
      <!-- new code -->
      <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">
          <!--begin::Card-->
          <div class="card">
            <!--begin::Card body-->
            <div class="card-body">
              <!--begin::Heading-->
              <div class="card-px text-center pt-15">
                <!--begin::Title-->
                <h2 class="fs-2x fw-bolder mb-0">Stripe Connect
                    <span v-if="stripeConnected && bankAccount" class="svg-icon svg-icon-1 svg-icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                            <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF" />
                            <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white" />
                        </svg>
                    </span>
                    </h2>
                <!--end::Title-->
                <!--begin::Description-->
                <p class="text-gray-400 fs-4 fw-bold py-7" v-if="!stripeConnected">
                  Click on the below buttons to connect <br />stripe account.
                </p>
                <!--end::Description-->
                <!--begin::Action-->
                <a 
                  :href="route('retail.dashboard.business.redirect.stripe', [getSelectedModuleValue(), business.uuid])"
                  target="_blank"
                  class="btn btn-primary er fs-6 px-8 py-4"
                  v-if="!stripeConnected"
                  >Connect Stripe Account</a
                >
                <p class="text-gray-400 fs-4 fw-bold py-7" v-if="stripeConnected">
                  Stripe account connected. <span v-if="bankAccount">Bank account ending with {{bankAccount.last4}}.</span>
                  <span v-if="!bankAccount" class="text-danger">Please add bank account and fee below </span>
                </p>
                <!--end::Action-->
              </div>
              <!--end::Heading-->
              <!--begin::Illustration-->

              <!--end::Illustration-->
            </div>
            <!--end::Card body-->
          </div>
          <!--end::Card-->
          <div class="card" v-if="stripeConnected">
            <div class="card-body p-9 pt-2">
                <form v-if="form" @submit.prevent="submit" ref="form">
                    <div class="row mb-2">
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="name" class="required" value="Account holder name" />
                            <Input id="name" type="text"
                                :class="{'is-invalid border border-danger' : form.errors.account_holder_name}" v-model="form.account_holder_name"
                                autofocus autocomplete="name" placeholder="Account holder name" />
                            <error :message="form.errors.account_holder_name"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="email" class="required" value="Account number"/>
                            <Input id="email" type="text"
                                :class="{'is-invalid border border-danger' : form.errors.account_number}" v-model="form.account_number"
                                autofocus placeholder="Account number" />
                            <error :message="form.errors.account_number"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="slug" class="required" value="Country" />
                            <Input id="slug" type="text"
                                :class="{'is-invalid border border-danger' : form.errors.country}" v-model="form.country"
                                autofocus placeholder="Country" />
                            <error :message="form.errors.country"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="minimum_purchase" class="required" value="Routing number/Sort code" />
                            <Input id="minimum_purchase" type="text"
                                :class="{'is-invalid border border-danger' : form.errors.routing_number}"
                                v-model="form.routing_number" autofocus
                                placeholder="Routing number/Sort code" />
                            <error :message="form.errors.routing_number"></error>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing"> {{ this.user ? 'Update' : 'Save' }}
                            </span>
                            <span class="indicator-progress" v-if="form.processing">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
        </div>
        <!--end::Container-->
      </div>
    </div>

    <div class="card mt-9" id="kt_profile_details_view" v-if="this.$page.props.auth.user.completed_stripe_onboarding && this.$page.props.auth.user.stripe_bank_id">
      <div class="card-header cursor-pointer">
          <div class="card-title m-0">
            <h3 class="fw-bold m-0">
                <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-1">{{stripeAccount.individual.first_name}} {{stripeAccount.individual.last_name}}</a>
                <a href="#">
                    <span class="svg-icon svg-icon-1 svg-icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                            <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF" />
                            <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white" />
                        </svg>
                    </span>
                </a>
            </h3>
          </div>
          <div class="card-title m-0">
            <select class="form-select form-select-solid align-self-center" name="category" v-model="payoutSchedule" @change="changePayoutSchedule($event)">
              <option value="null">Change Payout Interval</option>
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
      </div>
      <div class="card-body p-9">
        <div class="row">
          <div class="col-lg-6">
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Location</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">{{stripeAccount.country}}</span></div>
            </div>
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Email</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">{{stripeAccount.email}}</span></div>
            </div>
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Bank Name</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">{{stripeAccount.external_accounts.data[0].bank_name}}  </span></div>
            </div>
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Business Type</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">{{stripeAccount.business_type}}  </span></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Verification Status</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">{{stripeAccount.individual.verification.status}}  </span></div>
            </div>
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Payout Type</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">Automatic</span></div>
            </div>
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Payout Delay Days</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">{{stripeAccount.settings.payouts.schedule.delay_days}}  </span></div>
            </div>
            <div class="row mb-7">
              <label class="col-lg-4 fw-semobold text-muted">Payout Interval</label>
              <div class="col-lg-8"><span class="fw-bold fs-6 text-dark">{{stripeAccount.settings.payouts.schedule.interval}}  </span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-5 g-xl-8 mt-3" v-if="this.$page.props.auth.user.completed_stripe_onboarding && this.$page.props.auth.user.stripe_bank_id">
      <div class="col-xl-4">
          <a href="#" class="card-xl-stretch mb-xl-8 bg-danger card hoverable">
            <div class="card-body">
                <span class="svg-icon-white svg-icon svg-icon-3x mx-n1">
                  <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                      <path xmlns="http://www.w3.org/2000/svg" d="M21 10H13V11C13 11.6 12.6 12 12 12C11.4 12 11 11.6 11 11V10H3C2.4 10 2 10.4 2 11V13H22V11C22 10.4 21.6 10 21 10Z" fill="currentColor"></path>
                      <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M12 12C11.4 12 11 11.6 11 11V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V11C13 11.6 12.6 12 12 12Z" fill="currentColor"></path>
                      <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M18.1 21H5.9C5.4 21 4.9 20.6 4.8 20.1L3 13H21L19.2 20.1C19.1 20.6 18.6 21 18.1 21ZM13 18V15C13 14.4 12.6 14 12 14C11.4 14 11 14.4 11 15V18C11 18.6 11.4 19 12 19C12.6 19 13 18.6 13 18ZM17 18V15C17 14.4 16.6 14 16 14C15.4 14 15 14.4 15 15V18C15 18.6 15.4 19 16 19C16.6 19 17 18.6 17 18ZM9 18V15C9 14.4 8.6 14 8 14C7.4 14 7 14.4 7 15V18C7 18.6 7.4 19 8 19C8.6 19 9 18.6 9 18Z" fill="currentColor"></path>
                  </svg>
                </span>
                <div class="text-inverse-danger fw-bold fs-2 mb-2 mt-5">Available Amount</div>
                <div class="text-inverse-danger fw-semobold fs-7">${{stripeBalance.available[0].amount/100}} USD</div>
            </div>
          </a>
      </div>
      <div class="col-xl-4">
          <a href="#" class="card-xl-stretch mb-xl-8 bg-primary card hoverable">
            <div class="card-body">
                <span class="svg-icon-white svg-icon svg-icon-3x mx-n1">
                  <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                      <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M18 21.6C16.3 21.6 15 20.3 15 18.6V2.50001C15 2.20001 14.6 1.99996 14.3 2.19996L13 3.59999L11.7 2.3C11.3 1.9 10.7 1.9 10.3 2.3L9 3.59999L7.70001 2.3C7.30001 1.9 6.69999 1.9 6.29999 2.3L5 3.59999L3.70001 2.3C3.50001 2.1 3 2.20001 3 3.50001V18.6C3 20.3 4.3 21.6 6 21.6H18Z" fill="currentColor"></path>
                      <path xmlns="http://www.w3.org/2000/svg" d="M12 12.6H11C10.4 12.6 10 12.2 10 11.6C10 11 10.4 10.6 11 10.6H12C12.6 10.6 13 11 13 11.6C13 12.2 12.6 12.6 12 12.6ZM9 11.6C9 11 8.6 10.6 8 10.6H6C5.4 10.6 5 11 5 11.6C5 12.2 5.4 12.6 6 12.6H8C8.6 12.6 9 12.2 9 11.6ZM9 7.59998C9 6.99998 8.6 6.59998 8 6.59998H6C5.4 6.59998 5 6.99998 5 7.59998C5 8.19998 5.4 8.59998 6 8.59998H8C8.6 8.59998 9 8.19998 9 7.59998ZM13 7.59998C13 6.99998 12.6 6.59998 12 6.59998H11C10.4 6.59998 10 6.99998 10 7.59998C10 8.19998 10.4 8.59998 11 8.59998H12C12.6 8.59998 13 8.19998 13 7.59998ZM13 15.6C13 15 12.6 14.6 12 14.6H10C9.4 14.6 9 15 9 15.6C9 16.2 9.4 16.6 10 16.6H12C12.6 16.6 13 16.2 13 15.6Z" fill="currentColor"></path>
                      <path xmlns="http://www.w3.org/2000/svg" d="M15 18.6C15 20.3 16.3 21.6 18 21.6C19.7 21.6 21 20.3 21 18.6V12.5C21 12.2 20.6 12 20.3 12.2L19 13.6L17.7 12.3C17.3 11.9 16.7 11.9 16.3 12.3L15 13.6V18.6Z" fill="currentColor"></path>
                  </svg>
                </span>
                <div class="text-inverse-primary fw-bold fs-2 mb-2 mt-5">Instant Available Amount</div>
                <div class="text-inverse-primary fw-semobold fs-7">${{stripeBalance.instant_available[0].amount/100}} USD</div>
            </div>
          </a>
      </div>
      <div class="col-xl-4">
          <a href="#" class="card-xl-stretch mb-5 mb-xl-8 bg-success card hoverable">
            <div class="card-body">
                <span class="svg-icon-white svg-icon svg-icon-3x mx-n1">
                  <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                      <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M14 12V21H10V12C10 11.4 10.4 11 11 11H13C13.6 11 14 11.4 14 12ZM7 2H5C4.4 2 4 2.4 4 3V21H8V3C8 2.4 7.6 2 7 2Z" fill="currentColor"></path>
                      <path xmlns="http://www.w3.org/2000/svg" d="M21 20H20V16C20 15.4 19.6 15 19 15H17C16.4 15 16 15.4 16 16V20H3C2.4 20 2 20.4 2 21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21C22 20.4 21.6 20 21 20Z" fill="currentColor"></path>
                  </svg>
                </span>
                <div class="text-inverse-success fw-bold fs-2 mb-2 mt-5">Pending Amount</div>
                <div class="text-inverse-success fw-semobold fs-7">${{stripeBalance.pending[0].amount/100}} USD</div>
            </div>
          </a>
      </div>
    </div>

    <div class="card-xl-stretch mb-xl-8 card mt-3" v-if="this.$page.props.auth.user.completed_stripe_onboarding && this.$page.props.auth.user.stripe_bank_id">
    <div class="card-header border-0 pt-10 pb-5">
        <h3 class="card-title align-items-start flex-column"><span class="card-label fw-bold fs-3 mb-1">Latest Transactions</span>
        </h3>
        <div class="card-toolbar">
          <ul class="nav" role="tablist">
              <li class="nav-item" role="presentation"><a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-light-primary fw-bold px-4 me-1 active" data-bs-toggle="tab" href="#kt_table_widget_7_tab_1" aria-selected="true" role="tab">All Transactions</a></li>
              <!-- <li class="nav-item" role="presentation" ><a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-light-danger fw-bold px-4" data-bs-toggle="tab" href="#kt_table_widget_7_tab_3" aria-selected="false" role="tab" tabindex="-1">Refunds</a></li> -->
              <li class="nav-item" role="presentation"><a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-light-warning fw-bold px-4" data-bs-toggle="tab" href="#kt_table_widget_7_tab_5" aria-selected="false" role="tab" tabindex="-1">Payouts</a></li>
          </ul>
        </div>
    </div>
    <div class="card-body py-1 pb-4">
        <div class="tab-content">
          <div class="tab-pane fade active show" id="kt_table_widget_7_tab_1" role="tabpanel" >
            <div class="table-responsive" v-if="allTransactions.length > 0">
              <table class="table align-middle gs-0 gy-3">
                  <thead>
                    <tr>
                        <th class="p-0 w-50px"></th>
                        <th class="p-0 min-w-150px"></th>
                        <th class="p-0 min-w-140px"></th>
                        <th class="p-0 min-w-120px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(charge,index) in allTransactions" :key="index">
                        <td>
                          <div class="symbol symbol-50px me-2">
                              <span class="bg-light-success symbol-label" v-if="charge.refunded == false &&  charge.refunds.total_count == 0 && charge.status == 'succeeded'">
                                <span class="svg-icon-success svg-icon svg-icon-2x">
                                    <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                                      <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M18 22C19.7 22 21 20.7 21 19C21 18.5 20.9 18.1 20.7 17.7L15.3 6.30005C15.1 5.90005 15 5.5 15 5C15 3.3 16.3 2 18 2H6C4.3 2 3 3.3 3 5C3 5.5 3.1 5.90005 3.3 6.30005L8.7 17.7C8.9 18.1 9 18.5 9 19C9 20.7 7.7 22 6 22H18Z" fill="currentColor"></path>
                                      <path xmlns="http://www.w3.org/2000/svg" d="M18 2C19.7 2 21 3.3 21 5H9C9 3.3 7.7 2 6 2H18Z" fill="currentColor"></path>
                                      <path xmlns="http://www.w3.org/2000/svg" d="M9 19C9 20.7 7.7 22 6 22C4.3 22 3 20.7 3 19H9Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                              </span>
                              <span class="bg-light-danger symbol-label" v-if="charge.refunded == true ">
                                  <span class="svg-icon-danger svg-icon svg-icon-2x">
                                      <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path xmlns="http://www.w3.org/2000/svg" d="M18 21.6C16.6 20.4 9.1 20.3 6.3 21.2C5.7 21.4 5.1 21.2 4.7 20.8L2 18C4.2 15.8 10.8 15.1 15.8 15.8C16.2 18.3 17 20.5 18 21.6ZM18.8 2.8C18.4 2.4 17.8 2.20001 17.2 2.40001C14.4 3.30001 6.9 3.2 5.5 2C6.8 3.3 7.4 5.5 7.7 7.7C9 7.9 10.3 8 11.7 8C15.8 8 19.8 7.2 21.5 5.5L18.8 2.8Z" fill="currentColor"></path>
                                        <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M21.2 17.3C21.4 17.9 21.2 18.5 20.8 18.9L18 21.6C15.8 19.4 15.1 12.8 15.8 7.8C18.3 7.4 20.4 6.70001 21.5 5.60001C20.4 7.00001 20.2 14.5 21.2 17.3ZM8 11.7C8 9 7.7 4.2 5.5 2L2.8 4.8C2.4 5.2 2.2 5.80001 2.4 6.40001C2.7 7.40001 3.00001 9.2 3.10001 11.7C3.10001 15.5 2.40001 17.6 2.10001 18C3.20001 16.9 5.3 16.2 7.8 15.8C8 14.2 8 12.7 8 11.7Z" fill="currentColor"></path>
                                      </svg>
                                  </span>
                              </span>
                              <span class="bg-light-info symbol-label" v-if="charge.refunded == false &&  charge.refunds.total_count > 0">
                                  <span class="svg-icon-info svg-icon svg-icon-2x">
                                      <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M18.4 5.59998C21.9 9.09998 21.9 14.8 18.4 18.3C14.9 21.8 9.2 21.8 5.7 18.3L18.4 5.59998Z" fill="currentColor"></path>
                                        <path xmlns="http://www.w3.org/2000/svg" d="M12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2ZM19.9 11H13V8.8999C14.9 8.6999 16.7 8.00005 18.1 6.80005C19.1 8.00005 19.7 9.4 19.9 11ZM11 19.8999C9.7 19.6999 8.39999 19.2 7.39999 18.5C8.49999 17.7 9.7 17.2001 11 17.1001V19.8999ZM5.89999 6.90002C7.39999 8.10002 9.2 8.8 11 9V11.1001H4.10001C4.30001 9.4001 4.89999 8.00002 5.89999 6.90002ZM7.39999 5.5C8.49999 4.7 9.7 4.19998 11 4.09998V7C9.7 6.8 8.39999 6.3 7.39999 5.5ZM13 17.1001C14.3 17.3001 15.6 17.8 16.6 18.5C15.5 19.3 14.3 19.7999 13 19.8999V17.1001ZM13 4.09998C14.3 4.29998 15.6 4.8 16.6 5.5C15.5 6.3 14.3 6.80002 13 6.90002V4.09998ZM4.10001 13H11V15.1001C9.1 15.3001 7.29999 16 5.89999 17.2C4.89999 16 4.30001 14.6 4.10001 13ZM18.1 17.1001C16.6 15.9001 14.8 15.2 13 15V12.8999H19.9C19.7 14.5999 19.1 16.0001 18.1 17.1001Z" fill="currentColor"></path>
                                      </svg>
                                  </span>
                              </span>
                          </div>
                        </td>
                        <td><a href="javascript:void(0)" class="text-dark fw-bold text-hover-primary mb-1 fs-6">Payment From {{charge.source.application_name}}</a><span class="text-muted fw-semobold d-block fs-7">{{charge.id}}</span></td>
                        <td class="text-end"><span class="text-muted fw-semobold d-block fs-8">Amount</span><span class="text-dark fw-bold d-block fs-7">${{charge.amount/100}}</span></td>
                        <td class="text-end"><span class="text-dark fw-bold d-block fs-7">{{formatDateTimeUnix(charge.created)}}</span></td>
                        <td class="text-end" v-if="charge.refunded == false &&  charge.refunds.total_count == 0 && charge.status == 'succeeded'"><span class="badge-light-success badge fs-7 fw-bold">Succeeded</span></td>
                        <td class="text-end" v-if="charge.refunded == true "><span class="badge-light-danger badge fs-7 fw-bold">Refunded</span></td>
                        <td class="text-end" v-if="charge.refunded == false &&  charge.refunds.total_count > 0"><span class="badge-light-info badge fs-7 fw-bold">Partially Refunded</span></td>
                    </tr>
                  </tbody>
              </table>
            </div>
            <div v-else>No Record Found</div>
            <div class="row">
              <div class="col-6">
              </div>
              <div class="col-6">
                <ul class="pagination justify-content-end">
                  <li class="page-item previous" :class="[this.hasMoreTransactionsPrevious ? '' : 'disabled']">
                      <!-- <span class="page-link page-text">Previous</span> -->
                      <a class="page-link page-text" @click="getTransactions(firstTransactionId, null, 'transactions')"><span><span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor"/>
                      <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor"/>
                      </svg>
                      </span>Previous</span></a>
                  </li>
                  <li class="page-item next" :class="[this.hasMoreTransactionsNext ? '' : 'disabled']">
                      <a class="page-link page-text" @click="getTransactions(null, lastTransactionId, 'transactions')"><span>Next<span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M9.63433 11.4343L5.45001 7.25C5.0358 6.83579 5.0358 6.16421 5.45001 5.75C5.86423 5.33579 6.5358 5.33579 6.95001 5.75L12.4929 11.2929C12.8834 11.6834 12.8834 12.3166 12.4929 12.7071L6.95001 18.25C6.5358 18.6642 5.86423 18.6642 5.45001 18.25C5.0358 17.8358 5.0358 17.1642 5.45001 16.75L9.63433 12.5657C9.94675 12.2533 9.94675 11.7467 9.63433 11.4343Z" fill="currentColor"/>
                      <path d="M15.6343 11.4343L11.45 7.25C11.0358 6.83579 11.0358 6.16421 11.45 5.75C11.8642 5.33579 12.5358 5.33579 12.95 5.75L18.4929 11.2929C18.8834 11.6834 18.8834 12.3166 18.4929 12.7071L12.95 18.25C12.5358 18.6642 11.8642 18.6642 11.45 18.25C11.0358 17.8358 11.0358 17.1642 11.45 16.75L15.6343 12.5657C15.9467 12.2533 15.9467 11.7467 15.6343 11.4343Z" fill="currentColor"/>
                      </svg>
                      </span></span></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- <div class="tab-pane fade" id="kt_table_widget_7_tab_3" role="tabpanel" >
              <div class="table-responsive" v-if="refundedTransactions.length > 0">
                <table class="table align-middle gs-0 gy-3">
                    <thead>
                      <tr>
                          <th class="p-0 w-50px"></th>
                          <th class="p-0 min-w-150px"></th>
                          <th class="p-0 min-w-140px"></th>
                          <th class="p-0 min-w-120px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(charge,index) in refundedTransactions" :key="index">
                          <td>
                            <div class="symbol symbol-50px me-2">
                                <span class="bg-light-danger symbol-label">
                                  <span class="svg-icon-danger svg-icon svg-icon-2x">
                                      <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path xmlns="http://www.w3.org/2000/svg" d="M18 21.6C16.6 20.4 9.1 20.3 6.3 21.2C5.7 21.4 5.1 21.2 4.7 20.8L2 18C4.2 15.8 10.8 15.1 15.8 15.8C16.2 18.3 17 20.5 18 21.6ZM18.8 2.8C18.4 2.4 17.8 2.20001 17.2 2.40001C14.4 3.30001 6.9 3.2 5.5 2C6.8 3.3 7.4 5.5 7.7 7.7C9 7.9 10.3 8 11.7 8C15.8 8 19.8 7.2 21.5 5.5L18.8 2.8Z" fill="currentColor"></path>
                                        <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M21.2 17.3C21.4 17.9 21.2 18.5 20.8 18.9L18 21.6C15.8 19.4 15.1 12.8 15.8 7.8C18.3 7.4 20.4 6.70001 21.5 5.60001C20.4 7.00001 20.2 14.5 21.2 17.3ZM8 11.7C8 9 7.7 4.2 5.5 2L2.8 4.8C2.4 5.2 2.2 5.80001 2.4 6.40001C2.7 7.40001 3.00001 9.2 3.10001 11.7C3.10001 15.5 2.40001 17.6 2.10001 18C3.20001 16.9 5.3 16.2 7.8 15.8C8 14.2 8 12.7 8 11.7Z" fill="currentColor"></path>
                                      </svg>
                                  </span>
                                </span>
                            </div>
                          </td>
                          <td><a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">Payment From Shumsy Technologies</a><span class="text-muted fw-semobold d-block fs-7">{{charge.id}}</span></td>
                          <td class="text-end"><span class="text-muted fw-semobold d-block fs-8">Amount</span><span class="text-dark fw-bold d-block fs-7">${{charge.amount/100}}</span></td>
                          <td class="text-end"><span class="text-dark fw-bold d-block fs-7">{{formatDate(charge.created)}}</span></td>
                          <td class="text-end"><span class="badge-light-danger badge fs-7 fw-bold">Refunded</span></td>
                          <td class="text-end"><a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"><i class="bi bi-three-dots fs-5"></i></a></td>
                      </tr>
                    </tbody>
                </table>
              </div>
              <div v-else>No Record Found</div>
            <div>
              <ul class="pagination justify-content-end">
                  <li class="page-item previous" :class="[this.hasMoreRefundsPrevious ? '' : 'disabled']">
                      <a class="page-link page-text" @click="getTransactions(firstRefundId, null, 'refunds')"><span><span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor"/>
                      <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor"/>
                      </svg>
                      </span>Previous</span></a>
                  </li>
                  <li class="page-item next" :class="[this.hasMoreRefundsNext ? '' : 'disabled']">
                      <a class="page-link page-text" @click="getTransactions(null, lastRefundId, 'refunds')"><span>Next<span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M9.63433 11.4343L5.45001 7.25C5.0358 6.83579 5.0358 6.16421 5.45001 5.75C5.86423 5.33579 6.5358 5.33579 6.95001 5.75L12.4929 11.2929C12.8834 11.6834 12.8834 12.3166 12.4929 12.7071L6.95001 18.25C6.5358 18.6642 5.86423 18.6642 5.45001 18.25C5.0358 17.8358 5.0358 17.1642 5.45001 16.75L9.63433 12.5657C9.94675 12.2533 9.94675 11.7467 9.63433 11.4343Z" fill="currentColor"/>
                      <path d="M15.6343 11.4343L11.45 7.25C11.0358 6.83579 11.0358 6.16421 11.45 5.75C11.8642 5.33579 12.5358 5.33579 12.95 5.75L18.4929 11.2929C18.8834 11.6834 18.8834 12.3166 18.4929 12.7071L12.95 18.25C12.5358 18.6642 11.8642 18.6642 11.45 18.25C11.0358 17.8358 11.0358 17.1642 11.45 16.75L15.6343 12.5657C15.9467 12.2533 15.9467 11.7467 15.6343 11.4343Z" fill="currentColor"/>
                      </svg>
                      </span></span></a>
                  </li>
                </ul>
            </div>
          </div> -->
          <div class="tab-pane fade" id="kt_table_widget_7_tab_5" role="tabpanel" >
              <div class="table-responsive" v-if="stripePayouts.data.length > 0">
                <table class="table align-middle gs-0 gy-3">
                    <thead>
                      <tr>
                          <th class="p-0 w-50px"></th>
                          <th class="p-0 min-w-150px"></th>
                          <th class="p-0 min-w-140px"></th>
                          <th class="p-0 min-w-120px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(payout,index) in payouts.data" :key="index">
                          <td>
                            <div class="symbol symbol-50px me-2">
                              <span class="bg-light-warning symbol-label">
                                <span class="svg-icon-warning svg-icon svg-icon-2x">
                                    <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                                      <path xmlns="http://www.w3.org/2000/svg" opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                      <path xmlns="http://www.w3.org/2000/svg" d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                              </span>
                            </div>
                          </td>
                          <td><a href="javascript:void(0)" class="text-dark fw-bold text-hover-primary mb-1 fs-6">Payment From {{payout.description}}</a><span class="text-muted fw-semobold d-block fs-7">{{payout.id}}</span></td>
                          <td class="text-end"><span class="text-muted fw-semobold d-block fs-8">Amount</span><span class="text-dark fw-bold d-block fs-7">${{payout.amount/100}}</span></td>
                          <td class="text-end"><span class="text-dark fw-bold d-block fs-7">{{formatDateTimeUnix(payout.arrival_date)}}</span></td>
                          <td class="text-end"><span class="badge-light-warning badge fs-7 fw-bold">{{payout.status}}</span></td>
                      </tr>
                    </tbody>
                </table>
              </div>
              <div v-else>No Record Found</div>
            <div>
              <ul class="pagination justify-content-end">
                  <li class="page-item previous" :class="[this.hasMorePayoutsPrevious ? '' : 'disabled']">
                      <a class="page-link page-text" @click="getTransactions(firstPayoutId, null, 'payouts')"><span><span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor"/>
                      <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor"/>
                      </svg>
                      </span>Previous</span></a>
                  </li>
                  <li class="page-item next" :class="[this.hasMorePayoutsNext ? '' : 'disabled']">
                      <a class="page-link page-text" @click="getTransactions(null, lastPayoutId, 'payouts')"><span>Next<span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M9.63433 11.4343L5.45001 7.25C5.0358 6.83579 5.0358 6.16421 5.45001 5.75C5.86423 5.33579 6.5358 5.33579 6.95001 5.75L12.4929 11.2929C12.8834 11.6834 12.8834 12.3166 12.4929 12.7071L6.95001 18.25C6.5358 18.6642 5.86423 18.6642 5.45001 18.25C5.0358 17.8358 5.0358 17.1642 5.45001 16.75L9.63433 12.5657C9.94675 12.2533 9.94675 11.7467 9.63433 11.4343Z" fill="currentColor"/>
                      <path d="M15.6343 11.4343L11.45 7.25C11.0358 6.83579 11.0358 6.16421 11.45 5.75C11.8642 5.33579 12.5358 5.33579 12.95 5.75L18.4929 11.2929C18.8834 11.6834 18.8834 12.3166 18.4929 12.7071L12.95 18.25C12.5358 18.6642 11.8642 18.6642 11.45 18.25C11.0358 17.8358 11.0358 17.1642 11.45 16.75L15.6343 12.5657C15.9467 12.2533 15.9467 11.7467 15.6343 11.4343Z" fill="currentColor"/>
                      </svg>
                      </span></span></a>
                  </li>
                </ul>
            </div>
          </div>
        </div>
    </div>
    </div>
    
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import Select2 from 'vue3-select2-component';
import Helpers from '@/Mixins/Helpers'
import Error from '@/Components/InputError.vue'

export default {
  props: ["business", "bankAccount", "stripeConnected", "stripeBalance", "stripeChargesAll", "stripeAccount", "stripePayouts", "stripeChargesRefunded"],

  components: {
    Head,
    AuthenticatedLayout,
    Button,
    Breadcrumbs,
    Label,
    Input,
    Select2,
    Error,
  },

  data() {
    return {
      form: null,
      businessUuid: null,
      title: "Settings",
      transactions: this.stripeChargesAll ? this.stripeChargesAll : null,
      firstTransactionId: null,
      lastTransactionId: null,
      hasMoreTransactionsNext: null,
      hasMoreTransactionsPrevious: null,
      // refunds: this.stripeChargesRefunded ? this.stripeChargesRefunded : null,
      // firstRefundId: null,
      // lastRefundId: null,
      // hasMoreRefundsNext: null,
      // hasMoreRefundsPrevious: null,
      payouts: this.stripePayouts ? this.stripePayouts : null,
      firstPayoutId: null,
      lastPayoutId: null,
      hasMorePayoutsNext: null,
      hasMorePayoutsPrevious: null,
      totalTransactions: 1,
      payoutSchedule: null,
    };
  },

  methods: {
    submit() {
      this.form.post(
        route("retail.dashboard.business.stripe.savebank", [ this.getSelectedModuleValue(), 
          this.businessUuid
        ]),
        {
          preventScroll: true,
        }
      );
    },

    getTransactions(prevId = null , nextId = null , type = null){
      window.axios.post(route("retail.dashboard.business.stripe.list", [this.getSelectedModuleValue(), this.businessUuid]) , {
        previous_id : prevId,
        next_id : nextId,
        request_type : type,
      })
      .then((response) => {
          switch (type) {
            case 'transactions':
              this.transactions = response.data.data
              this.firstTransactionId = this.transactions.data[0].id
              this.lastTransactionId = this.transactions.data[this.transactions.data.length - 1].id
              if (nextId) {
                this.hasMoreTransactionsNext = this.transactions.has_more
                this.hasMoreTransactionsPrevious = true
              } else {
                this.hasMoreTransactionsNext = true
                this.hasMoreTransactionsPrevious = this.transactions.has_more
              }
              break;

            case 'payouts':
              this.payouts = response.data.data
              this.firstPayoutId = this.payouts.data[0].id
              this.lastPayoutId = this.payouts.data[this.payouts.data.length - 1].id
              if (nextId) {
                this.hasMorePayoutsNext = this.payouts.has_more
                this.hasMorePayoutsPrevious = true
              } else {
                this.hasMorePayoutsNext = true
                this.hasMorePayoutsPrevious = this.payouts.has_more
              }
              break;

            // case 'refunds':
            //   this.refunds = response.data.data
            //   this.firstRefundId = this.refunds.data[0].id
            //   this.lastRefundId = this.refunds.data[this.refunds.data.length - 1].id
            //   if (nextId) {
            //     this.hasMoreRefundsNext = this.refunds.has_more
            //     this.hasMoreRefundsPrevious = true
            //   } else {
            //     this.hasMoreRefundsNext = true
            //     this.hasMoreRefundsPrevious = this.refunds.has_more
            //   }
            //   break;
          }
      })
      .catch(error => {
      });
    },

    changePayoutSchedule(event){
      if (event.target.value != null) {
        this.$inertia.post(route('retail.dashboard.business.payout.stripe',[this.getSelectedModuleValue(), this.businessUuid]) ,  {schedule : event.target.value} , {
            onError: (errors) => {
          }
        })
      }
    }
  },

  computed:{
    // refundedTransactions: function() {
    //     return this.refunds.data.filter(function(c) {
    //         return true
    //     })
    // },

    allTransactions: function() {
        return this.transactions.data.filter(function(c) {
            return true
        })
    },
  },  

  mounted() {
    this.form = useForm({
        account_holder_name: this.bankAccount ? this.bankAccount.account_holder_name : "",
        account_number: this.bankAccount ? this.bankAccount.account_number : "",
        account_holder_type: this.bankAccount ? this.bankAccount.account_holder_type : "",
        country: this.bankAccount ? this.bankAccount.country : "",
        routing_number: this.bankAccount ? this.bankAccount.routing_number : "",
        businessUrl: this.business.url,
    });
    this.businessUuid = this.business.uuid
    //Transactions variable assignment
    this.firstTransactionId = this.transactions.data.length > 0 ? this.transactions.data[0].id : null
    this.lastTransactionId = this.transactions.data.length > 0 ? this.transactions.data[this.transactions.data.length - 1].id : null
    this.hasMoreTransactionsNext = this.transactions.has_more
    this.hasMoreTransactionsPrevious = false
    //Payout variable assignment
    this.firstPayoutId = this.payouts.data.length > 0 ? this.payouts.data[0].id : null
    this.lastPayoutId = this.payouts.data.length > 0 ? this.payouts.data[this.payouts.data.length - 1].id : null
    this.hasMorePayoutsNext = this.payouts.has_more
    this.hasMorePayoutsPrevious = false
    //Refunds variable assignment
    // this.firstRefundId = this.refunds.data.length > 0 ? this.refunds.data[0].id : null
    // this.lastRefundId = this.refunds.data.length > 0 ? this.refunds.data[this.payouts.data.length - 1].id : null
    // this.hasMoreRefundsNext = this.refunds.has_more
    // this.hasMoreRefundsPrevious = false
  },
  mixins: [Helpers]
};
</script>
