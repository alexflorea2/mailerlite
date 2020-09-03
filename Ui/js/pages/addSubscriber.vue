<template>
  <div>
    <ui-box title="Add subscriber">
      <template v-slot:body>
        <form ref="addForm">

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Email address</label>
            <div class="col-sm-10">
              <input v-model="subscriber.email" type="email" required class="form-control" placeholder="Enter email">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Name</label>
            <div class="col-sm-10">
              <input v-model="subscriber.name" type="text" required class="form-control" placeholder="Enter name">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">State</label>
            <div class="col-sm-10">
              <select v-model="subscriber.state" required class="form-control">
                <option v-for="option in states" :key="option" >{{ option }}</option>
              </select>
            </div>
          </div>

          <button :disabled="submitActive" :class="{disabled: submitActive}" type="submit" class="btn btn-primary" @click.prevent="submitForm()">Add subscriber</button>
        </form>
      </template>
    </ui-box>

  </div>
</template>

<script>

import {addSubscriber} from "@/services/Api.js";
import Swal from "sweetalert2";
import StandardBox from "../components/core/StandardBox";

const STATES = [
    'active',
    'unconfirmed'
];

const SUBSCRIBER = {
  email:null,
  name:null,
  state: 'active'
};

export default {
  name: "addSubscriberPager",
  components: {StandardBox},
  data(){
    return {
      states: STATES,
      subscriber: JSON.parse(JSON.stringify(SUBSCRIBER))
    }
  },
  computed:{
    submitActive(){
      return  this.subscriber.email == null || this.subscriber.name == null;
    }
  },
  methods:{
    async submitForm(){

      let isValid = this.$refs['addForm'].reportValidity();

      if( !isValid )
      {
        return;
      }

      try {
        let response = await addSubscriber(this.subscriber);
      }catch(error)
      {
        let text = error.message;
        if( error.response && error.response.data && error.response.data.errors )
        {
          text = error.response.data.errors.join('<br>');
        }
        Swal.fire(
            'Error!',
            text,
            'error'
        )

        return;
      }

      Swal.fire(
          'Success!',
          'Subscriber has been added.',
          'success'
      );

      this.subscriber = JSON.parse(JSON.stringify(SUBSCRIBER));
    }
  }
}
</script>
