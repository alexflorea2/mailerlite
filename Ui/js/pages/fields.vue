<template>
  <div>

    <div class="row mb-3">
      <div class="col">
        <ui-box title="Add Field">
          <template v-slot:body>
            <form ref="addForm">

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Title</label>
                <div class="col-sm-10">
                  <input v-model="field.title" type="text" required class="form-control" placeholder="Enter title">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Short description</label>
                <div class="col-sm-10">
                  <input v-model="field.description" type="text" required class="form-control"
                         placeholder="Enter help information">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Type</label>
                <div class="col-sm-10">
                  <select v-model="field.type" required class="form-control">
                    <option v-for="option in types" :key="option">{{ option }}</option>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Default value</label>
                <div class="col-sm-10">
                  <input v-if="field.type=='string'" v-model="field.default_value" type="text" class="form-control"
                         placeholder="Enter default">
                  <input v-if="field.type=='number'" v-model="field.default_value" type="number" step="1"
                         class="form-control" placeholder="Enter default">
                  <select v-if="field.type=='boolean'" v-model="field.default_value" class="form-control">
                    <option value="true">true</option>
                    <option value="false">false</option>
                  </select>
                  <input v-if="field.type=='date'" v-model="field.default_value" type="date" class="form-control"
                         placeholder="Enter default">
                </div>
              </div>

              <button :disabled="submitActive" :class="{disabled: submitActive}" type="submit" class="btn btn-primary"
                      @click.prevent="submitForm()">Add field
              </button>
            </form>
          </template>
        </ui-box>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <ui-box title="Existing Fields">
          <template v-slot:body>
            <div class="row">
              <div class="col">
                <template v-if="fields.length === 0">
                  <div class="alert alert-info">There are no Fields.</div>
                </template>
                <template v-else>
                  <div class="row">
                    <div class="col-md-4 mb-2" v-for="field in fields" :key="field.id">
                      <ui-simple-box :title="field.title">
                        <template v-slot:body>
                          <p>{{ field.description }}</p>
                          <span class="text-muted">Type</span> {{ field.type }} <br>
                          <span class="text-muted">Default</span>
                          <template v-if="field.default_value">{{ field.default_value }}</template>
                          <template v-else>N/A</template>
                        </template>
                      </ui-simple-box>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </template>
        </ui-box>
      </div>
    </div>

  </div>
</template>

<script>
import {getFields, addField} from "@/services/Api.js";
import SimpleBox from "../components/core/SimpleBox";
import StandardBox from "../components/core/StandardBox";
import Swal from "sweetalert2";

const TYPES = [
  'date', 'number', 'string', 'boolean'
];

const FIELD = {
  title: null,
  type: "string",
  description: null,
  default_value: null
}

export default {
  name: "fieldsPage",
  components: {StandardBox, SimpleBox},
  data() {
    return {
      fields: [],
      field: JSON.parse(JSON.stringify(FIELD)),
      types: TYPES
    }
  },
  mounted() {
    this.getData();
  },
  computed: {
    submitActive() {
      return this.field.title == null;
    }
  },
  methods: {
    async getData() {
      let response = await getFields();
      this.fields = response.data;
    },
    async submitForm() {

      let isValid = this.$refs['addForm'].reportValidity();

      if (!isValid) {
        return;
      }

      try {
        let response = await addField(this.field);
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

      Swal.fire(
          'Success!',
          'Field has been added.',
          'success'
      );

      this.field = JSON.parse(JSON.stringify(FIELD));
      this.getData();
    }
  }
}
</script>
