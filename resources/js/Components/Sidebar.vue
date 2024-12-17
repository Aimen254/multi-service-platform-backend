<template>
    <div>
        <!--begin::Brand-->
        <div class="aside-logo flex-column-auto" id="kt_aside_logo">
            <!--begin::Logo-->
            <ApplicationLogo :classes="'h-40px logo'" :imgSrc="'/images/logo/localverse.jpeg'" />
            <!--end::Logo-->
            <!--begin::Aside toggler-->
            <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
                data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                data-kt-toggle-name="aside-minimize" @click="sidebarToggle">
                <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
                <span class="svg-icon svg-icon-1 rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.5"
                            d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z"
                            fill="black" />
                        <path
                            d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z"
                            fill="black" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </div>
            <!--end::Aside toggler-->
        </div>
        <div class="flex-column-auto px-4">
            <select2 class="business" v-model="selected" :options="moduleTags" @select="getValue($event)" />
        </div>
        <!--end::Brand-->
        <!--begin::Aside menu-->
        <div class="aside-menu flex-column-fluid">
            <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
                data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
                data-kt-scroll-offset="0">
                <!--begin::Menu-->
                <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                    id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">
                    <!-- Dashboard -->
                    <div data-kt-menu-trigger="click" :class="[route().current(completeRoute()) ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route(completeRoute(), selected)"
                                :class="[route().current(completeRoute() + '.*') ? 'active' : '']">
                            <span class="menu-title">Dashboard</span>
                            </Link>
                        </span>
                    </div>
                    <!-- Users Section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_administrators', 'view_business_owners', 'view_customers', 'view_reporters', 'view_remote_assistants']) && sideBarPermissions('view_users_tab')"
                        :class="[route().current('dashboard.administrators.*') || route().current('dashboard.owners.*') || route().current('dashboard.customers.*') || route().current('dashboard.reporters.*') || route().current('dashboard.assistants.*') || route().current('dashboard.employees.*') || route().current('dashboard.agents.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none">
                                            <path
                                                d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z"
                                                fill="black"></path>
                                            <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black"></rect>
                                        </svg>
                                    </span>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="menu-title">Users</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">

                            <!-- Administrators -->
                            <div class="menu-item" v-if="checkUserPermissions('view_administrators')">
                                <Link class="menu-link" :href="route('dashboard.administrators.index')"
                                    :class="[route().current('dashboard.administrators.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Administrators</span>
                                </Link>
                            </div>

                            <!-- Business Owners -->
                            <div class="menu-item" v-if="checkUserPermissions('view_business_owners')">
                                <Link class="menu-link" :href="route('dashboard.owners.index')"
                                    :class="[route().current('dashboard.owners.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Owners</span>
                                </Link>
                            </div>

                            <!-- Customers -->
                            <div class="menu-item" v-if="checkUserPermissions('view_customers')">
                                <Link class="menu-link" :href="route('dashboard.customers.index')"
                                    :class="[route().current('dashboard.customers.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Customers</span>
                                </Link>
                            </div>

                            <!-- Reporters -->
                            <div class="menu-item" v-if="checkUserPermissions('view_reporters')">
                                <Link class="menu-link" :href="route('dashboard.reporters.index')"
                                    :class="[route().current('dashboard.reporters.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reporters</span>
                                </Link>
                            </div>

                            <!-- Remote Assistant -->
                            <div class="menu-item" v-if="checkUserPermissions('view_remote_assistants')">
                                <Link class="menu-link" :href="route('dashboard.assistants.index')"
                                    :class="[route().current('dashboard.assistants.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Remote Assistant</span>
                                </Link>
                            </div>

                        </div>
                    </div>

                    <!-- Drivers Section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_drivers', 'view_drivers_group', 'view_drivers_manager']) && sideBarPermissions('view_drivers_tab')"
                        :class="[route().current('dashboard.drivers.*') || route().current('dashboard.driver.managers.*') || route().current('dashboard.driver.groups.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                            width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path
                                                    d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z"
                                                    fill="#000000" fill-rule="nonzero"></path>
                                                <path
                                                    d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z"
                                                    fill="#000000" opacity="0.3"></path>
                                            </g>
                                        </svg>
                                    </span>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="menu-title">Drivers</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_drivers') && sideBarPermissions('view_drivers_tab')">
                                <Link class="menu-link" :href="route('dashboard.drivers.index')"
                                    :class="[route().current('dashboard.drivers.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Drivers</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_drivers_manager')">
                                <Link class="menu-link" :href="route('dashboard.driver.managers.index')"
                                    :class="[route().current('dashboard.driver.managers.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Driver Managers</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_drivers_group')">
                                <Link class="menu-link" :href="route('dashboard.driver.groups.index')"
                                    :class="[route().current('dashboard.driver.groups.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Driver Groups</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <!-- Settings Section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_settings', 'view_languages']) && sideBarPermissions('view_settings_tab')"
                        :class="[route().current('dashboard.settings.general.*') || route().current('dashboard.settings.languages.*') || 
                            route().current('dashboard.news-paper.logo') || route().current('dashboard.settings.roles.*') 
                            || route().current('dashboard.settings.deliveyzone.*') || route().current('dashboard.settings.chat-settings.index') || urlSecondLastSegment == 'email-notification' || urlSecondLastSegment == 'push-notification' || urlSecondLastSegment == 'social-authentication' ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                            width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path
                                                    d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z"
                                                    fill="#000000"></path>
                                            </g>
                                        </svg>
                                    </span>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="menu-title">Settings</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_roles')">
                                <Link class="menu-link" :href="route('dashboard.settings.roles.index')"
                                    :class="[route().current('dashboard.settings.roles.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Roles and Permissions</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'email-notification')"
                                    :class="[urlLastSegment == 'email-notification' || urlSecondLastSegment == 'email-notification' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Email Nofication</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'push-notification')"
                                    :class="[urlLastSegment == 'push-notification' || urlSecondLastSegment == 'push-notification' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Push Notification</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'social-authentication')"
                                    :class="[urlLastSegment == 'social-authentication' || urlSecondLastSegment == 'social-authentication' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Social Authentication</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'stripe-connect-settings')"
                                    :class="[urlLastSegment == 'stripe-connect-settings' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stripe Connect</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'number-format-settings')"
                                    :class="[urlLastSegment == 'number-format-settings' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Number Format</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'time-format-settings')"
                                    :class="[urlLastSegment == 'time-format-settings' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Time Format</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'tax_model_settings')"
                                    :class="[urlLastSegment == 'tax_model_settings' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tax Model</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link"
                                    :href="route('dashboard.settings.general.group', 'driver-assignment-settings')"
                                    :class="[urlLastSegment == 'driver-assignment-settings' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Driver Assignment</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_languages')">
                                <Link class="menu-link" :href="route('dashboard.settings.languages.index')"
                                    :class="[route().current('dashboard.settings.languages.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Languages</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_delivery_zones')">
                                <Link class="menu-link" :href="route('dashboard.settings.deliveyzone.index')"
                                    :class="[route().current('dashboard.settings.deliveyzone.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Delivery Settings</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_settings')">
                                <Link class="menu-link" :href="route('dashboard.news-paper.logo')"
                                    :class="[route().current('dashboard.news-paper.logo') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">News Paper Logo</span>
                                </Link>
                            </div>
                        </div>

                        <!-- chat setting -->
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_chat_settings')">
                                <Link class="menu-link" :href="route('dashboard.settings.chat-settings.index')"
                                    :class="[route().current('dashboard.settings.chat-settings.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Chat Settings</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Drivers News -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_news', 'view_news_categories']) && selected == 'settings'"
                        :class="[route().current('dashboard.news.*') || route().current('dashboard.categories.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-newspaper"></i>
                                    </span>
                                </span>
                            </span>
                            <span class="menu-title">News</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_news_categories')">
                                <Link class="menu-link" :href="route('dashboard.categories.index')"
                                    :class="[route().current('dashboard.categories.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">News Categories</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <!-- Tag manager -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_standard_tag']) && selected == 'settings'"
                        :class="[route().current('dashboard.tag.index') || route().current('dashboard.attributes.index') || route().current('dashboard.brandTag.index') || route().current('dashboard.productTag.index')
                            || route().current('dashboard.attributeTag.index') || route().current('dashboard.tag-mappers.index') || route().current('dashboard.standard-tag-mapper.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-newspaper"></i>
                                    </span>
                                </span>
                            </span>
                            <span class="menu-title">Tags Manager</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_industry_tag')">
                                <Link class="menu-link" :href="route('dashboard.tag.index')"
                                    :class="[route().current('dashboard.tag.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tags</span>
                                </Link>
                            </div>
                        </div>
                        <!-- brand tag in admin setting's tag manager -->
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_standard_tag')">
                                <Link class="menu-link" :href="route('dashboard.brandTag.index')"
                                    :class="[route().current('dashboard.brandTag.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Brand Tags</span>
                                </Link>
                            </div>
                        </div>
                        <!-- attribute tags in admin setting's tag manager -->
                        <!-- attribute type in admin setting's tag manager -->
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_attributes')">
                                <Link class="menu-link" :href="route('dashboard.attributes.index')"
                                    :class="[route().current('dashboard.attributes.index') || route().current('dashboard.attributeTag.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Attributes</span>
                                </Link>
                            </div>
                        </div>
                        <!-- orphan tag mapping in settings -->
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_tags_mapper')">
                                <Link class="menu-link" :href="route('dashboard.tag-mappers.index')"
                                    :class="[route().current('dashboard.tag-mappers.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Orphan Tag mapper</span>
                                </Link>
                            </div>
                        </div>

                        <!--tag mapper in tag manager -->
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_standard_tag')">
                                <Link class="menu-link" :href="route('dashboard.standard-tag-mapper.index')"
                                    :class="[route().current('dashboard.standard-tag-mapper.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tags Mapper</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <!-- Subscription in settings for business owner section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions('view_payment_method') && selected == 'settings'"
                        :class="[route().current('dashboard.subscription.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M21 9V11C21 11.6 20.6 12 20 12H14V8H20C20.6 8 21 8.4 21 9ZM10 8H4C3.4 8 3 8.4 3 9V11C3 11.6 3.4 12 4 12H10V8Z"
                                            fill="black" />
                                        <path d="M15 2C13.3 2 12 3.3 12 5V8H15C16.7 8 18 6.7 18 5C18 3.3 16.7 2 15 2Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M9 2C10.7 2 12 3.3 12 5V8H9C7.3 8 6 6.7 6 5C6 3.3 7.3 2 9 2ZM4 12V21C4 21.6 4.4 22 5 22H10V12H4ZM20 12V21C20 21.6 19.6 22 19 22H14V12H20Z"
                                            fill="black" />
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Subscriptions</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_payment_method')">
                                <Link class="menu-link" :href="route('dashboard.subscription.payment-method.index')"
                                    :class="[route().current('dashboard.subscription.payment-method.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Payment Method</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_subscription')">
                                <Link class="menu-link" :href="route('dashboard.subscription.subscribe.index')"
                                    :class="[route().current('dashboard.subscription.subscribe.index') || route().current('dashboard.subscription.subscribe.show') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Plans</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Administrator Settings for modules [News, Posts, Blogs, Obituaries, Recipes, Classifieds, Taskers] -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="modules.includes(this.getSelectedModuleName()) && (sideBarPermissions('view_tag_hierarchies') || sideBarPermissions('view_subscription_plan')) && checkUserPermissions(['view_tag_hierarchies', 'view_subscription_plan']) && selectedBusinessId == 'administration'"
                        :class="[(route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index') || route().current(this.getSelectedModuleName() + '.dashboard.subscription.*') || route().current(this.getSelectedModuleName() + '.dashboard.headlines.*')) ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-user-cog"></i>
                                    </span>
                                </span>
                            </span>
                            <span class="menu-title">Administrator</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">{{}}
                            <div class="menu-item menu-accordion" data-kt-menu-trigger="click"
                                v-if="checkUserPermissions('view_tag_hierarchies') && sideBarPermissions('view_tag_hierarchies') && selectedBusinessId == 'administration'"
                                :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index') ? 'here show' : '']">
                                <a href="#" class="menu-link py-3">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Tags Hierarchy </span>
                                    <span class="menu-arrow"></span>
                                </a>

                                <div class="menu-sub menu-sub-accordion pt-3">
                                    <div class="menu-item">
                                        <Link class="menu-link"
                                            :href="route(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 2])"
                                            :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 2]) ? 'active' : '']">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Level 2 Tags</span>
                                        </Link>
                                    </div>
                                    <div class="menu-item">
                                        <Link class="menu-link"
                                            :href="route(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 3])"
                                            :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 3]) ? 'active' : '']">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Level 3 Tags</span>
                                        </Link>
                                    </div>
                                    <div class="menu-item">
                                        <Link class="menu-link"
                                            :href="route(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 4])"
                                            :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 4]) ? 'active' : '']">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Level 4 Tags</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item menu-accordion" data-kt-menu-trigger="click"
                                v-if="checkUserPermissions('view_subscription_plan') && sideBarPermissions('view_subscriptions') && selectedBusinessId == 'administration'"
                                :class="[route().current(`${this.getSelectedModuleName()}.dashboard.subscription.*`) ? 'here show' : '']">
                                <a href="#" class="menu-link py-3">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Subscriptions</span>
                                    <span class="menu-arrow"></span>
                                </a>

                                <div class="menu-sub menu-sub-accordion pt-3">
                                    <div class="menu-item" v-if="checkUserPermissions('view_subscription_plan')">
                                        <Link v-if="selectedBusinessId" class="menu-link"
                                            :href="route(`${this.getSelectedModuleName()}.dashboard.subscription.plan.index`, [this.getSelectedModuleValue()])"
                                            :class="[route().current(`${this.getSelectedModuleName()}.dashboard.subscription.plan.index`) || route().current(`${this.getSelectedModuleName()}.dashboard.subscription.plan.create`) || route().current(`${this.getSelectedModuleName()}.dashboard.subscription.plan.edit`) ? 'active' : '']">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Plans</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- headlines -->
                        <div class="menu-sub menu-sub-accordion menu-active-bg"
                            v-if="(sideBarPermissions('view_tab_headlines')) && checkUserPermissions('view_headlines') && (selectedBusinessId === 'administration')">
                            <div class="menu-item">
                                <Link class="menu-link"
                                    :href="route(this.getSelectedModuleName() + '.dashboard.headlines.index', getSelectedModuleValue())"
                                    :class="[route().current(this.getSelectedModuleName() + '.dashboard.headlines.index') || route().current(this.getSelectedModuleName() + '.dashboard.headlines.create') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">HeadLines</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Tag Hierarachy -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="!modules.includes(this.getSelectedModuleName()) && checkUserPermissions('view_tag_hierarchies') && sideBarPermissions('view_tag_hierarchies') && selectedBusinessId == 'administration'"
                        :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-newspaper"></i>
                                    </span>
                                </span>
                            </span>
                            <span class="menu-title">Tags Hierarchy</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item">
                                <Link class="menu-link"
                                    :href="route(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 2])"
                                    :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 2]) ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Level 2 Tags</span>
                                </Link>
                            </div>
                            <div class="menu-item">
                                <Link class="menu-link"
                                    :href="route(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 3])"
                                    :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 3]) ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Level 3 Tags</span>
                                </Link>
                            </div>
                            <div class="menu-item">
                                <Link class="menu-link"
                                    :href="route(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 4])"
                                    :class="[route().current(this.getSelectedModuleName() + '.dashboard.tag-hierarchies.index', [this.getSelectedModuleValue(), 4]) ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Level 4 Tags</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <!-- Subscription in administration for admin section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="!modules.includes(this.getSelectedModuleName()) && checkUserPermissions('view_subscription_plan') && sideBarPermissions('view_subscriptions') && selectedBusinessId == 'administration'"
                        :class="[route().current(`${this.getSelectedModuleName()}.dashboard.subscription.*`) ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M21 9V11C21 11.6 20.6 12 20 12H14V8H20C20.6 8 21 8.4 21 9ZM10 8H4C3.4 8 3 8.4 3 9V11C3 11.6 3.4 12 4 12H10V8Z"
                                            fill="black" />
                                        <path d="M15 2C13.3 2 12 3.3 12 5V8H15C16.7 8 18 6.7 18 5C18 3.3 16.7 2 15 2Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M9 2C10.7 2 12 3.3 12 5V8H9C7.3 8 6 6.7 6 5C6 3.3 7.3 2 9 2ZM4 12V21C4 21.6 4.4 22 5 22H10V12H4ZM20 12V21C20 21.6 19.6 22 19 22H14V12H20Z"
                                            fill="black" />
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Subscriptions</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_subscription_plan')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route(`${this.getSelectedModuleName()}.dashboard.subscription.plan.index`, [this.getSelectedModuleValue()])"
                                    :class="[route().current(`${this.getSelectedModuleName()}.dashboard.subscription.plan.index`) || route().current(`${this.getSelectedModuleName()}.dashboard.subscription.plan.create`) || route().current(`${this.getSelectedModuleName()}.dashboard.subscription.plan.edit`) ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Plans</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Businesses -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_business_tab'))"
                        :class="[route().current('retail.dashboard.businesses.index') || route().current('retail.dashboard.businesses.index') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('retail.dashboard.businesses.index', getSelectedModuleValue())"
                                :class="[route().current('retail.dashboard.businesses.index') || route().current('retail.dashboard.businesses.create') ? 'active' : '']">
                            <span class="menu-title">Businesses</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Business section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'view_business_settings', 'edit_business']) && sideBarPermissions('view_business_details_tab') && selectedBusinessId && selectedBusinessId != 'administration'"
                        :class="[route().current('retail.dashboard.business.admin-settings') || route().current('retail.dashboard.business.admin.assignFeeType') || route().current('retail.dashboard.business.admin.settings')
                            || route().current('dashboard.businesses.edit') || route().current('retail.dashboard.business.settings.index') || route().current('retail.dashboard.business.stripe.index')
                            || route().current('retail.dashboard.business.businessschedules.index') || route().current('retail.dashboard.business.deliveryzones.index') || route().current('retail.dashboard.business.mailings.index')
                            || route().current('retail.dashboard.business.emails.index') || route().current('retail.dashboard.business.reviews.index') || route().current('retail.dashboard.business.business-tags.index') || route().current('retail.dashboard.businesses.edit') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Business Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business') && selectedBusinessId">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.businesses.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.businesses.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Info</span>
                                </Link>
                            </div>

                            <!-- Settings -->
                            <div class="menu-item" v-if="checkUserPermissions('view_business_settings')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.settings.index', [this.getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.settings.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Settings</span>
                                </Link>
                            </div>
                            <!-- Stripe -->
                            <div class="menu-item" v-if="checkUserPermissions('view_business_settings')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.stripe.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.stripe.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stripe Connect</span>
                                </Link>
                            </div>
                            <!-- Business schedule -->
                            <div class="menu-item" v-if="checkUserPermissions('view_business_schedule_time')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.businessschedules.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.businessschedules.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Schedule</span>
                                </Link>
                            </div>

                            <!-- Delivery Zone -->
                            <div class="menu-item" v-if="checkUserPermissions('view_delivery_zones')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.deliveryzones.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.deliveryzones.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Delivery Settings</span>
                                </Link>
                            </div>

                            <!-- business mailings setting -->
                            <div class="menu-item" v-if="checkUserPermissions('view_business_mailings')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.mailings.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.mailings.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Mail Settings</span>
                                </Link>
                            </div>

                            <!-- Additional emails -->
                            <div class="menu-item" v-if="checkUserPermissions('view_additional_emails')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.emails.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.emails.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Additional Emails</span>
                                </Link>
                            </div>
                            <!-- Business Reviews -->
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                            <!-- business tags -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.admin-settings') || route().current('retail.dashboard.business.admin.settings') || route().current('retail.dashboard.business.admin.assignFeeType') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>

                            <!-- business extra tags -->
                            <div class="menu-item" v-if="checkUserPermissions('view_extra_tags')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.business-tags.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.business-tags.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Extra Tags</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Coupons section -->
                    <div class="menu-item menu-accordion"
                        v-if="checkUserPermissions('view_business_coupons') && sideBarPermissions('view_coupon_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selectedBusinessId"
                        :class="[route().current('retail.dashboard.business.coupons.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-primary svg-icon-2x">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                            width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path
                                                    d="M3.52270623,14.028695 C2.82576459,13.3275941 2.82576459,12.19529 3.52270623,11.4941891 L11.6127629,3.54050571 C11.9489429,3.20999263 12.401513,3.0247814 12.8729533,3.0247814 L19.3274172,3.0247814 C20.3201611,3.0247814 21.124939,3.82955935 21.124939,4.82230326 L21.124939,11.2583059 C21.124939,11.7406659 20.9310733,12.2027862 20.5869271,12.5407722 L12.5103155,20.4728108 C12.1731575,20.8103442 11.7156477,21 11.2385688,21 C10.7614899,21 10.3039801,20.8103442 9.9668221,20.4728108 L3.52270623,14.028695 Z M16.9307214,9.01652093 C17.9234653,9.01652093 18.7282432,8.21174298 18.7282432,7.21899907 C18.7282432,6.22625516 17.9234653,5.42147721 16.9307214,5.42147721 C15.9379775,5.42147721 15.1331995,6.22625516 15.1331995,7.21899907 C15.1331995,8.21174298 15.9379775,9.01652093 16.9307214,9.01652093 Z"
                                                    fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                            </g>
                                        </svg>
                                    </span>
                                </span>
                            </span>
                            <Link
                                :href="route('retail.dashboard.business.coupons.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('dashboard.business.coupons.index') ? 'active' : '']">
                            <span class="menu-title">Coupons</span>
                            </Link>
                        </span>
                    </div>

                    <!-- products section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions('view_products') && sideBarPermissions('view_products_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selectedBusinessId"
                        :class="[route().current('retail.dashboard.business.products.*') || route().current('retail.dashboard.product.*') || route().current('dashboard.product.discounts.*') || route().current('retail.dashboard.business.sizes.*') || route().current('retail.dashboard.business.colors.*') || route().current('dashboard.product.variants.*') || route().current('dashboard.product.media.*') || route().current('dashboard.product.taxes.*') || urlLastSegment == 'discount' ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M21 9V11C21 11.6 20.6 12 20 12H14V8H20C20.6 8 21 8.4 21 9ZM10 8H4C3.4 8 3 8.4 3 9V11C3 11.6 3.4 12 4 12H10V8Z"
                                            fill="black" />
                                        <path d="M15 2C13.3 2 12 3.3 12 5V8H15C16.7 8 18 6.7 18 5C18 3.3 16.7 2 15 2Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M9 2C10.7 2 12 3.3 12 5V8H9C7.3 8 6 6.7 6 5C6 3.3 7.3 2 9 2ZM4 12V21C4 21.6 4.4 22 5 22H10V12H4ZM20 12V21C20 21.6 19.6 22 19 22H14V12H20Z"
                                            fill="black" />
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Products</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_products') && selectedBusinessId">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.products.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.products.*') || route().current('retail.dashboard.product.*') || route().current('dashboard.product.discounts.*') || route().current('dashboard.product.variants.*') || route().current('dashboard.product.media.*') || route().current('dashboard.product.taxes.*') || urlLastSegment == 'discount' ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Products</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_product_sizes')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.sizes.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.sizes.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Sizes</span>
                                </Link>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_product_colors')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('retail.dashboard.business.colors.index', [this.getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('retail.dashboard.business.colors.*') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Colors</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- orders section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions('view_orders') && sideBarPermissions('view_orders_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selectedBusinessId"
                        :class="[route().current('retail.dashboard.business.order.type.orders.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path opacity="0.3"
                                            d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z"
                                            fill="black" />
                                        <path
                                            d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z"
                                            fill="black" />
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Orders</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item" v-if="checkUserPermissions('view_orders') && selectedBusinessId">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route(getSelectedModuleValue() == 1 ? 'retail.dashboard.business.order.type.orders.index' : '', [getSelectedModuleValue(), selectedBusinessId, 'active'])"
                                    :class="[route().current(getSelectedModuleValue() == 1 ? 'retail.dashboard.business.order.type.orders.*' : '', [getSelectedModuleValue(), selectedBusinessId, 'active']) ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Active Orders</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_orders') && selectedBusinessId">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route(getSelectedModuleValue() == 1 ? 'retail.dashboard.business.order.type.orders.index' : '', [getSelectedModuleValue(), selectedBusinessId, 'completed'])"
                                    :class="[route().current(getSelectedModuleValue() == 1 ? 'retail.dashboard.business.order.type.orders.*' : '', [getSelectedModuleValue(), selectedBusinessId, 'completed']) ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Completed Orders</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_orders') && selectedBusinessId">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route(getSelectedModuleValue() == 1 ? 'retail.dashboard.business.order.type.orders.index' : '', [getSelectedModuleValue(), selectedBusinessId, 'cancelled'])"
                                    :class="[route().current(getSelectedModuleValue() == 1 ? 'retail.dashboard.business.order.type.orders.*' : '', [getSelectedModuleValue(), selectedBusinessId, 'cancelled']) ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Cancelled Orders</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Dealerships -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_garage_tab')) && selected != 'settings'"
                        :class="[route().current('automotive.dashboard.dealership.index') || route().current('automotive.dashboard.dealership.create') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('automotive.dashboard.dealership.index', getSelectedModuleValue())"
                                :class="[route().current('automotive.dashboard.dealership.index') || route().current('automotive.dashboard.dealership.create') ? 'active' : '']">
                            <span class="menu-title">Dealerships</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Dealership section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'edit_business']) && sideBarPermissions('view_garage_detail_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selected != 'settings'"
                        :class="[route().current('automotive.dashboard.dealership.edit') || route().current('automotive.dashboard.dealership.admin-settings') || route().current('automotive.dashboard.dealership.admin.settings') || route().current('retail.dashboard.garage.admin.assignFeeType') || route().current('automotive.dashboard.dealership.reviews.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Dealership Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business') && selectedBusinessId">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('automotive.dashboard.dealership.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('automotive.dashboard.dealership.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Dealership Info</span>
                                </Link>
                            </div>
                            <!-- dealership admin settings -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('automotive.dashboard.dealership.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('automotive.dashboard.dealership.admin-settings') || route().current('automotive.dashboard.dealership.admin.settings') || route().current('retail.dashboard.garage.admin.assignFeeType') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>
                            <!-- Garage Reviews -->
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews') && selectedBusinessId">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('automotive.dashboard.dealership.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('automotive.dashboard.dealership.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicles section -->
                    <!-- Dealerships -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_products']) && sideBarPermissions('view_vehilces_tab')) && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('automotive.dashboard.dealership.vehicles.*') || route().current('automotive.dashboard.vehicle.media.index') || route().current('automotive.dashboard.vehicle.vehicle-tags.index') || route().current('automotive.dashboard.vehicle.attribute-tags.index') || route().current('automotive.dashboard.vehicle.discount.index') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('automotive.dashboard.dealership.vehicles.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('automotive.dashboard.dealership.vehicles.*') || route().current('automotive.dashboard.vehicle.media.index') || route().current('automotive.dashboard.vehicle.vehicle-tags.index') || route().current('automotive.dashboard.vehicle.attribute-tags.index') || route().current('automotive.dashboard.vehicle.discount.index') ? 'active' : '']">
                            <span class="menu-title">Vehicles</span>
                            </Link>
                        </span>
                    </div>
                    <!-- Contact Form -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_contact_form']) && sideBarPermissions('view_contact_form')) && (selectedBusinessId != 'administration') && selectedModule != 'settings'"
                        :class="[route().current('automotive.dashboard.dealership.contact-form.*') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M21 9V11C21 11.6 20.6 12 20 12H14V8H20C20.6 8 21 8.4 21 9ZM10 8H4C3.4 8 3 8.4 3 9V11C3 11.6 3.4 12 4 12H10V8Z"
                                            fill="black" />
                                        <path d="M15 2C13.3 2 12 3.3 12 5V8H15C16.7 8 18 6.7 18 5C18 3.3 16.7 2 15 2Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M9 2C10.7 2 12 3.3 12 5V8H9C7.3 8 6 6.7 6 5C6 3.3 7.3 2 9 2ZM4 12V21C4 21.6 4.4 22 5 22H10V12H4ZM20 12V21C20 21.6 19.6 22 19 22H14V12H20Z"
                                            fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('automotive.dashboard.dealership.contact-form.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('automotive.dashboard.dealership.contact-form.*') ? 'active' : '']">
                            <span class="menu-title">Contact Forms</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Dealership Boats -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_boats_dealerships')) && selected != 'settings'"
                        :class="[route().current('boats.dashboard.dealership.index') || route().current('boats.dashboard.dealership.create') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('boats.dashboard.dealership.index', getSelectedModuleValue())"
                                :class="[route().current('boats.dashboard.dealership.index') || route().current('boats.dashboard.dealership.create') ? 'active' : '']">
                            <span class="menu-title">Dealerships</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Dealership Boats details -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'edit_business']) && sideBarPermissions('view_dealership_detail_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selected != 'settings'"
                        :class="[route().current('boats.dashboard.dealership.edit') || route().current('boats.dashboard.dealership.admin-settings') || route().current('boats.dashboard.dealership.admin.settings') || route().current('retail.dashboard.garage.admin.assignFeeType') || route().current('boats.dashboard.dealership.reviews.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Dealership Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('boats.dashboard.dealership.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('boats.dashboard.dealership.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Dealership Info</span>
                                </Link>
                            </div>
                            <!-- dealership admin settings -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('boats.dashboard.dealership.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('boats.dashboard.dealership.admin-settings') || route().current('boats.dashboard.dealership.admin.settings') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('boats.dashboard.dealership.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('boats.dashboard.dealership.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Boats -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_products']) && sideBarPermissions('view_boats_tab')) && (selectedBusinessId != 'administration') && selectedModule != 'settings' &&  selectedBusinessId"
                        :class="[route().current('boats.dashboard.dealership.boats.*') || route().current('boats.dashboard.boat.media.index') || route().current('boats.dashboard.boat.boat-tags.index') || route().current('boats.dashboard.boat.attribute-tags.index') || route().current('boats.dashboard.boat.discount.index') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('boats.dashboard.dealership.boats.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('boats.dashboard.dealership.boats.*') || route().current('boats.dashboard.boat.media.index') || route().current('boats.dashboard.boat.boat-tags.index') || route().current('boats.dashboard.boat.boat-tags.index') || route().current('boats.dashboard.boat.discount.index') ? 'active' : '']">
                            <span class="menu-title">Boats</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Boats contact form -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_contact_form']) && sideBarPermissions('view_boats_contact_form')) && (selectedBusinessId != 'administration') && selectedModule != 'settings'"
                        :class="[route().current('boats.dashboard.dealership.contact-form.*') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M21 9V11C21 11.6 20.6 12 20 12H14V8H20C20.6 8 21 8.4 21 9ZM10 8H4C3.4 8 3 8.4 3 9V11C3 11.6 3.4 12 4 12H10V8Z"
                                            fill="black" />
                                        <path d="M15 2C13.3 2 12 3.3 12 5V8H15C16.7 8 18 6.7 18 5C18 3.3 16.7 2 15 2Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M9 2C10.7 2 12 3.3 12 5V8H9C7.3 8 6 6.7 6 5C6 3.3 7.3 2 9 2ZM4 12V21C4 21.6 4.4 22 5 22H10V12H4ZM20 12V21C20 21.6 19.6 22 19 22H14V12H20Z"
                                            fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('boats.dashboard.dealership.contact-form.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('boats.dashboard.dealership.contact-form.*') ? 'active' : '']">
                            <span class="menu-title">Contact Forms</span>
                            </Link>
                        </span>
                    </div>

                    <!-- News section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_news_tab')"
                        :class="[route().current('news.dashboard.news.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-newspaper"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('news.dashboard.news.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.news.*') ? 'active' : '']">
                                <span class="menu-title">News</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- obituaries products -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_obituaries_tab')"
                        :class="[route().current('obituaries.dashboard.obituaries.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-users"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('obituaries.dashboard.obituaries.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.obituaries.*') ? 'active' : '']">
                                <span class="menu-title">Obituaries</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- blogs products -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_blogs')"
                        :class="[route().current('blogs.dashboard.blogs.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-blog"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('blogs.dashboard.blogs.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.blogs.*') ? 'active' : '']">
                                <span class="menu-title">Blogs</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- recipes products -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_recipes_tab')"
                        :class="[route().current('recipes.dashboard.recipes.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-utensils"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('recipes.dashboard.recipes.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.recipes.*') ? 'active' : '']">
                                <span class="menu-title">Recipes</span>
                                </Link>
                            </div>
                        </span>
                    </div>
                    <!-- events -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_events_tab')"
                        :class="[route().current('events.dashboard.events.*') || route().current('events.dashboard.events.tags.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fas fa-blog"></i>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('events.dashboard.events.index', getSelectedModuleValue())"
                                    :class="[route().current('events.dashboard.events.*') || route().current('events.dashboard.events.tags.index') ? 'active' : '']">
                                <span class="menu-title">Events</span>
                                </Link>
                            </div>
                        </span>
                    </div>


                    <!-- posts products -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_posts_tab') && selectedBusinessId"
                        :class="[route().current('posts.dashboard.posts.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-sticky-note"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('posts.dashboard.posts.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.posts.*') ? 'active' : '']">
                                <span class="menu-title">Posts</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- classifieds products -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_classifieds_tab')"
                        :class="[route().current('classifieds.dashboard.classifieds.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-bullhorn"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('classifieds.dashboard.classifieds.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.classifieds.*') ? 'active' : '']">
                                <span class="menu-title">Marketplace</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- classifieds communication portal -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_contact_form']) && sideBarPermissions('view_communication_portal') && $page.props.auth.user.user_type == 'admin' && this.getSelectedModuleName() === 'classifieds'"
                        :class="[route().current('classifieds.dashboard.communication-portal.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-people-arrows"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item">
                                <Link
                                    :href="route('classifieds.dashboard.communication-portal.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.classifieds.*') ? 'active' : '']">
                                <span class="menu-title">Communication Portal</span>
                                </Link>
                            </div>
                        </span>
                    </div>



                    <!-- tasker settings -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="sideBarPermissions('view_tasker_settings') && $page.props.auth.user.user_type == 'customer' && this.getSelectedModuleName() === 'taskers'"
                        :class="[route().current('taskers.dashboard.user-settings') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item">
                                <Link
                                    :href="route('taskers.dashboard.user-settings', [getSelectedModuleValue(), $page.props.auth.user.id])"
                                    :class="[route().current('taskers.dashboard.user-settings') ? 'active' : '']">
                                <span class="menu-title">Settings</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- tasker communication portal -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_contact_form']) && sideBarPermissions('view_communication_portal') && $page.props.auth.user.user_type == 'admin' && this.getSelectedModuleName() === 'taskers'"
                        :class="[route().current('taskers.dashboard.communication-portal.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-people-arrows"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item">
                                <Link
                                    :href="route('taskers.dashboard.communication-portal.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.taskers.*') ? 'active' : '']">
                                <span class="menu-title">Communication Portal</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- taskers tab -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_products']) && sideBarPermissions('view_taskers_tab')"
                        :class="[route().current('taskers.dashboard.taskers.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-bullhorn"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item" v-if="checkUserPermissions('view_products')">
                                <Link :href="route('taskers.dashboard.taskers.index', getSelectedModuleValue())"
                                    :class="[route().current('dashboard.taskers.*') ? 'active' : '']">
                                <span class="menu-title">Tasks</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_employers_tab')) && selected != 'settings'"
                        :class="[route().current('employment.dashboard.employers.index') || route().current('employment.dashboard.employers.create') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('employment.dashboard.employers.index', getSelectedModuleValue())"
                                :class="[route().current('employment.dashboard.employers.index') || route().current('employment.dashboard.employers.create') ? 'active' : '']">
                            <span class="menu-title">Employers</span>
                            </Link>
                        </span>
                    </div>

                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'edit_business']) && sideBarPermissions('view_employer_detail_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selected != 'settings'"
                        :class="[route().current('employment.dashboard.employers.edit') || route().current('employment.dashboard.employers.admin-settings') || route().current('employment.dashboard.employers.employerschedule.index') || route().current('employment.dashboard.employers.social-links.index') || route().current('boats.dashboard.dealership.admin-settings') || route().current('boats.dashboard.dealership.admin.settings') || route().current('employment.dashboard.employers.reviews.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Employer Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('employment.dashboard.employers.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('employment.dashboard.employers.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Employer Info</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('employment.dashboard.employers.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('employment.dashboard.employers.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('employment.dashboard.employers.social-links.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('employment.dashboard.employers.social-links.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Social Links</span>
                                </Link>
                            </div>

                            <!-- Employer schedule -->
                            <div class="menu-item" v-if="checkUserPermissions('view_business_schedule_time')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('employment.dashboard.employers.employerschedule.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('employment.dashboard.employers.employerschedule.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Employer Schedule</span>
                                </Link>
                            </div>

                            <!-- employer tags -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('employment.dashboard.employers.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('employment.dashboard.employers.admin-settings') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Tab -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_products']) && sideBarPermissions('view_employment_posts_tab')) && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('employment.dashboard.employers.posts.*') || route().current('employment.dashboard.post.media.index') || route().current('employment.dashboard.post.post-tags.index') || route().current('employment.dashboard.post.attribute-tags.index') || route().current('employment.dashboard.post.reviews.index') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('employment.dashboard.employers.posts.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('employment.dashboard.employers.posts.*') || route().current('employment.dashboard.post.media.index') || route().current('employment.dashboard.post.post-tags.index') || route().current('employment.dashboard.post.attribute-tags.index') || route().current('employment.dashboard.post.reviews.index') ? 'active' : '']">
                            <span class="menu-title">Posts</span>
                            </Link>
                        </span>
                    </div>

                    <!-- view services business -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_services_bueinsess'))  && selected != 'settings'"
                        :class="[route().current('services.dashboard.service-provider.index') || route().current('services.dashboard.service-provider.create') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('services.dashboard.service-provider.index', getSelectedModuleValue())"
                                :class="[route().current('services.dashboard.service-provider.index') || route().current('services.dashboard.service-provider.create') ? 'active' : '']">
                            <span class="menu-title">Service Providers</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Dealership services details -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'edit_business']) && sideBarPermissions('view_business_detail_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selected != 'settings'"
                        :class="[route().current('services.dashboard.service-provider.edit') || route().current('services.dashboard.service-provider.reviews.index') || route().current('services.dashboard.service-provider.social-links.index') || route().current('services.dashboard.service-provider.businessschedules.index') || route().current('services.dashboard.service-provider.admin-settings') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Service Provider Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('services.dashboard.service-provider.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('services.dashboard.service-provider.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Service Provider Info</span>
                                </Link>
                            </div>
                            <!-- Business schedule -->
                            <div class="menu-item" v-if="checkUserPermissions('view_business_schedule_time')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('services.dashboard.service-provider.businessschedules.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('services.dashboard.service-provider.businessschedules.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Service Provider Schedule</span>
                                </Link>
                            </div>

                            <!-- business reviews -->
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('services.dashboard.service-provider.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('services.dashboard.service-provider.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('edit_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('services.dashboard.service-provider.social-links.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('services.dashboard.service-provider.social-links.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Social Links</span>
                                </Link>
                            </div>
                            <!-- business tags -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('services.dashboard.service-provider.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('services.dashboard.service-provider.admin-settings') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- services section -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions('view_products') && sideBarPermissions('view_services_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selectedBusinessId"
                        :class="[route().current('services.dashboard.service-provider.services.*') || route().current('services.dashboard.service.media.index') || route().current('services.dashboard.services.service-tags.index') || route().current('services.dashboard.services.media.*') || route().current('services.dashboard.services.comments.index') ? 'show here' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M21 9V11C21 11.6 20.6 12 20 12H14V8H20C20.6 8 21 8.4 21 9ZM10 8H4C3.4 8 3 8.4 3 9V11C3 11.6 3.4 12 4 12H10V8Z"
                                            fill="black" />
                                        <path d="M15 2C13.3 2 12 3.3 12 5V8H15C16.7 8 18 6.7 18 5C18 3.3 16.7 2 15 2Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M9 2C10.7 2 12 3.3 12 5V8H9C7.3 8 6 6.7 6 5C6 3.3 7.3 2 9 2ZM4 12V21C4 21.6 4.4 22 5 22H10V12H4ZM20 12V21C20 21.6 19.6 22 19 22H14V12H20Z"
                                            fill="black" />
                                    </svg>
                                </span>
                            </span>
                            <Link
                                :href="route('services.dashboard.service-provider.services.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('services.dashboard.service-provider.services.*') || route().current('services.dashboard.services.media.index') || route().current('services.dashboard.services.service-tags.index') || route().current('services.dashboard.services.service-tags.index') ? 'active' : '']">
                            <span class="menu-title">Services</span>
                            </Link>
                        </span>
                    </div>

                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_contact_form']) && sideBarPermissions('view_serveices_communication_portal') && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('services.dashboard.service-provider.communication-portal.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-people-arrows"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item">
                                <Link
                                    :href="route('services.dashboard.service-provider.communication-portal.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('dashboard.services.*') ? 'active' : '']">
                                <span class="menu-title">Communication Portal</span>
                                </Link>
                            </div>
                        </span>
                    </div>

                    <!-- government -->

                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_department_tab')) && selected != 'settings'"
                        :class="[route().current('government.dashboard.departments.index') || route().current('government.dashboard.departments.create') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('government.dashboard.departments.index', getSelectedModuleValue())"
                                :class="[route().current('government.dashboard.departments.index') || route().current('government.dashboard.departments.create') ? 'active' : '']">
                            <span class="menu-title">Departments</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Department details -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'edit_business']) && sideBarPermissions('view_department_detail_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selected != 'settings'"
                        :class="[route().current('government.dashboard.departments.edit') || route().current('government.dashboard.department.admin-settings') || route().current('boats.dashboard.dealership.admin.settings') || route().current('retail.dashboard.garage.admin.assignFeeType') || route().current('government.dashboard.department.reviews.index') || route().current('government.dashboard.department.departmentschedules.index') || route().current('government.dashboard.department.social-links.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Department Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('government.dashboard.departments.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('government.dashboard.departments.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Department Info</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_business_schedule_time')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('government.dashboard.department.departmentschedules.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('government.dashboard.department.departmentschedules.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Department Schedule</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('government.dashboard.department.social-links.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('government.dashboard.department.social-links.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Social Links</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('government.dashboard.department.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('government.dashboard.department.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                            <!-- business tags -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('government.dashboard.department.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('government.dashboard.department.admin-settings') || route().current('retail.dashboard.business.admin.settings') || route().current('retail.dashboard.business.admin.assignFeeType') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_government_staff']) && sideBarPermissions('view_government_staffs_tab')) && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('government.dashboard.department.staffs.*')  ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none">
                                            <path
                                                d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z"
                                                fill="black"></path>
                                            <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black"></rect>
                                        </svg>
                                    </span>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('government.dashboard.department.staffs.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('government.dashboard.department.staffs.index') ? 'active' : '']">
                            <span class="menu-title">Government Staff</span>
                            </Link>
                        </span>
                    </div>
                    <!-- Government Posts -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_products']) && sideBarPermissions('view_government_posts_tab')) && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('government.dashboard.department.posts.*') || route().current('government.dashboard.post.media.index') || route().current('government.dashboard.post.post-tags.index') || route().current('government.dashboard.post.attribute-tags.index') || route().current('government.dashboard.post.reviews.index') || route().current('government.dashboard.post.comments.index') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('government.dashboard.department.posts.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('government.dashboard.department.posts.*') || route().current('government.dashboard.post.media.index') || route().current('government.dashboard.post.post-tags.index') || route().current('government.dashboard.post.attribute-tags.index') ? 'active' : '']">
                            <span class="menu-title">Posts</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Government communication portal -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_contact_form']) && sideBarPermissions('view_government_communication_portal') && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('government.dashboard.department.communication-portal.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-people-arrows"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item">
                                <Link
                                    :href="route('government.dashboard.department.communication-portal.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('dashboard.government.*') ? 'active' : '']">
                                <span class="menu-title">Communication Portal</span>
                                </Link>
                            </div>
                        </span>
                    </div>


                    <!-- orgnaization list in notices module -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_organization_tab')) && (selectedBusinessId != 'administration') && selected != 'settings'"
                        :class="[route().current('notices.dashboard.organizations.index') || route().current('notices.dashboard.organizations.create') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('notices.dashboard.organizations.index', getSelectedModuleValue())"
                                :class="[route().current('notices.dashboard.organizations.index') || route().current('notices.dashboard.organizations.create') ? 'active' : '']">
                            <span class="menu-title">Organizations</span>
                            </Link>
                        </span>
                    </div>

                    <!-- Organization details -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'edit_business']) && sideBarPermissions('view_organization_detail_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selected != 'settings'"
                        :class="[route().current('notices.dashboard.organizations.edit') || route().current('notices.dashboard.organization.admin-settings') || route().current('boats.dashboard.dealership.admin.settings') || route().current('notices.dashboard.organization.reviews.index') || route().current('notices.dashboard.organization.organizationSchedules.index') || route().current('notices.dashboard.organization.social-links.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Organization Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('notices.dashboard.organizations.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('notices.dashboard.organizations.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Organization Info</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_business_schedule_time')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('notices.dashboard.organization.organizationSchedules.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('notices.dashboard.organization.organizationSchedules.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Organization Schedule</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('notices.dashboard.organization.social-links.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('notices.dashboard.organization.social-links.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Social Links</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('notices.dashboard.organization.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('notices.dashboard.organization.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                            <!-- business tags -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('notices.dashboard.organization.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('notices.dashboard.organization.admin-settings') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Notices -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_products']) && sideBarPermissions('view_notices_tab')) && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('notices.dashboard.organization.notices.*') || route().current('notices.dashboard.notices.media.index') || route().current('notices.dashboard.notices.notice-tags.index') || route().current('notices.dashboard.notices.reviews.index') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('notices.dashboard.organization.notices.index', [getSelectedModuleValue(), selectedBusinessId])"
                                :class="[route().current('notices.dashboard.organization.notices.*') || route().current('notices.dashboard.notices.media.index') || route().current('notices.dashboard.notices.notice-tags.index') || route().current('notices.dashboard.notices.reviews.index') ? 'active' : '']">
                            <span class="menu-title">Notices</span>
                            </Link>
                        </span>
                    </div>

                    <!-- brokers list -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_business']) && sideBarPermissions('view_brokers_tab')) && selected != 'settings'"
                        :class="[route().current('real-estate.dashboard.brokers.index') || route().current('real-estate.dashboard.brokers.create') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link :href="route('real-estate.dashboard.brokers.index', getSelectedModuleValue())"
                                :class="[route().current('real-estate.dashboard.brokers.index') || route().current('real-estate.dashboard.brokers.create') ? 'active' : '']">
                            <span class="menu-title">Brokers</span>
                            </Link>
                        </span>
                    </div>

                     <!-- real estate brokerage requests -->
                     <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions('approve_business') && sideBarPermissions('view_broker_request_tab') && selectedBusinessId == 'administration')"
                        :class="[route().current('real-estate.dashboard.broker.request') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('real-estate.dashboard.broker.request', [getSelectedModuleValue()])"
                                :class="[route().current('real-estate.dashboard.broker.request') ? 'active' : '']">
                            <span class="menu-title">Brokerage Requests</span>
                            </Link>
                        </span>
                    </div>
                    
                    <!-- Broker details -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_business', 'edit_business']) && sideBarPermissions('view_broker_detail_tab') && selectedBusinessId && selectedBusinessId != 'administration' && selected != 'settings'"
                        :class="[route().current('real-estate.dashboard.brokers.edit') || route().current('real-estate.dashboard.broker.admin-settings') || route().current('real-estate.dashboard.broker.reviews.index') || route().current('real-estate.dashboard.broker.social-links.index') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                                fill="#000000" />
                                            <path
                                                d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Broker Details</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!-- business information -->
                            <div class="menu-item" v-if="checkUserPermissions('edit_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('real-estate.dashboard.brokers.edit', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('real-estate.dashboard.brokers.edit') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Broker Info</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_business')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('real-estate.dashboard.broker.social-links.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('real-estate.dashboard.broker.social-links.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Social Links</span>
                                </Link>
                            </div>
                            <div class="menu-item" v-if="checkUserPermissions('view_reviews')">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('real-estate.dashboard.broker.reviews.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('real-estate.dashboard.broker.reviews.index') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reviews</span>
                                </Link>
                            </div>
                            <!-- business tags -->
                            <div class="menu-item"
                                v-if="checkUserPermissions('view_admin_settings') && $page.props.auth.user.user_type == 'admin'">
                                <Link v-if="selectedBusinessId" class="menu-link"
                                    :href="route('real-estate.dashboard.broker.admin-settings', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('real-estate.dashboard.broker.admin-settings') ? 'active' : '']">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Admin Settings</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- real estate agents -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions('view_agents') && sideBarPermissions('view_agents_tab')) && (selectedBusinessId != 'administration') && selected != 'settings'"
                        :class="[route().current('real-estate.dashboard.agents.*') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('real-estate.dashboard.agents.index', [getSelectedModuleValue(), this.getSelectedBusinessValue()])"
                                :class="[route().current('real-estate.dashboard.agents.*') ? 'active' : '']">
                            <span class="menu-title">Agents</span>
                            </Link>
                        </span>
                    </div>

                    <!-- real estate properties -->
                    <div data-kt-menu-trigger="click"
                        v-if="(checkUserPermissions(['view_products']) && sideBarPermissions('view_properties_tab')) && (selectedBusinessId != 'administration') && selected != 'settings' && selectedBusinessId"
                        :class="[route().current('real-estate.dashboard.broker.properties.*') || route().current('real-estate.dashboard.properties.*') ? 'show here' : '']"
                        class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <Link
                                :href="route('real-estate.dashboard.broker.properties.index', [getSelectedModuleValue(), this.getSelectedBusinessValue()])"
                                :class="[route().current('real-estate.dashboard.broker.properties.index') || route().current('real-estate.dashboard.broker.properties.create') ? 'active' : '']">
                            <span class="menu-title">Properties</span>
                            </Link>
                        </span>
                    </div>

                    <!-- real estate communication portal -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion"
                        v-if="checkUserPermissions(['view_contact_form']) && sideBarPermissions('view_real_estate_communication_portal') && (selectedBusinessId != 'administration') && selectedModule != 'settings' && selectedBusinessId"
                        :class="[route().current('real-estate.dashboard.broker.communication-portal.*') ? 'here show' : '']">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-people-arrows"></i>
                                    </span>
                                </span>
                            </span>
                            <div class="menu-item">
                                <Link
                                    :href="route('real-estate.dashboard.broker.communication-portal.index', [getSelectedModuleValue(), selectedBusinessId])"
                                    :class="[route().current('dashboard.real-estate.*') ? 'active' : '']">
                                <span class="menu-title">Communication Portal</span>
                                </Link>
                            </div>
                        </span>
                    </div>
                </div>
                <!--end::Menu-->
            </div>
        </div>
        <div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
        </div>
    </div>
</template>

<script>
import {
    Link
} from '@inertiajs/inertia-vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Helpers from '@/Mixins/Helpers'
import Select2 from 'vue3-select2-component';

export default {
    components: {
        Link,
        ApplicationLogo,
        Select2
    },

    props: ['type'],

    data() {
        const MODULES = ['news', 'posts', 'blogs', 'obituaries', 'recipes', 'classifieds', 'taskers', 'events'];
        return {
            urlLastSegment: null,
            urlSecondLastSegment: null,
            selected: 'settings',
            selectedBusinessId: null,
            moduleTags: [],
            modules: MODULES
        }
    },
    methods: {
        getValue(event) {
            localStorage.selectedModule = event.id
            // this.$inertia.visit(route('dashboard.panel', localStorage.selectedModule))
            var URL = this.moduleURL(event.text.toLowerCase());
            localStorage.selectedModuleName = this.makeTagSlug(event.text.toLowerCase())
            this.$inertia.visit(route(URL, localStorage.selectedModule))
            this.emitter.emit('stripe-connect')
        },
        getModuleTags() {
            this.moduleTags = []
            window.axios.get(route('module-tags'))
                .then((response) => {
                    response.data.moduleTags.forEach((value, index) => {
                        let moduleTags = {
                            'id': value.id,
                            'text': value.name
                        }
                        this.moduleTags.push(moduleTags)
                    })
                    var setting = {
                        'id': 'settings',
                        'text': 'Settings'
                    }
                    this.moduleTags.push(setting)
                    if (!localStorage.selectedModuleName) {
                        this.selected = 'settings'
                        localStorage.selectedModule = this.selected
                        localStorage.selectedModuleName = this.selected
                        this.selected = localStorage.selectedModule
                    }
                    if (localStorage.selectedBusiness != 'null') {
                        this.selectedBusiness = localStorage.selectedBusiness
                        this.emitter.emit("selected-business", localStorage.selectedBusiness);
                    } else {
                        localStorage.selectedBusiness = this.selectedBusiness
                        this.emitter.emit("selected-business", localStorage.selectedBusiness);
                    }
                })
        },
        completeRoute() {
            return this.moduleURL(route().current().split(".")[0]);
        }
    },
    beforeMount() {
        this.getModuleTags()
        this.emitter.on("selected-business", business => {
            this.selectedBusinessId = business;
        });
        if (localStorage.getItem("selectedModule") != null) {
            this.selected = localStorage.selectedModule
        } else {
            this.selected = 'settings'
            localStorage.selectedModule = this.selected
            localStorage.selectedModuleName = this.selected
            this.selected = localStorage.selectedModule
        }
    },
    mounted() {
        if (window.KTMenu) {
            window.KTMenu.init()
        }
        if (window.KTToggle) {
            window.KTToggle.init();
        }
        if (window.KTScroll) {
            window.KTScroll.init();
        }
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        this.emitter.on('subscription_changed', () => {
            this.getModuleTags()
        })
    },
    created: function () {
        this.urlLastSegment = window.location.pathname.split('/').pop();
        this.urlSecondLastSegment = window.location.pathname.split('/').slice(-2)[0];
    },
    mixins: [Helpers],
}
</script>
