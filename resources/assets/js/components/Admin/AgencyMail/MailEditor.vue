<template>
  <div>
  <input type="hidden" name="message" :value="content" required>
    <vue-editor v-model="content" :editorOptions="editorOptions"></vue-editor>
  </div>
</template>

<script>
import { VueEditor } from 'vue2-editor'; // Import VueEditor from 'vue2-editor'

export default {
  components: {
    VueEditor
  },
  data() {
    return {
      content: '', // Data binding for the textarea content
      editorOptions: {
        // Options for the editor (optional)
        placeholder: 'Enter text here...',
        name: 'message',
        // You can add more options as per your requirement
      }
    };
  }
};
</script>