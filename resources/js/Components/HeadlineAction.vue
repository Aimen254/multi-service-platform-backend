<template>
    <div>
        <div @click="removeTooltip()" class="btn  btn-active-color-primary btn-sm ms-1"
            :class="{ 'btn-icon btn-bg-light': !text }" data-bs-toggle="tooltip" data-bs-placement="bottom"
            :data-bs-original-title="`Make this ${getModule} headline`">
            <span class="svg-icon svg-icon-3">
                <i class="fas fa-newspaper" style="color: #757a95;"></i><span v-if="text">{{ text }}</span>
            </span>
        </div>
    </div>
</template>

<script>
import Helpers from '@/Mixins/Helpers'

export default {
    props: ['text'],
    computed: {
        getModule() {
            let module = '';
            switch (this.getSelectedModuleName()) {
                case 'news':
                    module = 'news';
                    break;
                case 'blogs':
                    module = 'blog';
                    break;
                case 'recipes':
                    module = 'recipe';
                    break;
            }
            return module;
        }
    },
    methods: {
        removeTooltip() {
            this.hideTooltip()
        }
    },
    mounted() {
        this.showTooltip()
    },
    mixins: [Helpers]
}
</script>
