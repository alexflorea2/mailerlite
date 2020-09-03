<template>

  <!-- Sidebar -->
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" :class="{toggled : !fullSidebar}" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
      <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-laugh-wink"></i>
      </div>
      <div class="sidebar-brand-text mx-3">Admin <sup>demo</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
      <router-link :to="{ name: 'dashboard'}" class="nav-item" tag="li" active-class="active">
        <a class="nav-link">
          <font-awesome-icon icon="file-contract" />
          <span>Dashboard</span>
        </a>
      </router-link>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
      Subscribers
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
      <router-link :to="{ name: 'subscribers'}" class="nav-item" tag="li" active-class="active">
        <a class="nav-link">
          <font-awesome-icon icon="users" />
          <span>List</span>
        </a>
      </router-link>

    <router-link :to="{ name: 'addSubscriber'}" class="nav-item" tag="li" active-class="active">
      <a class="nav-link">
        <font-awesome-icon icon="user-plus" />
        <span>Add</span>
      </a>
    </router-link>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
      Fields
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <router-link :to="{ name: 'fields'}" class="nav-item" tag="li" active-class="active">
      <a class="nav-link">
        <font-awesome-icon icon="stream" />
        <span>Manage</span>
      </a>
    </router-link>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle" v-on:click="fullSidebar=!fullSidebar">
        <font-awesome-icon icon="angle-left" style="color: white" v-if="fullSidebar"/>
        <font-awesome-icon icon="angle-right" style="color: white" v-else/>
      </button>
    </div>

  </ul>
  <!-- End of Sidebar -->
</template>

<script>
export default {
  name: "sidebar",
  data() {
    return {
      fullSidebar: true
    }
  }
}
</script>

<style>
.sidebar #sidebarToggle::after, .sidebar.toggled #sidebarToggle::after{
  content: initial;
}
</style>