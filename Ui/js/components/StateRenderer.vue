<template>
  <div>
       <span class="badge " :class="stateClass">{{params.value}}</span>
  </div>
</template>

<script>
const states = {
  'unconfirmed' : 'default',
  'active':'success',
  'unsubscribed':'muted',
  'junk':'warning'
};
export default {
  computed:{
    stateClass(){
       return `badge-${states[this.params.value]}`;
    }
  },
};
</script>