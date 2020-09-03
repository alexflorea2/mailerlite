<template>
  <div>
    <ui-box title="subscriber info" class="mb-3">
      <template v-slot:body>
        <form>

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

        </form>
      </template>
    </ui-box>

    <ui-box title="Fields">
      <template v-slot:body>

        <form ref="addForm">

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Field</label>
            <div class="col-sm-10">
              <v-select :getOptionLabel="getOptionLabel" v-model="field" :options="availableFields" @input="new_value=field.default_value">
                <template slot="no-options">
                  No available Fields. You can add them from Manage page.
                </template>
                <template slot="option" slot-scope="option">
                  <div class="d-center">
                    <span class="text-info">{{ option.type }}</span> {{ option.title }}
                    <div class="text-muted">{{ option.description }}</div>
                  </div>
                </template>
                <template slot="selected-option" slot-scope="option">
                  <div class="d-center">
                    <span class="text-info">{{ option.type }}</span> {{ option.title }}
                    <div class="text-muted">{{ option.description }}</div>
                  </div>
                </template>
              </v-select>
            </div>
          </div>

          <div class="form-group row" v-if="field.type">
            <label class="col-sm-2 col-form-label">Value</label>
            <div class="col-sm-10">
              <input v-if="field.type=='string'" required v-model="new_value" type="text" class="form-control"
                     placeholder="Enter default">
              <input v-if="field.type=='number'" required v-model="new_value" type="number" step="1"
                     class="form-control" placeholder="Enter default">
              <select v-if="field.type=='boolean'" required v-model="new_value" class="form-control">
                <option value="true">true</option>
                <option value="false">false</option>
              </select>
              <input v-if="field.type=='date'" required v-model="new_value" type="date" class="form-control"
                     placeholder="Enter default">
            </div>
          </div>

          <button :disabled="submitActive" :class="{disabled: submitActive}" type="submit" class="btn btn-primary" @click.prevent="submitForm()">Add field to subscriber</button>

        </form>

        <hr>

        <template v-if="fields.length === 0">
          <div class="alert alert-info">There are no Fields.</div>
        </template>
        <template v-else>
          <div class="row">
            <div class="col-md-4 mb-2" v-for="item in fields" :key="item.id">
              <ui-simple-box :title="item.field.title">
                <template v-slot:body>
                  <p>{{ item.value }}</p>
                  <hr>
                  <p>{{ item.field.description }}</p>
                  <span class="text-muted">Type</span> {{ item.field.type }} <br>
                  <span class="text-muted">Default</span>
                  <template v-if="item.field.default_value">{{ item.field.default_value }}</template>
                  <template v-else>N/A</template>
                </template>
              </ui-simple-box>
            </div>
          </div>
        </template>
      </template>
    </ui-box>


  </div>
</template>

<script>

import {getSubscriber, getSubscriberFields, getFields, addFieldToSubscriber} from "@/services/Api.js";
import Swal from "sweetalert2";
import vSelect from 'vue-select';
import {_} from "ag-grid-community";

export default {
  name: "editSubscriberPager",
  props: ['id'],
  data() {
    return {
      subscriber: {},
      fields: [],
      availableFields: [],
      field: {},
      new_value:null
    }
  },

  components: {
    vSelect
  },

  computed:{
    submitActive(){
      return  this.new_value == null;
    }
  },

  mounted() {
    this.getSubscriberProfile();
    this.getSubscriberFields();
    this.getAvailableFields();
  },

  methods: {

    getOptionLabel: (option,) => {
      return option.title;
    },

    onSearch(search, loading) {
      loading(true);
      this.search(loading, search, this);
    },

    async getAvailableFields(search = null) {
      let response = await getFields(escape(search));
      this.availableFields = response.data;
    },

    async getSubscriberProfile() {

      try {
        let response = await getSubscriber(this.id);
        this.subscriber = response.data;
      } catch (error) {
        let text = error.message;
        if (error.response && error.response.data && error.response.data.errors) {
          text = error.response.data.errors.join('<br>');
        }
        Swal.fire(
            'Error!',
            text,
            'error'
        )

        return;
      }

    },

    async getSubscriberFields() {

      try {
        let response = await getSubscriberFields(this.id);
        this.fields = response.data;
      } catch (error) {
        let text = error.message;
        if (error.response && error.response.data && error.response.data.errors) {
          text = error.response.data.errors.join('<br>');
        }
        Swal.fire(
            'Error!',
            text,
            'error'
        )

        return;
      }

    },

    async submitForm(){

      let isValid = this.$refs['addForm'].reportValidity();

      if( !isValid )
      {
        return;
      }

      try {
        let response = await addFieldToSubscriber({subscriberId:this.id, fieldId:this.field.id, value:this.new_value});
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
          'Field added.',
          'success'
      );

      await this.getSubscriberFields();
    }

  }
}
</script>

