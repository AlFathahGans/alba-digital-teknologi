<template>
    <section class="section section-shaped section-lg my-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <card type="secondary" shadow
                          header-classes="bg-white"
                          body-classes="px-lg-5 py-lg-4"
                          class="border-0">
                          <template>
                                <div class="text-muted text-center mb-3">
                                    <span>Sign up with credentials</span>
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
                            <form @submit.prevent="register" role="form">
                                <base-input alternative
                                            type="text"
                                            v-model="name"
                                            autocomplete="name"
                                            class="mb-3"
                                            placeholder="Name"
                                            addon-left-icon="ni ni-hat-3">
                                </base-input>
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
                                <base-input alternative
                                            type="password"
                                            v-model="password_confirmation"
                                            placeholder="Confirm Password"
                                            addon-left-icon="ni ni-lock-circle-open">
                                </base-input>
                                <base-checkbox>
                                    Remember me
                                </base-checkbox>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary my-4">Create Account</button>
                                </div>
                            </form>
                        </template>
                    </card>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import axios from 'axios';

export default {
    name: "AuthRegister",
    data() {
        return {
            name:'',
            email: '',
            password: '',
            password_confirmation:'',
            errorMessage: '',
            successMessage: '', // Tambahkan untuk pesan sukses
        };
    },
    methods: {
        async register() {
            try {
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                });
                console.log('Register successful', response.data);

                this.successMessage = 'Registration successful! Please verify your email before logging in.';
                // Reset form fields setelah registrasi berhasil
                this.name = '';
                this.email = '';
                this.password = '';
                this.password_confirmation = '';

                 // Tambahkan countdown 3 detik sebelum redirect
                let countdown = 3;
                const interval = setInterval(() => {
                    countdown--;
                    this.successMessage = `Registration successful! Redirecting in ${countdown} seconds...`;

                    if (countdown === 0) {
                        clearInterval(interval);  // Hentikan interval ketika countdown selesai
                        this.$router.push('/');   // Redirect ke halaman login setelah countdown selesai
                    }
                }, 1000);  // Setiap 1 detik
            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'Register failed, please try again.';
                console.error('Register failed', error);

                // Reset errorMessage setelah 5 detik
                setTimeout(() => {
                    this.errorMessage = '';
                    this.name = '';
                    this.email = '';
                    this.password = '';
                    this.password_confirmation = '';
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
