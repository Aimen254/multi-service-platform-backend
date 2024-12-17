import moment from 'moment';

export default {
    methods: {
        // formate time
        formatDate(dateString) {
            return dateString ? moment(dateString).format("Do MMM YYYY") : null;
        },
        // formate date & time
        formatDateTime(dateString) {
            return moment(dateString).format("Do MMM YYYY, h:mm:ss A");
        },
        // formate date & time Unix
        formatDateTimeUnix(dateString) {
            return moment.unix(dateString).format("Do MMM YYYY h:mm:ss A");
        },

        // generic function to get images
        getImage(path, isSaved = true, type = 'avatar', isExternal = 0) {
            const baseUrl = window.location.protocol + "//" + window.location.host
            if (isExternal) {
                console.log('path',path)
                return path
            } else {

                return path && path.length > 0 ?
                    (isSaved ? this.$page.props.file_url + '/' + path : path) :
                    baseUrl + this.placeHolderUrls(type)
            }

        },

        //to get placeholder urls
        placeHolderUrls(type) {
            var url = ''
            switch (type) {
                case 'category':
                case 'product':
                case 'variant':
                case 'news':
                case 'obituaries':
                case 'blogs':
                case 'taskers':
                    url = '/images/placeholder.png'
                    break
                case 'classifieds':
                    url = '/images/placeholder.png'
                    break
                case 'avatar':
                    url = '/images/no_avatar.jpg';
                    break;
                case 'banner':
                case 'secondaryBanner':
                    url = '/images/cover.jpg';
                    break;
                case 'logo':
                    url = '/images/placeholder.png';
                    break;
                case 'thumbnail':
                    url = '/images/placeholder.png';
                    break;
            }
            return url
        },

        // get role full name
        getRoleName(role) {
            const Name = role.replace('_', ' ');
            return Name.charAt(0).toUpperCase() + Name.slice(1);
        },

        // check user permissions
        checkUserPermissions(value) {
            const permissionsArray = this.$page.props.auth.user.permissions;

            // for single record
            if (typeof value == 'string') {
                return permissionsArray.includes(value);
            }

            // for array of permissions
            if (typeof value == 'object') {
                var result = false;
                value.forEach(element => {
                    let response = permissionsArray.includes(element);
                    if (response) {
                        return result = response;
                    }
                });
                return result;
            }
        },

        // conversion to boolean values
        booleanStatusValue(status) {
            var value = false;
            switch (status) {
                case 'active':
                    value = true
                    break;
                case '1':
                    value = true
                    break;
                default:
                    break;
            }
            return value
        },

        // conversion to required boolean values
        booleanRequiredValue(required) {
            var value = false;
            switch (required) {
                case 1:
                    value = true
                    break;

                default:
                    break;
            }
            return value
        },

        // get group name
        getGroupName(group) {
            return group ? group.replaceAll('_', ' ') : null;
        },

        // get sync categories
        syncedCategories(cat_id, business_categories) {
            var value = false;
            value = business_categories.find(function (category) {
                if (category.id === cat_id) {
                    return true;
                }
            })
            return value;
        },

        // get side bar permissions
        sideBarPermissions(value) {
            var permission = false;
            let condition = localStorage.getItem("selectedModuleName");
            switch (condition) {
                case 'settings':
                    var permissionsList = [
                        'view_users_tab',
                        'view_drivers_tab',
                        'view_settings_tab',
                        'view_business_tab',
                        'view_settings_tab'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'retail':
                    var permissionsList = [
                        'view_global_tags_tab',
                        'view_categories_tab',
                        'view_business_tab',
                        'view_business_details_tab',
                        'view_products_tab',
                        'view_orders_tab',
                        'view_categories_mapper_tab',
                        'view_standard_tag',
                        'view_tags_mapper_tab',
                        'view_industry_tag',
                        'view_global_tags',
                        'view_attributes',
                        'view_tag_hierarchies',
                        'view_coupon_tab',
                        'view_payment_method_tab',
                        'view_subscriptions'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'automotive':
                    var permissionsList = [
                        'view_tag_hierarchies',
                        'view_garage_tab',
                        'view_garage_detail_tab',
                        'view_vehilces_tab',
                        'view_contact_form',
                        'view_subscriptions'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'boats':
                    var permissionsList = [
                        'view_tag_hierarchies',
                        'view_boats_dealerships',
                        'view_dealership_detail_tab',
                        'view_boats_tab',
                        'view_boats_contact_form',
                        'view_subscriptions'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'news':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_news_tab',
                        'view_tab_headlines'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'obituaries':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_obituaries_tab'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'posts':
                    var permissionsList = [
                        'view_tag_hierarchies',
                        'view_posts_tab',
                        'view_subscriptions'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'blogs':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_blogs',
                        'view_tab_headlines'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'recipes':
                    var permissionsList = [
                        'view_tag_hierarchies',
                        'view_subscriptions',
                        'view_recipes_tab',
                        'view_tab_headlines'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'services':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_services_tab',
                        'view_services_bueinsess',
                        'view_business_detail_tab',
                        'view_serveices_communication_portal'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'marketplace':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_communication_portal',
                        'view_classifieds_tab'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'government':
                    var permissionsList = [
                        'view_tag_hierarchies',
                        'view_subscriptions',
                        'view_government_posts_tab',
                        'view_government_staffs_tab',
                        'view_department_tab',
                        'view_department_detail_tab',
                        'view_government_communication_portal',
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'taskers':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_taskers_tab',
                        'view_communication_portal',
                        'view_tasker_settings'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'employment':
                    var permissionsList = [
                        'view_tag_hierarchies',
                        'view_subscriptions',
                        'view_employment_posts_tab',
                        'view_employers_tab',
                        'view_employer_detail_tab'
                    ];
                    permission = permissionsList.includes(value);
                    break;

                case 'notices':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_organization_tab',
                        'view_organization_detail_tab',
                        'view_reviews',
                        'view_notices_tab'
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'real-estate':
                    var permissionsList = [
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_brokers_tab',
                        'view_broker_detail_tab',
                        'view_agents_tab',
                        'view_properties_tab',
                        'view_real_estate_communication_portal',
                        'view_broker_request_tab',
                    ];
                    permission = permissionsList.includes(value);
                    break;
                case 'events':
                    var permissionsList = [
                        'view_subscription_plan',
                        'view_subscriptions',
                        'view_tag_hierarchies',
                        'view_events_tab',
                        'view_events_detail_tab',
                          'view_attributes',


                    ];
                    permission = permissionsList.includes(value);
                    break;
                default:
                    break;
            }
            return permission;
        },

        // get selected track
        getSelectedModuleValue() {
            return localStorage.getItem("selectedModule")
        },

        // get selected track
        getSelectedBusinessValue() {
            return localStorage.getItem("selectedBusiness")
        },

        ellipsis(text, size = 30) {
            if (text.length <= size) {
                return text;
            }
            return text.substr(0, size) + '\u2026'
        },
        setTypeFormat(value) {
            return value.replaceAll("_", " ");
        },
        activeBusinessSideBarMenu(value) {
            let path = value.uri.split('/')
            let blockRoutes = ['colors', 'sizes', 'coupons'];
            if (path.indexOf('orders') > -1) {
                let blockOrders = ['orders']
                return !blockOrders.includes(path[6]);
            } else {
                return !blockRoutes.includes(path[3]);
            }
        },

        showTooltip() {

            // get tooltip if exist
            var element = document.querySelector('.tooltip');
            if (element) {
                // Remove the element from the DOM
                element.remove();
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        },

        hideTooltip() {
            $('[data-bs-toggle="tooltip"]').tooltip("hide");
        },

        orderStatus(data, order) {
            let statusArr = []
            data.forEach((value, index) => {
                switch (order.order_status.status) {
                    case 'pending':
                        if (value.status == "pending") {
                            statusArr.push(value)
                        }
                        if (value.status == "accepted") {
                            statusArr.push(value)
                        }
                        if (value.status == "rejected") {
                            statusArr.push(value)
                        }
                        break;
                    case 'accepted':
                        if (value.status == "accepted") {
                            statusArr.push(value)
                        }
                        if (value.status == "completed") {
                            statusArr.push(value)
                        }
                        if (value.status == "refunded" && order.payment_intent_id) {
                            statusArr.push(value)
                        }
                        if (order.order_type == 'pick_up' && value.status == "ready_for_collection") {
                            statusArr.push(value)
                        }
                        if (order.order_type == 'delivery' && value.status == "ready_for_delivery") {
                            statusArr.push(value)
                        }
                        break;
                    case 'completed':
                        if (value.status == "completed") {
                            statusArr.push(value)
                        }
                        if (value.status == "refunded" && order.selected_card) {
                            statusArr.push(value)
                        }
                        break;
                    case 'Processing':
                        if (value.status == "Processing") {
                            statusArr.push(value)
                        }
                        break;
                    case 'ready_for_collection':
                        if (value.status == "ready_for_collection") {
                            statusArr.push(value)
                        }
                        if (value.status == "completed") {
                            statusArr.push(value)
                        }
                        if (value.status == "refunded") {
                            statusArr.push(value)
                        }
                        break;
                    case 'ready_for_delivery':
                        if (value.status == "ready_for_delivery") {
                            statusArr.push(value)
                        }
                        if (value.status == "completed") {
                            statusArr.push(value)
                        }
                        if (value.status == "refunded") {
                            statusArr.push(value)
                        }
                        if (order.order_type == 'delivery' && value.status == "delivery_failed") {
                            statusArr.push(value)
                        }
                        break;
                    case 'out_for_delivery':
                        if (value.status == "out_for_delivery") {
                            statusArr.push(value)
                        }
                        if (value.status == "completed") {
                            statusArr.push(value)
                        }
                        if (value.status == "refunded") {
                            statusArr.push(value)
                        }
                        if (order.order_type == 'delivery' && value.status == "delivery_failed") {
                            statusArr.push(value)
                        }
                        break;
                    case 'delivery_failed':
                        if (value.status == "delivery_failed") {
                            statusArr.push(value)
                        }
                        if (value.status == "ready_for_delivery") {
                            statusArr.push(value)
                        }
                        if (value.status == "refunded") {
                            statusArr.push(value)
                        }
                        if (value.status == "rejected") {
                            statusArr.push(value)
                        }
                        break;
                    case 'partially_refunded':
                        if (value.status == "partially_refunded") {
                            statusArr.push(value)
                        }
                        break;
                    case 'refund_failed':
                        if (value.status == "refund_failed") {
                            statusArr.push(value)
                        }
                        break;
                    default:
                        break;
                }
            });
            return statusArr
        },

        orderStatusFilter(data, order_type) {
            let statusArr = []
            data.forEach((value, index) => {
                if (value.id == 1 || value.id == 2) {
                    statusArr.push(value)
                }
                if (order_type == 'pick_up') {
                    if (value.id == 3) {
                        statusArr.push(value)
                    }
                }
                if (order_type == 'delivery') {
                    if (value.id == 4 || value.id == 5) {
                        statusArr.push(value)
                    }
                }
            });
            return statusArr
        },

        orderTotal(orders, decimalLength, decimalSeparator) {
            var total = 0
            orders.data.forEach((value, index) => {
                total += value.total
            })
            return total.toFixed(decimalLength.value)
        },

        getMonth(month) {
            return moment(month, 'M').format('MMMM')
        },
        stripeAmount(amount) {
            return amount / 100
        },

        // get module url
        moduleURL(name) {
            var URL = "dashboard.panel"
            switch (name) {
                case 'retail':
                    URL = 'retail.dashboard.panel';
                    break;
                case 'automotive':
                    URL = 'automotive.dashboard.panel';
                    break;
                case 'boats':
                    URL = 'boats.dashboard.panel';
                    break;
                case 'news':
                    URL = 'news.dashboard.panel';
                    break;
                case 'blogs':
                    URL = 'blogs.dashboard.panel';
                    break;
                case 'posts':
                    URL = 'posts.dashboard.panel'
                    break;
                case 'obituaries':
                    URL = 'obituaries.dashboard.panel'
                    break;
                case 'recipes':
                    URL = 'recipes.dashboard.panel'
                    break;
                case 'classifieds':
                    URL = 'classifieds.dashboard.panel'
                    break;
                case 'taskers':
                    URL = 'taskers.dashboard.panel'
                    break;
                case 'employment':
                    URL = 'employment.dashboard.panel'
                    break;
                case 'government':
                    URL = 'government.dashboard.panel'
                    break;
                case 'notices':
                    URL = 'notices.dashboard.panel'
                    break;
                // case 'real-estate':
                //     URL = 'real-estate.dashboard.panel'
                //     break;
            }
            return URL;
        },

        // get selected track name
        getSelectedModuleName() {
            let module = localStorage.getItem("selectedModuleName");

            return module === 'marketplace' ? 'classifieds' : module;
        },

        getYear(dateString) {
            return moment(dateString).format("YYYY")
        },

        removeUnderscores(str) {
            return str?.replace(/_/g, ' ');
        },

        // truncate string
        limit(string, limit = 50, end = '...') {
            return (string?.length > limit
                ? string.slice(0, limit) + end
                : string)
        },

        removeDescriptionHtml(description) {
            return description.replace(/<[^>]+>/g, "");
        },

        makeTagSlug(tag) {
            return tag.toLowerCase().replace(/\s+/g, '-');
        }

    },
}
