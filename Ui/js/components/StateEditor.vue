<template>
  <div>
    <v-select :options="states" @input="stopEditing" v-model="state" :appendToBody="true" ref="dropdown"></v-select>
  </div>
</template>

<script>

import vSelect from 'vue-select';
import {updateSubscriberState} from "@/services/Api.js";
import Toast from "@/services/Toast.js";

const states = [
  {label: 'unconfirmed', code: 'default'},
  {label: 'active', code: 'success'},
  {label: 'unsubscribed', code: 'muted'},
  {label: 'junk', code: 'warning'}
];
export default {
  data() {
    return {
      state: null,
      originalValue: null,
      states: states
    }
  },
  components: {
    vSelect
  },
  mounted() {
    this.state = this.params.value;
    this.originalValue = this.params.value

    this.$refs["dropdown"].open = true;
  },
  methods: {
    isPopup() {
      return false;
    },
    stopEditing() {
      setTimeout(async _ => {
            if( this.state.label !== this.originalValue )
            {
              await updateSubscriberState(this.params.data.id, this.state.label);
              Toast.fire({
                icon: 'success',
                title: 'State was changed!'
              })
            }

            this.params.api.stopEditing();
          },
          100);
    },
    getValue() {
      if (typeof this.state.label == 'undefined' || this.state.label == null) return this.originalValue;
      return this.state.label;
    }
  }
};
</script>
