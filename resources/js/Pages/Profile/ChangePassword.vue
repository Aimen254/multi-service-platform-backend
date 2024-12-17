<template>
  <form class="" @submit.prevent="submit">
    <h3 class="mb-5">Password Information</h3>
    <div class="row mb-4">
      <div class="col-md-6">
        <Label for="current_password" value="Current Password" />
        <Input
          id="current_password"
          type="password"
          autofocus
          v-model="form.current_password"
          placeholder="Current password"
        />
        <error :message="form.errors.current_password"></error>
      </div>
      <div class="col-md-6">
        <Label for="password" value="New Password" />
        <Input
          id="password"
          type="password"
          autofocus
          v-model="form.password"
          placeholder="Enter new password"
        />
        <error :message="form.errors.password"></error>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-md-6">
        <Label for="confirm_password" value="Confirm Password" />
        <Input
          id="confirm_password"
          type="password"
          autofocus
          v-model="form.confirm_password"
          placeholder="Confirm password"
        />
        <error
          :message="
            form.errors.confirm_password ? form.errors.confirm_password : ''
          "
        ></error>
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

  data() {
    return {
      form: useForm({
        current_password: "",
        password: "",
        confirm_password: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(route("dashboard.profile.change.password"), {
        errorBag: "admin",
        preserveScroll: true,
      });
    },
  },

  mixins: [Helpers],
};
</script>