<template>
    <div>
        <base-nav type="primary" effect="dark" expand>
        <router-link slot="brand" class="navbar-brand mr-lg-0" to="/">
                <img src="/img/logo/logo.png" alt="logo">
            </router-link>

            <div class="row" slot="content-header" slot-scope="{closeMenu}">
                <div class="col-6 collapse-brand">
                    <a href="#">
                        <img src="/img/logo/nsi-text.png">
                    </a>
                </div>
                <div class="col-6 collapse-close">
                    <close-button @click="closeMenu"></close-button>
                </div>
            </div>

            <ul class="navbar-nav navbar-nav-hover align-items-lg-center">
                <router-link to="/post" slot="title" class="nav-link" :class="{ 'navbar-link-active': isActive('/post') }">
                    <i class="ni ni-ui-04 d-lg-none"></i>
                    <span class="nav-link-inner--text"><i class="ni ni-ui-04"></i> Post</span>
                </router-link>
                <base-dropdown tag="li" class="nav-item">
                    <a slot="title" href="#" class="nav-link" data-toggle="dropdown" role="button">
                        <i class="ni ni-collection d-lg-none"></i>
                        <span class="nav-link-inner--text"><i class="ni ni-bullet-list-67"></i> Referensi</span>
                    </a>
                    <router-link to="/user" class="dropdown-item" :class="{ 'dropdown-item-active': isActive('/user') }">User</router-link>
                    <router-link to="/post-category" class="dropdown-item" :class="{ 'dropdown-item-active': isActive('/post-category') }">Post Category</router-link>
                    <router-link to="/bookmark" class="dropdown-item" :class="{ 'dropdown-item-active': isActive('/bookmark') }">Bookmark</router-link>
                </base-dropdown>
            </ul>
            <ul class="navbar-nav align-items-lg-center ml-lg-auto">
                <li class="nav-item">
                    <a class="nav-link nav-link-icon" href="https://www.facebook.com/creativetim" target="_blank" rel="noopener"
                       data-toggle="tooltip" title="Like us on Facebook">
                        <i class="fa fa-facebook-square"></i>
                        <span class="nav-link-inner--text d-lg-none">Facebook</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-icon" href="https://www.instagram.com"
                       target="_blank" rel="noopener" data-toggle="tooltip" title="Follow us on Instagram">
                        <i class="fa fa-instagram"></i>
                        <span class="nav-link-inner--text d-lg-none">Instagram</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-icon" href="https://twitter.com" target="_blank" rel="noopener"
                       data-toggle="tooltip" title="Follow us on Twitter">
                        <i class="fa fa-twitter-square"></i>
                        <span class="nav-link-inner--text d-lg-none">Twitter</span>
                    </a>
                </li>
                <li class="nav-item d-none d-lg-block ml-lg-4">
                    <base-button type="warning" @click.prevent="logout" rel="noopener"
                       class="btn btn-warning btn-icon">
                        <span class="btn-inner--icon">
                        <i class="ni ni-button-power mr-2"></i>
                        </span>
                        <span>Logout</span>
                    </base-button>
                </li>
            </ul>

            <!-- Notifikasi -->
            <div class="notifikasi">
            <base-alert type="success" v-if="successMessage">
                <strong>Success!</strong>  {{ successMessage }}
            </base-alert>
           </div>

        </base-nav>
    </div>
</template>
<script>
import BaseNav from "~/components-argon/BaseNav.vue";
import BaseDropdown from "~/components-argon/BaseDropdown.vue";
import CloseButton from "~/components-argon/CloseButton.vue";

import axios from 'axios';

export default {
    components: {
        BaseNav,
        CloseButton,
        BaseDropdown
    },
    data() {
    return {
      successMessage: '',
    };
  },
    methods: {
        isActive(path) {
            return this.$route.path === path;
        },
        async logout() {
            try {
            // Mengambil CSRF token
            await axios.get('/sanctum/csrf-cookie');

            // Melakukan permintaan logout
            await axios.post('/api/logout');

            // Hapus token dari localStorage
            localStorage.removeItem('auth_token');

            // Hapus header default Axios
            delete axios.defaults.headers.common['Authorization'];

            // Tampilkan notifikasi sukses
            this.successMessage = 'Logout successful!';

            // Tampilkan pesan sukses selama 1 detik sebelum navigasi
            setTimeout(() => {
                this.successMessage = '';
                this.$router.push('/');
            }, 1000);

            console.log('Logout successful');
            } catch (error) {
            console.error('Logout failed', error);
            }
        },
    }

};
</script>
<style>
.navbar-brand img {
    height: 55px;
}

.notifikasi {
    position: fixed;
    top: 15%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999; /* Pastikan notifikasi berada di atas elemen lainnya */
    width: 80%; /* Sesuaikan lebar sesuai kebutuhan */
    max-width: 500px; /* Sesuaikan lebar maksimum sesuai kebutuhan */
    text-align: center;
}
</style>
