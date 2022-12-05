<template>
  <Modal id="childModal">
    <div slot="body" class="body">
      <p v-for="(msg, field) in error()" :key="'error-' + field">
        {{ msg }}
      </p>
    </div>
    <div slot="buttons">
      <button class="btn btn-primary" v-on:click="close">OK</button>
    </div>
  </Modal>
</template>
<script>
import validation from "../../mixins/validation";
import { Modal } from "../Modals";

export default {
  components: { Modal },
  mixins: [validation],

  props: {},
  data() {
    return {
      errors: this.initialErrors,
    };
  },
  methods: {
    close() {
      document.getElementById("childModal").style.display = "none";
    },
  },
};
</script>
<style>
#childModal .body p {
  font-size: 24px;
  font-weight: bold;
}
</style>
