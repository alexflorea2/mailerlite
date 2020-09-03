<template>
  <div id="VueApp">
    <component :is="layout">
      <router-view :key="$route.path"></router-view>
    </component>
  </div>
</template>


<script>
    const DEFAULT_LAYOUT = "default";

    export default {
      computed:{
        layout(){
          return `${(this.$route.meta.layout || DEFAULT_LAYOUT )}-layout`;
        }
      }
    }
</script>