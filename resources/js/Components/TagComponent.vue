<template lang="">
    <vue-tags-input
        class="tags-input form-control form-control-md form-control-solid w-full bg-transparent pt-0"
        ref="tagsInput"
        data-bs-toggle="tooltip" data-bs-trigger="hover"
        data-bs-dismiss="click" data-bs-placement="bottom"
        :data-bs-original-title="tooltipContent"
        v-model="tags"
        :placeholder="placeholder"
        :tags="assigned"
        :autocomplete-items="filteredItems"
        :add-only-from-autocomplete="onlyFromAutoComplete"
        :autocomplete-max-length="10"
        :avoid-adding-duplicates="false"
        :autocomplete-always-open="showDropDown"
        @tags-changed="update"
        @before-adding-tag="beforeAdd"
        @before-deleting-tag = "deleteTag"
        @focus="()=>{focus(); fetchsuggetions()}"
        @blur="focus"
    >
    <template #tag-center="props" >
       <div
            slot="tag-center"
            slot-scope="props"
        >
            <span
                v-if="!props.edit"
                @click="props.performOpenEdit(props.index)"
                data-bs-toggle="tooltip" data-bs-placement="bottom"
                :data-bs-original-title="props.tag.tags_ ? props.tag.tags_.length > 0 ? 'Linked to: '+[...new Set(props.tag.tags_.map(obj => obj.name))] : null : null"
                :data-id="props.tag.id"
                class="tooltip-content"
            >
            {{ props.tag.text }}
            </span>
        </div>
    </template>
</vue-tags-input>
<TagModal v-if="childId" :id="childId" :uuid="uuid" />
</template>

<script>
import VueTagsInput from '@sipec/vue3-tags-input';
import Helpers from '@/Mixins/Helpers';
import TagModal from '@/Pages/Products/Settings/TagModal.vue';
import Swal from 'sweetalert2';
export default {
    components: {
        VueTagsInput,
        TagModal
    },
    props: ['placeholder', 'assignedTags', 'autocomplete', 'parent_id', 'tooltipContent', 'uuid', 'onlyFromAutoComplete', 'type', 'attribute_id', 'openOnClick', 'tagType', 'productUuid', 'level','businessTag'],
    data() {
        return {
            suggestion: this.autocomplete ? this.autocomplete : [],
            assigned: this.assignedTags ? this.assignedTags : [],
            tags: '',
            showDropDown: false,
            childId: null
        }
    },
    methods: {
        update(event) {
            this.$refs.tagsInput.focus(); //set focus
        },
        focus(e) {
            if (this.openOnClick) {
                setTimeout(() => {
                    this.showDropDown = this.showDropDown ? false : true
                }, 250)
            }
        },
        beforeAdd(event) {
            this.emitter.emit("assigned-tags", { tags: this.assignedTags });
            if (this.assignedTags) {
                if ((this.onlyFromAutoComplete && this.autocomplete.some(tag => tag.text === event.tag.text))) {
                    this.getSelectedModuleName() === 'real-estate' && this.type === 'attribute' && this.assigned.length === 1 ? this.$notify(
                        {
                            group: "toast",
                            type: "error",
                            text: "You cannot select more than one tag",
                        },
                        3000
                    ) : this.assignedTags.push(event.tag)
                } else if (!this.onlyFromAutoComplete) {
                    event.tag.type = this.type
                    this.assignedTags.push(event.tag)
                }
            }
            if (this.type == 'attribute') {
                this.showTooltip();
            }
            this.tags = '';
            this.$refs.tagsInput.focus(); //set focus
        },
        processTagRemoval(e) {
            this.emitter.emit("assigned-tags", { tags: this.assignedTags });
            this.emitter.emit('removedTag', { tag: e.tag });
            if (this.tagType == 'industry_tags' ||this.tagType == 'product_tags' ) {
                this.emitter.emit('remove_product_tags', {
                     tagType: this.tagType,
                    'industry_tags': e
                })
            }
            if (this.type != 'attribute') {
                this.assignedTags.splice(this.assignedTags.findIndex(obj => obj.id ? obj.id === e.tag.id : obj.text === e.tag.text), 1)
                this.assigned = this.assignedTags
            }

            else {
                if (e.tag?.pivot) {
                    this.assignedTags.splice(this.assignedTags.findIndex(obj => obj.id === e.tag.id && obj?.pivot?.attribute_id === e.tag.pivot?.attribute_id), 1)
                } else {
                    this.assignedTags.splice(this.assignedTags.findIndex(obj => obj.id === e.tag.id && obj.attribute[0]?.id === e.tag?.attribute[0]?.id), 1)
                }
                this.assigned = this.assignedTags
            }
            e.deleteTag();
        },
        deleteTag(e) {
            if(this.businessTag == true){
                window.axios.post(route('automotive.dashboard.dealership.delete-tags', [
                    this.getSelectedModuleValue(),
                    this.uuid,
                    e.tag.id,
                    ]))
                    .then((response) => {
                        this.processTagRemoval(e);
                    })
                    .catch(error => {
                        this.$notify(
                    {
                    group: "toast",
                    type: "error",
                    text: error.response.data.message,
                    },
                    3000
                    );
                });
            }else{
                this.processTagRemoval(e);
            }

        },
        format(type) {
            let arr = [];
            if (this.assigned && this.assigned.length > 0) {
                this.assigned.map((obj) => {
                    if (this.attribute_id) {
                        if (obj.pivot) {
                            if (obj.pivot.attribute_id) {
                                if (obj.pivot.attribute_id == this.attribute_id) {
                                    arr.push(obj)
                                }
                            } else {
                                if (obj.attribute.find(obj => obj.id === this.attribute_id)) {
                                    arr.push(obj)
                                }
                            }
                        } else {
                            if (obj.attribute.find(obj => obj.id === this.attribute_id)) {
                                arr.push(obj)
                            }
                        }
                    }
                    else if (obj[type] == this[type]) {
                        arr.push(obj)
                    }
                });
            }
            this.assigned = arr
        },

        fetchsuggetions() {
            if (this.attribute_id) {
                this.suggestion = []
                this.$emit('fetchSuggestions', this.attribute_id)
            } else if (this.level) {
                this.$emit('fetchSuggestions')
            }
            this.$refs.tagsInput.focus(); //set focus
        }
    },
    mounted() {
        if (this.parent_id) {
            this.format('parent_id');
        } else if (this.type) {
            this.format('type');
        }

        this.showTooltip();
    },
    computed: {
        filteredItems() {
            return this.suggestion.filter(i => {
                if (this.attribute_id) {
                    return i.text.toLowerCase().indexOf(this.tags.toLowerCase()) !== -1;
                } else if (this.attribute && i.attribute.length == 0) {
                    return false
                } else {
                    return i.text.toLowerCase().indexOf(this.tags.toLowerCase()) !== -1;
                }
            }).sort((a, b) => (a.text > b.text ? 1 : -1));
        }
    },
    watch: {
        autocomplete: {
            handler(tags) {
                this.suggestion = tags;
                this.$refs.tagsInput.focus(); //set focus
            },
            deep: true
        },
        assignedTags: {
            handler(tags) {
                this.assigned = tags
                if (this.parent_id) {
                    this.format('parent_id');
                } else if (this.type) {
                    this.format('type');
                }
                this.$refs.tagsInput.focus(); //set focus
            },
            deep: true
        },
    },
    mixins: [Helpers]
}
</script>

<style>
.tags-input {
    max-width: 100% !important;
    background: none !important;
}

.dropdown.show>.form-control.form-control-solid,
.form-control.form-control-solid:active,
.form-control.form-control-solid.active,
.form-control.form-control-solid:focus,
.form-control.form-control-solid.focus {
    background: none;
}

.ti-tags .ti-tag {
    background-color: #009EF7;
    padding: 10px;
    border-radius: 8px;
}

.ti-selected-item {
    background-color: #009EF7 !important;
}

.ti-item {
    background-color: white;
    padding: 3px;
}

.ti-item {
    color: black;
}

.ti-icon-close {
    color: rgb(255, 255, 255);
}

.ti-autocomplete {
    background: white;
    width: 96% !important;
}

.ti-autocomplete ul {
    max-height: 40vh !important;
    overflow: scroll;
}
.tooltip-inner {
    white-space: nowrap; /* Prevent line breaks */
}
</style>
