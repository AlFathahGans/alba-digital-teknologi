<template>
    <section class="section section-lg my-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <card type="secondary" shadow
                          header-classes="bg-white"
                          body-classes="px-lg-5 py-lg-4"
                          class="border-0">
                          <template>
                                <div class="text-muted text-center mb-3">
                                    <span>Sign in with credentials</span>
                                </div>
                                <div class="btn-wrapper text-center">
                                    <base-alert type="danger" v-if="errorMessage">
                                        <strong>Sorry!</strong>  {{ errorMessage }}
                                    </base-alert>
                                    <base-alert type="success" v-if="successMessage">
                                        <strong>Success!</strong>  {{ successMessage }}
                                    </base-alert>
                                </div>
                            </template>
                        <template>
                            <form @submit.prevent="login" role="form">
                                <base-input alternative
                                            type="email"
                                            v-model="email"
                                            autocomplete="email"
                                            class="mb-3"
                                            placeholder="Email"
                                            addon-left-icon="ni ni-email-83">
                                </base-input>
                                <base-input alternative
                                            type="password"
                                            v-model="password"
                                            placeholder="Password"
                                            addon-left-icon="ni ni-lock-circle-open">
                                </base-input>
                                <base-checkbox>
                                    Remember me
                                </base-checkbox>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary my-4">Sign In</button>
                                </div>
                            </form>
                        </template>
                    </card>
                    <div class="row mt-3">
                        <div class="col-6">
                            <a href="/forgot-password" class="text-white">
                                <small>Forgot password?</small>
                            </a>
                        </div>
                        <div class="col-6 text-right">
                            <a href="/register" class="text-white">
                                <small>Create new account</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import axios from 'axios';

export default {
    name: "AuthLogin",
    data() {
        return {
            email: '',
            password: '',
            errorMessage: '',
            successMessage: '', // Tambahkan untuk pesan sukses
        };
    },
    methods: {
        async login() {
            try {
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                });

                const token = response.data.access_token;
                localStorage.setItem('auth_token', token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

                console.log('Login successful', response.data);
                this.successMessage = 'Login successful!'; // Set pesan sukses

                // Tampilkan pesan sukses selama 3 detik sebelum navigasi
                setTimeout(() => {
                    this.successMessage = '';
                    this.$router.push('/post');
                }, 1000);
            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'Login failed, please try again.';
                console.error('Login failed', error);

                // Reset errorMessage setelah 5 detik
                setTimeout(() => {
                    this.errorMessage = '';
                    this.email = '';
                    this.password = '';
                }, 2000);
            }
        },
    },
};
</script>
<style>
.section {
    min-height: 100vh;
    background: linear-gradient(135deg, #6a1b9a, #ab47bc);
}
</style>
