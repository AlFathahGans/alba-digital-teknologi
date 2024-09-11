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
                                    <span>Form Reset Password</span>
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
                            <form @submit.prevent="resetPassword" role="form">
                                <input type="hidden" v-model="token" />
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
                                            placeholder="New Password"
                                            addon-left-icon="ni ni-lock-circle-open">
                                </base-input>
                                <base-input alternative
                                            type="password"
                                            v-model="password_confirmation"
                                            placeholder="Confirm New Password"
                                            addon-left-icon="ni ni-lock-circle-open">
                                </base-input>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary my-4">Reset Password</button>
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
    name: "ResetPassword",
    props: ['token'],
    data() {
        return {
            email: '',
            password: '',
            password_confirmation:'',
            errorMessage: '',
            successMessage: '', // Tambahkan untuk pesan sukses
        };
    },
    created() {
        // Ambil token dari query string jika belum ada di props
        if (!this.token) {
            this.token = this.$route.query.token;
        }
    },
    methods: {
        async resetPassword() {
            try {
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/reset-password', {
                    token: this.token,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                });
                console.log('Reset Password successful', response.data);
                this.successMessage = 'Reset Password successful!'; // Set pesan sukses

                // Tampilkan pesan sukses selama 3 detik sebelum navigasi
                setTimeout(() => {
                    this.successMessage = '';
                    this.$router.push('/');
                }, 1000);
            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'Reset Password failed, please try again.';
                console.error('Reset Password failed', error);

                // Reset errorMessage setelah 5 detik
                setTimeout(() => {
                    this.errorMessage = '';
                    this.email = '';
                    this.password = '';
                    this.password_confirmation = '';
                }, 2000);
            }
        },
    },
};
</script>
<style scoped>
section {
    background: linear-gradient(135deg, #6a1b9a, #ab47bc);  /* Contoh gradien hijau ke biru */
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    z-index: -1;
}
</style>
