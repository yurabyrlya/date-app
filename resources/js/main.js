import Vue from 'vue'
import VueRouter from "vue-router";

import Login from "./Component/Login";
import Profile from "./Component/Profile";
import Index from "./Component/App";

Vue.component('login', Login)
Vue.component('profile', Profile)
Vue.component('App', Index)

Vue.use(VueRouter);

const routes = [
    { path: '/', component: Index },
    { path: '/profile', component: Profile }
]

const router = new VueRouter({
    routes
})

const app = new Vue({
    mode: 'history',
    router
}).$mount('#app')
