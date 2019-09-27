
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// bind Vue
window.Vue = require('vue');

// import babel polyfill for IE support
import 'babel-polyfill'

// add Vuex
import store from './store'

// import the vue-router
import VueRouter from 'vue-router'
Vue.use(VueRouter)

// import vuetify
import Vuetify from 'vuetify'
Vue.use(Vuetify);

// add the components for the router
import App from './components/App'
import Login from './components/pages/auth/Login.vue'
import Subscribers from './components/pages/Subscribers.vue'
import AddSubscriber from './components/pages/AddSubscriber.vue'
import EditSubscriber from './components/pages/EditSubscriber.vue'
import Fields from './components/pages/Fields.vue'

const router = new VueRouter({
    mode: 'hash',
    routes: [
        {
            path: '/subscribers',
            name: 'subscribers',
            component: Subscribers,
            props: {  },
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/addsubscriber',
            name: 'addsubscriber',
            component: AddSubscriber,
            props: {  },
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/subscribers/:id',
            name: 'editsubscriber',
            component: EditSubscriber,
            props: true,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/fields',
            name: 'fields',
            component: Fields,
            props: {  },
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
            props: {  },
            meta: {
                requiresVisitor: true
            }
        },
        { path: '*', redirect: '/subscribers' }
    ],
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!store.state.access_token) {
            next({
                path: '/login'
            })
        } else {
            next()
        }
    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        // this route does not require auth, check if logged in
        // if they are, redirect to subscribers.
        if (store.state.access_token) {
            next({
                path: '/subscribers',
            })
        } else {
            next()
        }
    } else {
      next()
    }
})

const app = new Vue({
    el: '#app',
    components: { App },
    router,
    store
});
