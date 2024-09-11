<template>
    <div>
      <form id="proses-tambah-posts" @submit.prevent="submitForm" method="POST" ref="modalTambahContainer">
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
import Tabs from "~/components-argon/Tabs/Tabs.vue";
import TabPane from "~/components-argon/Tabs/TabPane.vue";

import { Modal } from 'ant-design-vue';
import axios from "axios";

export default {
    name: "TambahPost",
    components: {
        Tabs,
        TabPane,
        Modal
    },
    props: {
        posts: Array, // Props untuk menerima data posts dari parent component
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
            container:null,
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
            this.fileList.push({
                uid: file.uid,
                name: file.name,
                status: "done",
                url: URL.createObjectURL(file),
            });
            this.form.image = file;
            return false; // Prevent automatic upload
        },

        handleRemove(file) {
            const index = this.fileList.indexOf(file);
            const newFileList = this.fileList.slice();
            newFileList.splice(index, 1);
            this.fileList = newFileList;
        },
        filterOption(input, option) {
            return (
                option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
            );
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

        async submitForm() {
            try {

                await this.showTambahConfirm();

                const formData = new FormData();
                formData.append("title", this.form.title);
                formData.append("content", this.form.content);
                formData.append("post_category_id", this.form.post_category_id);
                formData.append("image", this.form.image);

                const response = await axios.post("/api/posts", formData, {
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
                title: "",
                content: "",
                post_category_id: "",
                image: '',
            };
            this.fileList = [];
        },
    },
    mounted() {
        this.fetchCategories(); // Panggil saat komponen di-mount
    },
};
</script>