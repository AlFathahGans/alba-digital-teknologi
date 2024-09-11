<template>
    <section class="section section-lg my-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <card type="secondary" shadow
                          header-classes="bg-white"
                          body-classes="px-lg-5 py-lg-5"
                          class="border-0">
                          <template>
                                <div class="text-muted text-center mb-3">
                                    <span>Form Forgot Password</span>
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
                            <form @submit.prevent="forgotPassword" role="form">
                                <base-input alternative
                                            type="email"
                                            v-model="email"
                                            autocomplete="email"
                                            class="mb-3"
                                            placeholder="Email"
                                            addon-left-icon="ni ni-email-83">
                                </base-input>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary my-4">Send Password Reset Link</button>
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
    name: "ForgotPassword",
    data() {
        return {
            email: '',
            errorMessage: '',
            successMessage: '', // Tambahkan untuk pesan sukses
        };
    },
    methods: {
        async forgotPassword() {
            try {
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/forgot-password', {
                    email: this.email,
                });
                console.log('Forgot Password successful', response.data);
                this.successMessage = this.message = response.data.message;// Set pesan sukses

                // Tampilkan pesan sukses selama 3 detik sebelum navigasi
                setTimeout(() => {
                    this.successMessage = '';
                    this.$router.push('/');
                }, 1000);
            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'Error sending reset link.';

                // Reset errorMessage setelah 5 detik
                setTimeout(() => {
                    this.errorMessage = '';
                    this.email = '';
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
