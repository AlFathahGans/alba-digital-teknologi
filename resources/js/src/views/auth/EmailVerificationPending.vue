<template>
    <section class="section section-shaped section-lg my-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <template>
                        <div class="btn-wrapper text-center">
                            <base-alert type="danger">
                                <strong>Pending!</strong> Maaf, untuk saat ini proses terjeda.
                                <p>
                                    Anda akan dialihkan ke halaman utama dalam {{ countdown }} detik...
                                </p>
                            </base-alert>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "EmailVerificationPending",
    data() {
        return {
            countdown: 5, // Mulai dari 5 detik
        };
    },
    mounted() {
      // Setiap detik, kurangi countdown
      const interval = setInterval(() => {
        this.countdown--;
        if (this.countdown <= 0) {
          clearInterval(interval);  // Hentikan interval jika countdown mencapai 0
          this.$router.push('/');  // Arahkan pengguna ke halaman /post
        }
      }, 1000);  // Setiap 1 detik
    }
};
</script>

<style>
.section {
    min-height: 100vh;
    background: linear-gradient(135deg, #6a1b9a, #ab47bc);
}
</style>
