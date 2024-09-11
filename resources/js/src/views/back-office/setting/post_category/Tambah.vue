<template>
    <div>
      <form id="proses-tambah-post-category" @submit.prevent="submitForm" method="POST" ref="modalTambahContainer">
        <div class="row">
          <!-- Category -->
          <div class="col-lg-12 col-sm-12 mb-2">
            <label for="category">Category</label>
            <a-input size="large" v-model="form.category" placeholder="Category" required></a-input>
          </div>
        </div>
      </form>
    </div>
</template>
<script>
import Tabs from "~/components-argon/Tabs/Tabs.vue";
import TabPane from "~/components-argon/Tabs/TabPane.vue";

import { Modal } from 'ant-design-vue';
import axios from "axios";

export default {
    name: "TambahPostCategory",
    components: {
        Tabs,
        TabPane,
        Modal
    },
    props: {
        post_category: Array, // Props untuk menerima data post category dari parent component
    },
    data() {
        return {
            form: {
                category: "",
            },
            container:null,
        };
    },
    methods: {
        showTambahConfirm() {
            return new Promise((resolve) => {
                Modal.confirm({
                title: 'Konfirmasi',
                content: 'Apakah Anda yakin ingin menyimpan data ini?',
                okText: 'Simpan',
                okType: 'primary',
                cancelText: 'Batal',
                getContainer: () => this.$refs.modalTambahContainer, // Tentukan container untuk modal konfirmasi
                onOk() {
                    resolve();
                },
                });
            });
        },

        async submitForm() {
            try {

                await this.showTambahConfirm();

                const formData = new FormData();
                formData.append("name", this.form.category);

                const response = await axios.post("/api/categories", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });

                this.$message.success('Data berhasil ditambahkan!');
                this.clear();
                this.$emit("data-added");
            } catch (error) {
                console.error("Gagal menambahkan data:", error);
                this.$message.success('Gagal menambahkan data.');
            }
        },

        clear() {
            this.form = {
                category: "",
            };
        },
    }
};
</script>