<template>
    <div id="app">
        <router-view name="header"></router-view>
        <main>
            <fade-transition origin="center" mode="out-in" :duration="250">
                <router-view/>
            </fade-transition>
        </main>
        <router-view name="footer"></router-view>
    </div>
</template>
<script>
import { FadeTransition } from "vue2-transitions";

export default {
    name: 'Main',
    components: {
        FadeTransition
    }
};
</script>

<style>
.container-fluid {
  margin: 0 auto; /* Mengatur margin otomatis pada sumbu horizontal */
  padding: 0 15px; /* Padding horizontal untuk konten */
  max-width: 1180px; /* Menentukan lebar maksimum */
}

/* Jika Anda menggunakan media query untuk responsivitas */
@media (max-width: 992px) {
  .container-fluid {
    max-width: 960px;
  }
}

@media (max-width: 768px) {
  .container-fluid {
    max-width: 720px;
  }
}

@media (max-width: 576px) {
  .container-fluid {
    max-width: 540px;
  }
}
</style>
