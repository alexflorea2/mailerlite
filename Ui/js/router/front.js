import Vue from "vue";
import VueRouter from "vue-router";

import DashboardPage from "@/pages/dashboard.vue";
import SubscribersPage from "@/pages/subscribers.vue";
import FieldsPage from "@/pages/fields.vue";
import AddSubscriberPage from "@/pages/addSubscriber.vue";
import EditSubscriberPage from "@/pages/editSubscriber.vue";
import NotFound from "@/pages/404.vue";

const BASE_LAYOUT = "base";

Vue.use(VueRouter);

const routes = [
    {
        path:'/',
        redirect: '/subscribers'
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: DashboardPage,
    },
    {
        path: "/subscribers",
        name: "subscribers",
        component: SubscribersPage,
    },
    {
        path: "/subscribers/:id",
        name: "editSubscriber",
        component: EditSubscriberPage,
        props:true
    },
    {
        path: "/add-subscriber",
        name: "addSubscriber",
        component: AddSubscriberPage,
    },
    {
        path: "/fields",
        name: "fields",
        component: FieldsPage,
    },
    {
        path: '*',
        meta: {
            layout: BASE_LAYOUT
        },
        component: NotFound
    }
];

const router = new VueRouter({
    base: "/",
    mode: "history",
    routes
});

export default router;