<template>
  <Head title="Tags Mapper" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
      <Breadcrumbs :title="`Tags Mapper`" :path="`Tags`" />
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
                  <div>
                    <h3 class="card-title align-items-start flex-column">
                      <span class="card-label fw-bolder fs-3 mb-1">
                        <select
                          placeholder="Select type"
                          @change="
                            reInitialize('type', null);
                            changeType();
                          "
                          v-model="form.type"
                          class="form-select text-capitalize form-select-lg form-select-solid"
                          style="height: 88%"
                        >
                          <option class="text-capitalize" value="tag">
                            Tags
                          </option>
                          <option class="text-capitalize" value="attribute">
                            Attributes
                          </option>
                          <option class="text-capitalize" value="brand">
                            Brands
                          </option>
                        </select>
                        <error :message="form.errors.type"></error>
                      </span>
                    </h3>
                  </div>
                  <div v-if="form.type == 'tag'">
                    <h3 class="card-title align-items-start flex-column">
                      <span class="card-label fw-bolder fs-3 mb-1">
                        <select
                          class="select"
                          v-model="form.standardTag"
                        ></select>
                      </span>
                    </h3>
                    <error :message="form.errors.standardTag"></error>
                  </div>
                  <div v-if="form.type == 'attribute'">
                    <h3 class="card-title align-items-start flex-column">
                      <span class="card-label fw-bolder fs-3 mb-1">
                        <select
                          class="select"
                          v-model="form.attribute"
                        ></select>
                      </span>
                    </h3>
                    <error :message="form.errors.attribute"></error>
                  </div>
                  <div v-if="form.type == 'brand'">
                    <h3 class="card-title align-items-start flex-column">
                      <span class="card-label fw-bolder fs-3 mb-1">
                        <select class="select" v-model="form.brand"></select>
                        <!-- <select2
                          @select="reInitialize('brand', $event)"
                          :class="{
                            'is-invalid border border-danger':
                              form.errors.brand,
                          }"
                          class="business"
                          :placeholder="`Select brand`"
                          v-model="form.brand"
                          :options="brands"
                        /> -->
                      </span>
                    </h3>
                    <error :message="form.errors.brand"></error>
                  </div>
                </div>
                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                  <Label for="name" value="Tags" />
                  <div class="fv-row mb-4" @click="focusTag()">
                    <TagsInput
                      id="tagsInput"
                      class="tags-input form-control form-control-md form-control-solid w-full bg-transparent"
                      v-model="tag"
                      :placeholder="` Search Tags`"
                      :tags="form.tags"
                      :autocomplete-items="autocompleteItems"
                      :add-only-from-autocomplete="true"
                      @before-deleting-tag="deleteTag"
                      @tags-changed="updateTags"
                      @keyup="getAutoComplete($event)"
                    />
                    <error :message="form.errors.tags"></error>
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
import TagsInput from "@sipec/vue3-tags-input";
import Select2 from "vue3-select2-component";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Error from "@/Components/InputError.vue";
import "select2";
export default {
  props: [
    "attributes",
    "brands",
    "orphanTags",
    "assignedTags",
    "level",
    "levelTwoTags",
    "attibuteTags",
  ],
  data() {
    return {
      tag: "",
      form: null,
      selected: 0,
      nodes: null,
      item: null,
      autocompleteItems: [],
      debounce: null,
      selectedId: null,
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
    TagsInput,
    Select2,
    Label,
    Button,
    Error,
  },
  computed: {
    orphan_tags() {
      return this.orphanTags.filter((i) => {
        return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
      });
    },

    standard_tags() {
      return this.attibuteTags.filter((i) => {
        return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
      });
    },
  },
  watch: {
    tag(val) {
      this.inintItems();
    },
  },
  methods: {
    submit() {
      this.form.post(route("dashboard.standard-tag-mapper.store"), {
        errorBag: "groups",
        preserveScroll: true,
      });
    },

    updateTags(event) {
      this.form.tags = event;
    },

    reInitialize(type, data, tag_type = null) {
      this.form.standardTag = type == "type" ? null : this.form.standardTag;
      this.form.tags =
        this.form.type == "attribute"
          ? data && data.standard_tags.length > 0
            ? data.standard_tags
            : []
          : data && data.tags_.length > 0
          ? data.tags_
          : [];
      this.form.attribute = type == "type" ? null : this.form.attribute;
      this.form.brand = type == "type" ? null : this.form.brand;
      this.tag = "";
    },

    focusTag() {
      document.getElementById("tagsInput").focus();
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
    inintItems() {
      this.autocompleteItems = [];
      if (this.tag.length < 1) return;
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        window.axios
          .get(route("dashboard.get-tags"), {
            params: {
              type: this.form.type,
              tag: this.tag,
            },
          })
          .then((response) => {
            this.autocompleteItems = response.data.tags;
            if (this.autocompleteItems.length == 0) {
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

    handleSelectFocus() {
      const input = document.querySelector(".select2-search__field");
      var tags = [];
      if (input) {
        input.addEventListener("input", (event) => {
          if (event.target.value < 1) return;
          clearTimeout(this.debounce);
          this.debounce = setTimeout(() => {
            window.axios
              .get(route("dashboard.search.standardTags"), {
                params: {
                  type: this.form.type,
                  keyword: event.target.value,
                },
              })
              .then((response) => {
                tags = response.data.tags.data;
                this.standardTags = tags;
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
        });
      }
    },

    // when change type reinitialize select 2
    changeType() {
      var vm = this;
      setTimeout(() => {
        $(".select").select2({
          placeholder:
            vm.form.type == "attribute"
              ? "Select attribute"
              : vm.form.type == "tag"
              ? "Select a tag"
              : "Select brand tag",

          // ajax request
          ajax: {
            url: route("dashboard.search.standardTags"),
            dataType: "json",
            delay: 250,
            data: function (params) {
              return {
                keyword: params.term, // search query
                page: params.page,
                type: vm.form.type,
                tag_type: vm.form.tag_type
              };
            },
            processResults: function (data, params) {
              params.page = params.page || 1;
              let response =
                vm.form.type == "attribute"
                  ? data.attributes
                  : vm.form.type == "tag"
                  ? data.tags
                  : data.brands;
              return {
                results: response.data.map(function (item) {
                  if (vm.form.type == "attribute") {
                    return {
                      id: item.id,
                      text: item.text,
                      standard_tags: item.standard_tags,
                    };
                  } else if (vm.form.type == "tag") {
                    return {
                      id: item.id,
                      text: item.text,
                      tags_: item.tags_,
                    };
                  } else {
                    return {
                      id: item.id,
                      text: item.text,
                      tags_: item.tags_,
                    };
                  }
                }),
                pagination: {
                  more: params.page * 10 < response.total,
                },
              };
            },
            cache: true,
          },
          minimumInputLength: 0, // minimum characters required to trigger search
        });

        //   get selected value
        $(".select").on("select2:select", function (e) {
          vm.form.type == "attribute"
            ? (vm.form.attribute = e.params.data.id)
            : vm.form.type == "tag"
            ? (vm.form.standardTag = e.params.data.id)
            : (vm.form.brand = e.params.data.id);
          vm.reInitialize(vm.form.type, e.params.data);
        });
      }, 0);
    },
  },
  mounted() {
    this.form = useForm({
      type: "tag",
      standardTag: null,
      attribute: null,
      brand: null,
      tags: [],
    });
  },
  created() {
    var vm = this;
    setTimeout(() => {
      $(".select").select2({
        placeholder: "Select a tag",

        // ajax request
        ajax: {
          url: route("dashboard.search.standardTags"),
          dataType: "json",
          delay: 250,
          data: function (params) {
            return {
              keyword: params.term, // search query
              page: params.page,
              type: vm.form.type,
              tag_type: vm.form.tag_type
            };
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.tags.data.map(function (item) {
                return {
                  id: item.id,
                  text: item.text,
                  tags_: item.tags_,
                };
              }),
              pagination: {
                more: params.page * 10 < data.tags.total,
              },
            };
          },
          cache: true,
        },
        minimumInputLength: 0, // minimum characters required to trigger search
      });

      //   get selected value
      $(".select").on("select2:select", function (e) {
        vm.form.standardTag = e.params.data.id;
        vm.reInitialize(vm.form.type, e.params.data, vm.form.tag_type);
      });
    }, 100);
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