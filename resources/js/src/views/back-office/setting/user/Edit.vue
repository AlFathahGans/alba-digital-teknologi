<template>
<div>
    <form id="proses-edit-user" @submit.prevent="submitForm" method="PUT" ref="modalEditContainer">
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
import { Modal } from "ant-design-vue";
import axios from "axios";

export default {
    name: "EditUser",
    props: {
        dataUpdate: Object, // Props untuk menerima data user yang akan diedit
    },
    data() {
        return {
            form: {
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            container: null,
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

                await this.showEditConfirm();

                const formData = new FormData();

                formData.append("name", this.form.name);
                formData.append("email", this.form.email);
                formData.append("password", this.form.password);

                formData.append("_method", "PUT");

                const response = await axios.post(
                    `/api/users/${this.dataUpdate.id}`,
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );

                this.$message.success("Data berhasil diupdate!");
                this.clear();
                this.$emit("data-updated");
            } catch (error) {
                console.error("Gagal mengupdate data:", error);
                this.$message.error("Gagal mengupdate data.");
            }
        },
        showEditConfirm() {
            return new Promise((resolve) => {
                Modal.confirm({
                    title: "Konfirmasi",
                    content: "Apakah Anda yakin ingin menyimpan perubahan ini?",
                    okText: "Simpan",
                    okType: "primary",
                    cancelText: "Batal",
                    getContainer: () => this.$refs.modalEditContainer, // Tentukan container untuk modal konfirmasi
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
    },
    watch: {
        dataUpdate: {
            immediate: true,
            handler(newData) {
                if (newData) {
                    this.form = {
                        name: newData.name || "",
                        email: newData.email || "",
                        password: '',
                        password_confirmation: '',
                    };
                }
            },
        },
    }

};
</script>

<style>
/* Tambahkan style khusus jika diperlukan */
</style>
