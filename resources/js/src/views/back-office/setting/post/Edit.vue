<template>
<div>
    <form id="proses-edit-posts" @submit.prevent="submitForm" method="PUT" ref="modalEditContainer">
    <div class="row">
        <!-- Title -->
        <div class="col-lg-12 col-sm-12 mb-2">
        <label for="title">Title</label>
        <a-input size="large" v-model="form.title" placeholder="Title" required></a-input>
        </div>

        <!-- Content -->
        <div class="col-lg-12 col-sm-12 mb-2">
        <label for="content">Content</label>
        <a-textarea size="large" v-model="form.content" class="form-control" placeholder="Content" rows="5" required></a-textarea>
        </div>

        <!-- Category -->
        <div class="col-lg-6 col-sm-12">
        <label for="category">Category</label>
        <a-select
            size="large"
            class="mt-2"
            show-search
            placeholder="[Pilih Category]"
            option-filter-prop="children"
            :filter-option="filterOption"
            @change="handleCategoryChange"
            v-model="form.post_category_id"
        >
            <a-select-option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
            </a-select-option>
        </a-select>
        </div>
        <div class="col-lg-6 col-sm-12">
        <label for="images">Images</label>
        <div>
            <a-upload
            :file-list="fileList"
            list-type="picture"
            :before-upload="handleBeforeUpload"
            :remove="handleRemove"
            >
            <base-button type="success" class="mt-2">
                <i class="fa fa-upload" size="lg"></i> <span>Click to Upload</span>
            </base-button>
            </a-upload>
        </div>
        </div>
    </div>
    </form>
</div>
</template>
<script>
import { Modal } from "ant-design-vue";
import axios from "axios";

export default {
    name: "EditPost",
    props: {
        dataUpdate: Object, // Props untuk menerima data post yang akan diedit
    },
    data() {
        return {
            form: {
                title: "",
                content: "",
                post_category_id: "",
                image: "",
            },
            fileList: [], // List file untuk gambar utama
            categories: [],
            container: null,
        };
    },
    methods: {
        async fetchCategories() {
            try {
                const response = await axios.get("/api/categories");
                this.categories = response.data;
            } catch (error) {
                console.error("Gagal mengambil kategori:", error);
            }
        },
        handleCategoryChange(value) {
            this.form.post_category_id = value;
        },
        handleBeforeUpload(file) {
            // Jika ada gambar yang sudah ada di form, hapus dari server
            if (this.form.image) {
                axios
                    .delete(`/api/posts/${this.dataUpdate.id}/image`)
                    .then(() => {
                        // Hapus gambar dari daftar file di frontend
                        this.fileList = [];
                        // Tambahkan gambar baru ke daftar file
                        this.fileList.push({
                            uid: file.uid,
                            name: file.name,
                            status: "done",
                            url: URL.createObjectURL(file),
                            originFileObj: file,
                        });
                        this.form.image = file;
                    })
                    .catch((error) => {
                        console.error(
                            "Gagal menghapus gambar dari server:",
                            error
                        );
                        // Jika gagal menghapus gambar, batalkan pengunggahan gambar baru
                        return false;
                    });
            } else {
                // Jika tidak ada gambar yang sudah ada, tambahkan gambar baru
                this.fileList.push({
                    uid: file.uid,
                    name: file.name,
                    status: "done",
                    url: URL.createObjectURL(file),
                    originFileObj: file,
                });
                this.form.image = file;
            }

            // Cegah unggahan otomatis karena kita mengelola file secara manual
            return false;
        },
        handleRemove(file) {
            // Hapus gambar dari server jika gambar yang dihapus adalah gambar yang saat ini ada di form
            if (this.form.image) {
                axios
                    .delete(`/api/posts/${this.dataUpdate.id}/image`)
                    .then(() => {
                        // Reset form.image setelah dihapus
                        this.form.image = "";
                        // Hapus file dari daftar file di frontend
                        const index = this.fileList.indexOf(file);
                        if (index > -1) {
                            this.fileList.splice(index, 1);
                        }
                    })
                    .catch((error) => {
                        console.error(
                            "Gagal menghapus gambar dari server:",
                            error
                        );
                    });
            } else {
                // Hapus file dari daftar file di frontend tanpa menghubungi server
                const index = this.fileList.indexOf(file);
                if (index > -1) {
                    this.fileList.splice(index, 1);
                }
            }
        },

        filterOption(input, option) {
            return (
                option.componentOptions.children[0].text
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
            );
        },
        async submitForm() {
            try {
                await this.showEditConfirm();

                const formData = new FormData();
                formData.append("title", this.form.title);
                formData.append("content", this.form.content);
                formData.append("post_category_id", this.form.post_category_id);
                // Tambahkan gambar
                this.fileList.forEach((file) => {
                    if (file.originFileObj) {
                        formData.append("image", file.originFileObj);
                    }
                });

                formData.append("_method", "PUT");

                const response = await axios.post(
                    `/api/posts/${this.dataUpdate.id}`,
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
                title: "",
                content: "",
                post_category_id: "",
                image: "",
            };
            this.fileList = [];
        },
    },
    watch: {
        dataUpdate: {
            immediate: true,
            handler(newData) {
                if (newData) {
                    this.form = {
                        title: newData.title || "",
                        content: newData.content || "",
                        post_category_id: newData.post_category_id || "",
                        image: newData.image_url || "",
                    };

                    // Set fileList jika ada gambar
                    if (newData.image_url) {
                        this.fileList = [
                            {
                                uid: newData.id,
                                name: newData.image_url.split("/").pop(), // Mengambil nama file dari URL
                                status: "done",
                                url: newData.image_url,
                            },
                        ];
                    }
                }
            },
        },
    },
    mounted() {
        this.fetchCategories(); // Panggil saat komponen di-mount
    }

};
</script>

<style>
/* Tambahkan style khusus jika diperlukan */
</style>
