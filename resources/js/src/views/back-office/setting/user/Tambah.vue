<template>
    <div>
      <form id="proses-tambah-user" @submit.prevent="submitForm" method="POST" ref="modalTambahContainer">
        <div class="row">
          <!-- Name -->
          <div class="col-lg-12 col-sm-12 mb-2">
            <label for="name">Name</label>
            <a-input size="large" v-model="form.name" placeholder="Name" required></a-input>
          </div>
          <!-- Email -->
          <div class="col-lg-12 col-sm-12 mb-2">
            <label for="email">Email</label>
            <a-input type="email" size="large" v-model="form.email" placeholder="email" required></a-input>
          </div>
          <!-- Password -->
          <div class="col-lg-12 col-sm-12 mb-2">
            <label for="password">Password</label>
            <a-input type="password" size="large" v-model="form.password" placeholder="Password" required></a-input>
          </div>
          <!-- Password Confirmation -->
          <div class="col-lg-12 col-sm-12 mb-2">
            <label for="password_confirmation">Password Confirmation</label>
            <a-input type="password" size="large" v-model="form.password_confirmation" placeholder="Password Confirmation" required></a-input>
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
    name: "TambahUser",
    components: {
        Tabs,
        TabPane,
        Modal
    },
    props: {
        users: Array, // Props untuk menerima data user dari parent component
    },
    data() {
        return {
            form: {
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            container:null,
        };
    },
    methods: {
        async submitForm() {
            try {

                 // Validasi password
                 if (this.form.password !== this.form.password_confirmation) {
                    this.$message.error('Password dan konfirmasi password tidak cocok!');
                    return;
                }

                await this.showTambahConfirm();

                const formData = new FormData();
                
                formData.append("name", this.form.name);
                formData.append("email", this.form.email);
                formData.append("password", this.form.password);
                
                const response = await axios.post("/api/users", formData, {
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

        clear() {
            this.form = {
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
            };
        },
    }
};
</script>