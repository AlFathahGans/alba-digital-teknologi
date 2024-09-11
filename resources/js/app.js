import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';
import Argon from "@/plugins/argon-kit";
import './registerServiceWorker'

import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css'; // Import CSS

// Import komponen

import Main from '@/Main.vue';
import Login from '@/views/auth/Login.vue';
import Register from '@/views/auth/Register.vue';
import ForgotPassword from '@/views/auth/ForgotPassword.vue';
import ResetPassword from '@/views/auth/ResetPassword.vue';

import EmailVerified from '@/views/auth/EmailVerified.vue';
import EmailVerificationPending from '@/views/auth/EmailVerificationPending.vue';
import EmailVerificationError from '@/views/auth/EmailVerificationError.vue';
import EmailAlreadyVerified from '@/views/auth/EmailAlreadyVerified.vue';

// BACK OFFICE
import AppHeaderAdmin from "@/layouts/back-office/AppHeader.vue";
import AppFooterAdmin from "@/layouts/back-office/AppFooter.vue";
import Post from '@/views/back-office/setting/post/Index.vue';
import PostCategory from '@/views/back-office/setting/post_category/Index.vue';
import User from '@/views/back-office/setting/user/Index.vue';
import Bookmark from '@/views/back-office/setting/bookmark/Index.vue';


// Gunakan VueRouter
Vue.use(VueRouter);

// Gunakan plugin Argon
Vue.use(Argon);

Vue.use(Antd);

// Definisikan rute
const routes = [
    {
        path: '/',
        name: 'Login',
        components: {
            default: Login,
        }
    },

    {
        path: '/register',
        name: 'Register',
        components: {
            default: Register,
        }
    },

    {
        path: '/forgot-password',
        name: 'ForgotPassword',
        components: {
            default: ForgotPassword,
        }
    },

    {
        path: '/reset-password',
        name: 'ResetPassword',
        component: ResetPassword,
        props: route => ({ token: route.query.token })
    },

    {
        path: '/email-verified',
        name: 'EmailVerified',
        component: EmailVerified
    },
    {
        path: '/email-verification-pending',
        name: 'EmailVerificationPending',
        component: EmailVerificationPending
    },
    {
        path: '/email-verification-error',
        name: 'EmailVerificationError',
        component: EmailVerificationError
    },
    {
        path: '/email-already-verified',
        name: 'EmailAlreadyVerified',
        component: EmailAlreadyVerified
    },


    // BACK OFFICE
    {
        path: '/post',
        name: 'Post',
        components: {
            header: AppHeaderAdmin,
            default: Post,
            footer: AppFooterAdmin
        },
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/post-category',
        name: 'Post Category',
        components: {
            header: AppHeaderAdmin,
            default: PostCategory,
            footer: AppFooterAdmin
        },
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/user',
        name: 'User',
        components: {
            header: AppHeaderAdmin,
            default: User,
            footer: AppFooterAdmin
        },
        meta: {
            requiresAuth: true
        }
    },

    {
        path: '/bookmark',
        name: 'Bookmark',
        components: {
            header: AppHeaderAdmin,
            default: Bookmark,
            footer: AppFooterAdmin
        },
        meta: {
            requiresAuth: true
        }
    },

];

// Buat router
const router = new VueRouter({
    mode: 'history',
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        console.log('Checking authentication for route:', to.path);
        axios.get('/api/user')
            .then(response => {
                if (response.data) {
                    console.log('User is authenticated:', response.data);
                    next();
                } else {
                    console.log('Not authenticated, redirecting to login...');
                    next('/');
                }
            })
            .catch(error => {
                console.log('Error fetching user:', error);
                next('/');
            });
    } else {
        next();
    }
});

Vue.config.productionTip = false;

// Buat aplikasi Vue
new Vue({
    router,
    render: h => h(Main),
  }).$mount('#app');
