<template>
  <div>
    <button class="btn btn-sm btn-info" @click="$router.push({ name: 'editSubscriber', params: { id: params.value } })">Edit</button>
    <button class="btn btn-sm btn-danger" @click="promptDelete()">Delete</button>
  </div>
</template>

<script>
import Swal from 'sweetalert2'
import {deleteSubscriber} from "@/services/Api.js";
import EventBus from "@/services/EventBus.js";

export default {
  methods:{
    promptDelete(){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then(async (result) => {
        if (result.value) {

          let $response = await deleteSubscriber(this.params.value);
          EventBus.$emit('subscriberRowAction:deleted', this.params.node )

          Swal.fire(
              'Deleted!',
              'Subscriber has been deleted.',
              'success'
          )
        }
      })
    }
  }
};
</script>