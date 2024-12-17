<template>
    <Disclosure>
        <div class="relative flex items-center justify-between">
            <div class="flex items-center">
                <!-- Business Settings dropdown -->
                <Menu as="div" class="ml-3 relative">
                    <div>
                        <MenuButton
                            class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="pt-1">{{ title }}</span>
                            <ChevronDownIcon class="-mr-1 ml-2 pt-2 h-6 w-6" aria-hidden="true" />
                        </MenuButton>
                    </div>
                    <transition enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95">
                        <MenuItems
                            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                            <MenuItem v-for="(list, index) in lists" :key="index">
                                <Link :href="list.path" :class="[list.current ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">
                                    {{ list.name }}
                                </Link>
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </div>
    </Disclosure>
</template>

<script>
import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems
} from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/solid'
import { Link } from '@inertiajs/inertia-vue3'
    export default {
        components: {
            Menu,
            MenuButton,
            MenuItem,
            MenuItems,
            ChevronDownIcon,
            Link,
            Disclosure,
            DisclosureButton,
            DisclosurePanel,
        },

        props: ['business', 'title', 'categories'],

        data () {
            return {
                lists: [
                    {
                        name: 'Business Info',
                        path: route('dashboard.businesses.edit', this.business.id),
                        current: route().current('dashboard.businesses.edit', this.business.id)
                    },
                    {
                        name: 'Business Schedule',
                        path: route('dashboard.business.businessschedules.index', this.business.id),
                        current: route()
                            .current('dashboard.business.businessschedules.index', this.business.id)
                    },
                    {
                        name: 'Delivery Settings',
                        path: route('dashboard.business.deliveryzones.index', this.business.id),
                        current: route()
                            .current('dashboard.business.deliveryzones.index', this.business.id)
                    },
                    {
                        name: 'Settings',
                        path: route('dashboard.business.settings.index', this.business.id),
                        current: route()
                            .current('dashboard.business.settings.index', this.business.id)
                    },
                    {
                        name: 'Coupons',
                        path: route('dashboard.business.coupons.index', this.business.id),
                        current: route()
                            .current('dashboard.business.coupons.index', this.business.id)
                    }
                ]
            }
        },

        mounted () {
            let categoriesArray = [];
            this.categories.forEach(category => {
                categoriesArray.push({
                    'name': category.name,
                    'path': route('dashboard.business.categories.index', [this.business.id, category.id]),
                    current: route().current('dashboard.business.categories.index', [this.business.id, category.id])
                });
            });
            this.lists = this.lists.concat(categoriesArray);
        }
    }
</script>

