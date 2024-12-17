<template>
    <Head title="Level 2 tags" />
    <AuthenticatedLayout>
      <template #breadcrumbs>
        <Breadcrumbs :title="`Level ${level} tags`" :path="`Tags Hierarchy`" />
      </template>
  
      <!--begin::Tables Widget 11-->
      <div class="widgetClasses card">
        <!-- begin::Header-->
  
        <form
          class="form d-flex flex-column flex-lg-row"
          v-if="form"
          @submit.prevent="submit"
          enctype="multipart/form-data"
        >
          <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
            <div class="d-flex flex-column gap-7 gap-lg-10">
              <div class="card card-flush py-4">
                <div class="card-body py-3">
                  <div class="d-flex gap-5">
                    <div v-if="level > 2">
                      <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">
                          <select2
                            :class="{
                              'is-invalid border border-danger': form.errors.L2,
                            }"
                            class="business"
                            :placeholder="`Select level 2 tag`"
                            v-model="levelTwoTag"
                            :options="levelTwoTags"
                            @select="getTags($event, 'levelTwo')"
                          />
                        </span>
                      </h3>
                      <error :message="form.errors.L2"></error>
                    </div>
                    <div v-if="level > 3" style="width:220px">
                      <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">
                          <select
                            class="select"
                            v-model="levelThreeTag"
                          ></select>
                        </span>
                      </h3>
                      <error :message="form.errors.L3"></error>
                    </div>
                  </div>
                  <div class="col-lg-12 fv-row fv-plugins-icon-container">
                    <Label for="name" value="Tags" />
                    <div class="fv-row mb-4">
                      <VueTagsInput
                        style="height: "
                        class="tags-input form-control form-control-md form-control-solid w-full bg-transparent"
                        v-model="tag"
                        :placeholder="`Level ${level} tags`"
                        :tags="form['L' + level]"
                        :autocomplete-items="all_tags"
                        :add-only-from-autocomplete="true"
                        @before-deleting-tag="deleteTag"
                        @tags-changed="updateTags"
                        @keyup="getAutoComplete($event)"
                      />
                      <error :message="form.errors['L' + level]"></error>
                      <div class="mt-4" v-if="meta && meta.data.length > 0">
                          <pagination @pageNo="pagination" :meta="meta" :section="'hierarchy'" :keyword="searchedKeyword" :selectedFilters="filterForm" :callType="type" />
                      </div>
                    </div>
                  </div>
  
                  <div class="text-end">
                    <Button
                      type="submit"
                      :class="{ 'opacity-25': form.processing }"
                      :disabled="form.processing"
                    >
                      <span class="indicator-label" v-if="!form.processing">
                        {{ form.id ? "Update" : "Save" }}</span
                      >
                      <span class="indicator-progress" v-if="form.processing">
                        <span
                          class="spinner-border spinner-border-sm align-middle"
                        ></span>
                      </span>
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script>
  import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
  import { Head, Link } from "@inertiajs/inertia-vue3";
  import Header from "@/Components/Header.vue";
  import Pagination from "@/Components/Pagination.vue";
  import Helpers from "@/Mixins/Helpers";
  import SearchInput from "@/Components/SearchInput.vue";
  import InlineSvg from "vue-inline-svg";
  import Breadcrumbs from "@/Components/Breadcrumbs.vue";
  import { useForm } from "@inertiajs/inertia-vue3";
  import VueTagsInput from "@sipec/vue3-tags-input";
  import Select2 from "vue3-select2-component";
  import Label from "@/Components/Label.vue";
  import Button from "@/Components/Button.vue";
  import Error from "@/Components/InputError.vue";
  
  export default {
    props: ["assignedTags", "level", "levelTwoTags"],
    data() {
      return {
        tag: "",
        form: null,
        levelTwoTag:
          this.levelTwoTags && this.levelTwoTags.length > 0
            ? this.levelTwoTags[0].id
            : null,
        levelThreeTag: null,
        levelThreeTags: [],
        tags: this.assignedTags,
        selected: 0,
        nodes: null,
        item: null,
        allStandardTags: [],
        meta: null
      };
    },
    components: {
      AuthenticatedLayout,
      Head,
      Link,
      Header,
      Pagination,
      SearchInput,
      InlineSvg,
      Breadcrumbs,
      VueTagsInput,
      Select2,
      Label,
      Button,
      Error,
      Pagination
    },
    watch: {
      tag(val) {
        this.searchTags();
      },
    },
    methods: {
      submit() {
        this.form.post(
          route("employment.dashboard.tag-hierarchies.store", [
            this.getSelectedModuleValue(),
            this.level,
          ]),
          {
            errorBag: "groups",
            preserveScroll: true,
          }
        );
      },
      updateTags(event) {
        this.form["L" + this.level] = event;
      },
      async getTags(e, dropdown, page = null) {
        this.tag = "";
        this.form["L" + this.level] = [];
        if(dropdown == 'levelTwo' ) {
          this.levelThreeTag = null
          this.levelThreeDropdown()
        }
        let parameters = [this.getSelectedModuleValue(), this.levelTwoTag];
        this.form.removeTags = []
        if ((this.level == 3 && dropdown == "levelTwo") || (this.level == 4 && dropdown == "levelThree")) {
          if (this.level == 4) {
            parameters.push(this.levelThreeTag);
          }
          axios
            .get(route("employment.dashboard.tag-hierarchies.getTagWithLevel", parameters), {
               params: {
                 page: page ? page : 1 
                },
            })
            .then((response) => {
              this.form.L2 = this.level == 2 ? response.data : this.levelTwoTag;
              this.form.L3 = this.level == 3 ?  response.data.data : this.levelThreeTag;
              this.form.L4 = this.level == 4 ? response.data.data : [];
              this.meta = this.level > 2 ? response.data : []
            });
        }
      },
      deleteTag(e) {
        axios({
          method: "get",
          url: route("employment.dashboard.tag-hierarchies.show", [
            this.getSelectedModuleValue(),
            this.level,
            e.tag.id,
          ]),
        })
          .then((response) => {
            this.level > 2 ? this.form.removeTags.push(e.tag) : []
            e.deleteTag();
          })
          .catch((error) => {
            this.$notify(
              {
                group: "toast",
                type: "error",
                text: error.response.data.message,
              },
              3000
            ); // 3s
          });
      },
      getAutoComplete(e) {
        this.item = document.querySelector(".ti-autocomplete ul");
        this.nodes = document.querySelectorAll(".ti-autocomplete li");
        if (e.key == "ArrowUp") {
          // up
          this.selectItem(this.nodes[this.selected - 1], e);
        }
        if (e.key == "ArrowDown") {
          // down
          this.selectItem(this.nodes[this.selected + 1], e);
        }
      },
  
      selectItem(el, e) {
        var s = [].indexOf.call(this.nodes, el);
        if (s === -1) return;
        this.selected = s;
        var elHeight = $(el).height();
        var scrollTop = $(this.item).scrollTop();
        var viewport = scrollTop + $(this.item).height();
        var elOffset = elHeight * this.selected;
        if (e.key == "ArrowUp") {
          // up
          this.item.scrollBy(0, -30);
        }
        if (e.key == "ArrowDown") {
          // up
          this.item.scrollBy(0, 30);
        }
      },
  
      searchTags() {
        this.allStandardTags = [];
        if (this.tag.length < 1) return;
        clearTimeout(this.debounce);
        this.debounce = setTimeout(() => {
          window.axios
            .get(
              route(
                "employment.dashboard.search.standardTags",
                this.getSelectedModuleValue()
              ),
              {
                params: {
                  tag: this.tag,
                },
              }
            )
            .then((response) => {
              this.allStandardTags = response.data.tags;
              if (this.allStandardTags.length == 0) {
                this.$notify(
                  {
                    group: "toast",
                    type: "error",
                    text: "Not Found",
                  },
                  3000
                ); // 3s
              }
            })
            .catch(
              () =>
                this.$notify(
                  {
                    group: "toast",
                    type: "error",
                    text: "Not Found",
                  },
                  3000
                ) // 3s
            );
        }, 300);
      },
  
      pagination(page) {
        this.getTags(this.levelTwoTag, this.level == 3 ? 'levelTwo' : 'levelThree', page)
      },
  
       levelThreeDropdown() {
        this.meta = null
        let parameters = [this.getSelectedModuleValue(), this.levelTwoTag];
        var vm = this;
        setTimeout(() => {
          $(".select").select2({
            placeholder: "Select Level 3",
            // ajax request
            ajax: {
              url: route("employment.dashboard.tag-hierarchies.getTagWithLevel", parameters),
              dataType: "json",
              delay: 250,
              data: function (params) {
                return {
                  keyword: params.term, // search query
                  page: params.page,
                };
              },
              processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                  results: data.data.map(function (item) {
                    return item
                  }),
                  pagination: {
                    more: params.page * 50 < data.total,
                  },
                };
              },
              cache: true,
            },
            minimumInputLength: 0, // minimum characters required to trigger search
          });
  
          //   get selected value
          $(".select").on("select2:select", function (e) {
            vm.levelThreeTag = e.params.data.id
            vm.getTags(vm.levelThreeTag, 'levelThree')
          });
        }, 0);
      },
    },
    beforeMount() {
      this.form = useForm({
        L2: this.level == 2 ? this.assignedTags : [],
        L3: [],
        L4: [],
        removeTags: []
      });
    },
    async mounted() {
      // triggering on change as first item selected
      if (this.levelTwoTag) {
        await this.getTags(this.levelTwoTag, "levelTwo");
        if (this.level == 4) {
          await this.levelThreeDropdown()
        }
      }
    },
    computed: {
      all_tags() {
        return this.allStandardTags.filter((i) => {
          if (i.id == this.levelTwoTag || i.id == this.levelThreeTag) {
            return false;
          }
          return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
        });
      },
    },
    mixins: [Helpers],
  };
</script>
  <style>
  .fv-plugins-icon-container .fv-row .vue-tags-input {
    min-height: 270px;
    border: 1px solid #d8d8d8 !important;
    max-height: 100%;
  }
  .fv-plugins-icon-container .fv-row .vue-tags-input .ti-input {
    border: none !important;
  }
  .tags-input {
    max-width: 100% !important;
    background: none !important;
  }
  .dropdown.show > .form-control.form-control-solid,
  .form-control.form-control-solid:active,
  .form-control.form-control-solid.active,
  .form-control.form-control-solid:focus,
  .form-control.form-control-solid.focus {
    background: none;
  }
  .ti-tags .ti-tag {
    background-color: #009ef7;
    padding: 10px;
    border-radius: 8px;
  }
  .ti-selected-item {
    background-color: #009ef7 !important;
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
  </style>
  