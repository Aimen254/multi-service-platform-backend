<template>
  <Head title="Create Driver" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
        <Breadcrumbs :title="`Create News`" :path="`News - News`" />
    </template>
    <div>
        <news-form :newsmediaSize="mediaSizes" :newsCategories="newsCategories"></news-form>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import NewsForm from './Form.vue'

export default {
  props: ['mediaSizes', 'newsCategories'],
  components: {
    Head,
    AuthenticatedLayout,
    Breadcrumbs,
    NewsForm
  },
};
</script>