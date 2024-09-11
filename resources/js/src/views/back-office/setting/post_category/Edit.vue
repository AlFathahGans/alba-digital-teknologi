<template>
<div>
    <form id="proses-edit-post-category" @submit.prevent="submitForm" method="PUT" ref="modalEditContainer">
    <div class="row">
        <!-- Category -->
        <div class="col-lg-12 col-sm-12 mb-2">
            <label for="category">Category</label>
            <a-input size="large" v-model="form.category" placeholder="category" required></a-input>
        </div>
    </div>
    </form>
</div>
</template>
<script>
import { Modal } from "ant-design-vue";
import axios from "axios";

export default {
    name: "EditPostCategory",
    props: {
        dataUpdate: Object, // Props untuk menerima data post category yang akan diedit
    },
    data() {
        return {
            form: {
                category: "",
            },
            container: null,
        };
    },
    methods: {
        async submitForm() {
            try {
                await this.showEditConfirm();

                const formData = new FormData();
                formData.append("name", this.form.category);
              
                formData.append("_method", "PUT");

                const response = await axios.post(
                    `/api/categories/${this.dataUpdate.id}`,
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
                category: "",
            };
        },
    },
    watch: {
        dataUpdate: {
            immediate: true,
            handler(newData) {
                if (newData) {
                    this.form = {
                        category: newData.name || "",
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
