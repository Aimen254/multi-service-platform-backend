<template>
    <!--begin::Header-->
    <div id="kt_header" style="" class="header align-items-stretch">
        <!--begin::Container-->
        <div class="container-fluid d-flex align-items-stretch justify-content-between">
            <!--begin::Wrapper-->
            <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                <!--begin::Navbar-->
                <div class="d-flex align-items-stretch" id="kt_header_nav">
                    <!--begin::Menu wrapper-->
                    <div class="header-menu py-2 align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" v-if="!modules.includes(this.getSelectedModuleName())"
                        data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                        data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end"
                        data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true"
                        data-kt-swapper-mode="prepend"
                        data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                        <!-- section where dropdown selection will be selected -->
                        <select2 v-if="(category !== 'settings' && dropdownShow)" class="business" v-model="selectedBusiness" :options="businessOptions"
                        @select="getBusinessSettings($event)"/>
                    </div>
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Navbar-->
                <!--begin::Toolbar wrapper-->
                <div class="d-flex align-items-stretch flex-shrink-0">
                    <!--begin::Activities-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3">
                        <!--begin::Drawer toggle-->
                        <!-- <div class="btn btn-icon btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px"
                            id="kt_activities_toggle">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <rect x="8" y="9" width="3" height="10" rx="1.5" fill="black" />
                                    <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="black" />
                                    <rect x="18" y="11" width="3" height="8" rx="1.5" fill="black" />
                                    <rect x="3" y="13" width="3" height="6" rx="1.5" fill="black" />
                                </svg>
                            </span>
                        </div> -->
                        <!--end::Drawer toggle-->
                    </div>
                    <!--end::Activities-->
                    <!--begin::Notifications-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3">
                        <!--begin::Menu- wrapper-->
                        <div class="btn btn-icon btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px"
                            data-kt-menu-trigger="click" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="notifications" data-kt-menu-attach="parent"
                            data-kt-menu-placement="bottom-end">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen022.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M11.2929 2.70711C11.6834 2.31658 12.3166 2.31658 12.7071 2.70711L15.2929 5.29289C15.6834 5.68342 15.6834 6.31658 15.2929 6.70711L12.7071 9.29289C12.3166 9.68342 11.6834 9.68342 11.2929 9.29289L8.70711 6.70711C8.31658 6.31658 8.31658 5.68342 8.70711 5.29289L11.2929 2.70711Z"
                                        fill="black" />
                                    <path
                                        d="M11.2929 14.7071C11.6834 14.3166 12.3166 14.3166 12.7071 14.7071L15.2929 17.2929C15.6834 17.6834 15.6834 18.3166 15.2929 18.7071L12.7071 21.2929C12.3166 21.6834 11.6834 21.6834 11.2929 21.2929L8.70711 18.7071C8.31658 18.3166 8.31658 17.6834 8.70711 17.2929L11.2929 14.7071Z"
                                        fill="black" />
                                    <path opacity="0.3"
                                        d="M5.29289 8.70711C5.68342 8.31658 6.31658 8.31658 6.70711 8.70711L9.29289 11.2929C9.68342 11.6834 9.68342 12.3166 9.29289 12.7071L6.70711 15.2929C6.31658 15.6834 5.68342 15.6834 5.29289 15.2929L2.70711 12.7071C2.31658 12.3166 2.31658 11.6834 2.70711 11.2929L5.29289 8.70711Z"
                                        fill="black" />
                                    <path opacity="0.3"
                                        d="M17.2929 8.70711C17.6834 8.31658 18.3166 8.31658 18.7071 8.70711L21.2929 11.2929C21.6834 11.6834 21.6834 12.3166 21.2929 12.7071L18.7071 15.2929C18.3166 15.6834 17.6834 15.6834 17.2929 15.2929L14.7071 12.7071C14.3166 12.3166 14.3166 11.6834 14.7071 11.2929L17.2929 8.70711Z"
                                        fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                            <!--begin::Heading-->
                            <div class="d-flex flex-column bgi-no-repeat rounded-top"
                                style="background-image:url('/images/project/pattern-1.jpg')">
                                <!--begin::Title-->
                                <h3 class="text-white fw-bold px-9 mt-10 mb-6">Notifications
                                    <span class="fs-8 opacity-75 ps-3">24 reports</span></h3>
                                <!--end::Title-->
                                <!--begin::Tabs-->
                                <ul class="nav nav-stretch fw-bold px-9">
                                    <li class="nav-item d-none">
                                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active"
                                            data-bs-toggle="tab" href="#kt_topbar_notifications_1">Alerts</a>
                                    </li>
                                </ul>
                                <!--end::Tabs-->
                            </div>
                            <!--end::Heading-->
                            <!--begin::Tab content-->
                            <div class="tab-content">
                                <!--begin::Tab panel-->
                                <div class="tab-pane fade show active" id="kt_topbar_notifications_1" role="tabpanel">
                                    <!--begin::Items-->
                                    <div class="scroll-y mh-325px my-5 px-8">
                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack py-4">
                                            <!--begin::Section-->
                                            <div class="d-flex align-items-center">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-35px me-4">
                                                    <span class="symbol-label bg-light-primary">
                                                        <!--begin::Svg Icon | path: icons/duotune/maps/map001.svg-->
                                                        <span class="svg-icon svg-icon-2 svg-icon-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <path opacity="0.3"
                                                                    d="M6 22H4V3C4 2.4 4.4 2 5 2C5.6 2 6 2.4 6 3V22Z"
                                                                    fill="black" />
                                                                <path
                                                                    d="M18 14H4V4H18C18.8 4 19.2 4.9 18.7 5.5L16 9L18.8 12.5C19.3 13.1 18.8 14 18 14Z"
                                                                    fill="black" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div class="mb-0 me-2">
                                                    <a href="#"
                                                        class="fs-6 text-gray-800 text-hover-primary fw-bolder">Project
                                                        Breafing</a>
                                                    <div class="text-gray-400 fs-7">Product launch status update</div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Section-->
                                            <!--begin::Label-->
                                            <span class="badge badge-light fs-8">21 Jan</span>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Tab panel-->
                            </div>
                            <!--end::Tab content-->
                        </div>
                        <!--end::Menu-->
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::Notifications-->
                    <!--begin::User menu-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                        <!--begin::Menu wrapper-->
                        <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click"
                            data-kt-menu-attach="parent" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="bottom" data-bs-original-title="avatar" data-kt-menu-placement="bottom-end">
                            <img :src="getImage(profilAvatar)" alt="user" />
                        </div>
                        <!--begin::User account menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-auto"
                            data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-50px me-5">
                                        <img alt="Logo" :src="getImage(profilAvatar)" />
                                    </div>
                                    <!--end::Avatar-->
                                    <!--begin::Username-->
                                    <div class="d-flex flex-column">
                                        <div class="fw-bolder d-flex align-items-center fs-5">
                                            {{ $page.props.auth.user.first_name }}  {{ $page.props.auth.user.last_name }}
                                            <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2 text-wrap">
                                                {{ $page.props.auth.user.role.title }}
                                            </span>
                                        </div>
                                        <a class="fw-bold text-muted text-hover-primary fs-7">
                                            {{ $page.props.auth.user.email }}
                                        </a>
                                    </div>
                                    <!--end::Username-->
                                </div>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu separator-->
                            <div class="separator my-2"></div>
                            <!--end::Menu separator-->
                            <!--begin::Menu item-->
                            <Link class="menu-item px-5" :href="route('dashboard.profile.index')">
                                <span class="menu-link px-5">My Profile</span>
                            </Link>
                            <!--end::Menu item-->
                            <!--begin::Menu separator-->
                            <div class="separator my-2"></div>
                            <!--end::Menu separator-->
                            <!--begin::Menu item-->
                            <!-- <div class="menu-item px-5" data-kt-menu-trigger="hover"
                                data-kt-menu-placement="left-start">
                                <a href="#" class="menu-link px-5">
                                    <span class="menu-title position-relative">Language
                                        <span
                                            class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">English
                                            <img class="w-15px h-15px rounded-1 ms-2"
                                                src="assets/media/flags/united-states.svg" alt="" /></span></span>
                                </a>
                                <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                    <div class="menu-item px-3">
                                        <a href="../../demo1/dist/account/settings.html"
                                            class="menu-link d-flex px-5 active">
                                            <span class="symbol symbol-20px me-4">
                                                <img class="rounded-1" src="assets/media/flags/united-states.svg"
                                                    alt="" />
                                            </span>English</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                            <span class="symbol symbol-20px me-4">
                                                <img class="rounded-1" src="assets/media/flags/spain.svg" alt="" />
                                            </span>Spanish</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                            <span class="symbol symbol-20px me-4">
                                                <img class="rounded-1" src="assets/media/flags/germany.svg" alt="" />
                                            </span>German</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                            <span class="symbol symbol-20px me-4">
                                                <img class="rounded-1" src="assets/media/flags/japan.svg" alt="" />
                                            </span>Japanese</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                            <span class="symbol symbol-20px me-4">
                                                <img class="rounded-1" src="assets/media/flags/france.svg" alt="" />
                                            </span>French</a>
                                    </div>
                                </div>
                            </div> -->
                            <!--begin::Menu item-->
                            <div class="menu-item px-5">
                                <Link @click="deleteStorage" :href="route('logout')" :class="'menu-link px-5'">
                                    Sign Out
                                </Link>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::User account menu-->
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::User menu-->
                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Header-->

    <!--begin::Activities drawer-->
		<div id="kt_activities" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'lg': '900px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activities_toggle" data-kt-drawer-close="#kt_activities_close">
			<div class="card shadow-none rounded-0">
				<!--begin::Header-->
				<div class="card-header" id="kt_activities_header">
					<h3 class="card-title fw-bolder text-dark">Activity Logs</h3>
					<div class="card-toolbar">
						<button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
							<span class="svg-icon svg-icon-1">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
									<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</button>
					</div>
				</div>
				<!--end::Header-->
				<!--begin::Body-->
				<div class="card-body position-relative" id="kt_activities_body">
					<!--begin::Content-->
					<div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
						<!--begin::Timeline items-->
						<div class="timeline">
							<!--begin::Timeline item-->
							<div class="timeline-item">
								<!--begin::Timeline line-->
								<div class="timeline-line w-40px"></div>
								<!--end::Timeline line-->
								<!--begin::Timeline icon-->
								<div class="timeline-icon symbol symbol-circle symbol-40px me-4">
									<div class="symbol-label bg-light">
										<!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
										<span class="svg-icon svg-icon-2 svg-icon-gray-500">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="black" />
												<path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="black" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</div>
								</div>
								<!--end::Timeline icon-->
								<!--begin::Timeline content-->
								<div class="timeline-content mb-10 mt-n1">
									<!--begin::Timeline heading-->
									<div class="pe-3 mb-5">
										<!--begin::Title-->
										<div class="fs-5 fw-bold mb-2">There are 2 new tasks for you in “AirPlus Mobile APp” project:</div>
										<!--end::Title-->
										<!--begin::Description-->
										<div class="d-flex align-items-center mt-1 fs-6">
											<!--begin::Info-->
											<div class="text-muted me-2 fs-7">Added at 4:23 PM by</div>
											<!--end::Info-->
											<!--begin::User-->
											<div class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="Nina Nilson">
												<img :src="getImage(profilAvatar)" alt="img" />
											</div>
											<!--end::User-->
										</div>
										<!--end::Description-->
									</div>
									<!--end::Timeline heading-->
								</div>
								<!--end::Timeline content-->
							</div>
							<!--end::Timeline item-->
						</div>
						<!--end::Timeline items-->
					</div>
					<!--end::Content-->
				</div>
				<!--end::Body-->
				<!--begin::Footer-->
				<div class="card-footer py-5 text-center" id="kt_activities_footer">
					<a href="../../demo1/dist/pages/user-profile/activity.html" class="btn btn-bg-body text-primary">View All Activities
					<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
					<span class="svg-icon svg-icon-3 svg-icon-primary">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
							<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
						</svg>
					</span>
					<!--end::Svg Icon--></a>
				</div>
				<!--end::Footer-->
			</div>
		</div>
		<!--end::Activities drawer-->
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import Helpers from '@/Mixins/Helpers'
    import firebase from 'firebase/app';
    import 'firebase/messaging';
    // import Select2 from 'vue3-select2-component';
    export default {
        props: ['categories'],
        components: {
            Link,
        },
        data() {
            return {
                businessOptions: [],
                category: this.categories,
                selectedBusiness: null,
                profilAvatar: this.$page.props.auth.user.avatar,
                dropdownShow: true,
                modules: ['news', 'posts', 'blogs', 'obituaries', 'recipes', 'classifieds', 'taskers','events']
            }
        },
        methods : {
            deleteStorage() {
                localStorage.removeItem('selectedModule')
                localStorage.removeItem('selectedBusiness')
                localStorage.removeItem('selectedModuleName')
            },
            getBusinessSettings(event) {
                localStorage.selectedBusiness = event.id
                this.selected = localStorage.selectedBusiness
                this.$inertia.visit(route('dashboard.panel', this.selected))
            },
            fetchBusinesses() {
                this.category = localStorage.selectedModule
                if(this.category !== 'settings') {
                    window.axios.get(route('dashboard.business.options', localStorage.selectedModule))
                        .then((response) => {
                            if(this.$page.props.auth.user.role.name == 'admin') {
                                let admin = {'id': 'administration' , 'text':'Administration'}
                                this.businessOptions.push(admin)
                            }
                            response.data.businesses.forEach((value, index) => {
                                let option = {'id': value.uuid, 'text': value.id + '-' + value.name}
                                if(index == 0) {
                                    console.log('index',index);
                                    this.selectedBusiness = option.id
                                }
                                this.businessOptions.push(option)
                            })
                            if (!this.checkBusinesses(response.data.businesses)) {
                                localStorage.selectedBusiness = this.selectedBusiness
                                this.selectedBusiness = localStorage.selectedBusiness
                            }
                            if(localStorage.selectedBusiness != 'null' && localStorage.selectedBusiness != 'undefined') {
                                this.selectedBusiness = localStorage.selectedBusiness
                                this.emitter.emit("selected-business", localStorage.selectedBusiness);
                            } else {

                                localStorage.selectedBusiness = this.selectedBusiness
                                if(response.data.businesses.length == 0) {
                                    localStorage.setItem("selectedBusiness", 'administration');
                                }
                                this.selectedBusiness = localStorage.selectedBusiness
                                this.emitter.emit("selected-business", localStorage.selectedBusiness);
                            }
                        })
                }
                if(this.category == 'settings') {
                    if(localStorage.selectedBusiness != null) {
                        localStorage.removeItem('selectedBusiness')
                    }
                }
            },
            checkBusinesses(businesses) {
                let test = [];
                test = businesses.filter((val, key) => {
                    if(localStorage.selectedBusiness == "administration") {
                        this.selected = localStorage.selectedBusiness
                        return localStorage.selectedBusiness
                    } else {
                        return val.uuid == localStorage.selectedBusiness
                    }
                })
                return test.length
            },
            handleBusinessDeleted() {
                // Refresh the business list when a business is deleted
                // Set selectedBusiness to the first available business or null if not found
                this.selectedBusiness = this.businessOptions.some(business => business.id === this.selectedBusiness)
                        ? this.selectedBusiness
                        : (this.businessOptions.length > 0 ? this.businessOptions[0].id : null);
                        localStorage.setItem("selectedBusiness", this.selectedBusiness);
                        this.emitter.emit("selected-business", localStorage.selectedBusiness);
                this.fetchBusinessList();
                  
            }
        },
        beforeMount () {
            this.fetchBusinesses();
        },
        mounted() {
            this.emitter.on('business-deleted', this.handleBusinessDeleted);
            this.fetchBusinesses();

            // if(!firebase.apps.length) {
            //     firebase.initializeApp({
            //         apiKey: "AIzaSyCONw2sJqLPSCSCZOSUW8vtuFir49LDZ-w",
            //         authDomain: "localverse-9d3d4.firebaseapp.com",
            //         databaseURL: "https://localverse-9d3d4-default-rtdb.firebaseio.com",
            //         projectId: "localverse-9d3d4",
            //         storageBucket: "localverse-9d3d4.appspot.com",
            //         messagingSenderId: "481054357290",
            //         appId: "1:481054357290:web:fa7bf34e9d43abf1f7d053",
            //         measurementId: "G-HPQ3DXR7FS"
            //     });
            // }
            // const messaging = firebase.messaging();
            // if ("serviceWorker" in navigator) {
            //     window.addEventListener("load", function () {
            //         navigator.serviceWorker
            //         .register("/firebase-messaging-sw.js")
            //         .then(function (registration) {
            //             firebase.messaging().useServiceWorker(registration);
            //             console.log("Service Worker registered with scope: ", registration.scope);
            //         })
            //         .catch(function (error) {
            //             console.log("Service Worker registration failed: ", error);
            //         });
            //     });
            // }
            // messaging.onMessage((payload) => {
            //     console.log(payload);
            //     const notification = new Notification(payload.notification.title, {
            //         body: payload.notification.message,
            //         timestamp: Date.now()
            //     });

                // notification.addEventListener('click', (event) => {
                //    this.$router.push('/retail')
                // })
            // });
            this.emitter.on("delete-media", avatar => {
                this.profilAvatar = null
            });

            this.emitter.on("change-media", avatar => {
                this.profilAvatar = this.$page.props.auth.user.avatar
            });

            //Hide drop down
            this.emitter.on("hide-dropdown", () => {
                this.dropdownShow = false
            });

            if (window.KTMenu) {
                window.KTMenu.init()
            }
        },

        mixins: [Helpers]
    }
</script>
