<template>
  <form v-if="form" @submit.prevent="submit">
    <h3 class="mb-5">Personal Information</h3>

    <div class="row mb-4">
      <div class="col-md-6">
        <Label for="first_name" value="First Name" />
        <Input
          id="first_name"
          type="text"
          v-model="form.first_name"
          autofocus
          autocomplete="first_name"
          placeholder="Enter first name"
        />
        <error :message="form.errors.first_name"></error>
      </div>
      <div class="col-md-6">
        <Label for="last_name" value="Last Name" />
        <Input
          id="last_name"
          type="text"
          v-model="form.last_name"
          autofocus
          autocomplete="last_name"
          placeholder="Enter last name"
        />
        <error :message="form.errors.last_name"></error>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <Label for="email" value="Email" />
        <Input
          id="email"
          type="email"
          v-model="form.email"
          autofocus
          autocomplete="email"
          placeholder="Enter email"
        />
        <error :message="form.errors.email"></error>
      </div>
      <div class="col-md-6">
        <Label for="phone" value="Phone" />
        <Input
          id="phone"
          type="text"
          v-model="form.phone"
          autofocus
          autocomplete="phone"
          placeholder="Enter phone number"
        />
        <error :message="form.errors.phone"></error>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <Label for="neighborhood_name" value="Neighborhood Name" />
        <Input
          id="neighborhood_name"
          type="text"
          v-model="form.neighborhood_name"
          autofocus
          autocomplete="neighborhood_name"
          placeholder="Enter neighborhood name"
        />
        <error :message="form.errors.neighborhood_name"></error>
      </div>
    </div>

    <div class="row">
      <div class="row">
        <div class="col-md-12 mt-5 d-flex justify-content-end">
          <Button
            type="submit"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            ref="submitButton"
          >
            <span class="indicator-label" v-if="!form.processing">Save</span>
            <span class="indicator-progress" v-if="form.processing">
              <span class="spinner-border spinner-border-sm align-middle"></span>
            </span>
          </Button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Error from "@/Components/InputError.vue";
import Helpers from "@/Mixins/Helpers";
import { useForm } from "@inertiajs/inertia-vue3";
import InlineSvg from "vue-inline-svg";

export default {
  props: ["user", "type"],
  components: {
    Input,
    Label,
    Button,
    Error,
    InlineSvg,
  },

  mounted() {
    this.form = useForm({
      id: this.user ? this.user.id : "",
      first_name: this.user ? this.user.first_name : "",
      last_name: this.user ? this.user.last_name : "",
      email: this.user ? this.user.email : "",
      phone: this.user ? this.user.phone : "",
      neighborhood_name: this.user ? this.user.neighborhood_name : "",
      zipcode:
        this.user && this.user.latest_address
          ? this.user.latest_address.zipcode
          : "",
    });
  },

  data() {
    return {
      form: null,
      isOpened: false,
      isSaved: false,
    };
  },

  methods: {
    submit() {
      if (this.user && this.user.id !== "") {
        this.form.put(route("dashboard.profile.update", this.user.id), {
          errorBag: "admin",
          preserveScroll: true,
        });
      }
    },
  },

  mixins: [Helpers],
};
</script>
