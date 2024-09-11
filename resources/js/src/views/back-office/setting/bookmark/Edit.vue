<template>
<div>
    <form id="proses-edit-bookmark" @submit.prevent="submitForm" method="PUT" ref="modalEditContainer">
    <div class="row">
        <!-- Post -->
        <div class="col-lg-12 col-sm-12 mb-2">
            <label for="post">Post</label>
            <a-select
                size="large"
                class="mt-2"
                show-search
                placeholder="[Pilih Post]"
                option-filter-prop="children"
                :filter-option="filterPostOption"
                @change="handlePostChange"
                :key="posts.length"  
                >
                <a-select-option v-for="post in posts" :key="post.id" :value="post.id">
                    {{ post.title }}
                </a-select-option>
            </a-select> 
          </div>
    </div>
    </form>
</div>
</template>
<script>
import { Modal } from "ant-design-vue";
import axios from "axios";

export default {
    name: "EditBookmark",
    props: {
        dataUpdate: Object,
        posts: Array, 
    },
    data() {
        return {
            form: {
                post_id: "",
            },
            container: null,
        };
    },
    methods: {
        handlePostChange(value) {
            this.form.post_id = value;
        },
        filterPostOption(input, option) {
            return (
                option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
            );
        },
        async submitForm() {
            try {
                // Validasi password
                if (this.form.password !== this.form.password_confirmation) {
                    this.$message.error('Password dan konfirmasi password tidak cocok!');
                    return;
                }

                await this.showEditConfirm();

                const formData = new FormData();

                formData.append("post_id", this.form.post_id);

                formData.append("_method", "PUT");

                const response = await axios.post(
                    `/api/bookmarks/${this.dataUpdate.id}`,
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );

                this.$message.success("Data berhasil diupdate!");
                this.clear();
                this.$emit("data-updated");
                this.$emit("post-updated");
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
                post_id: "",
            };
        },
    },
    watch: {
        dataUpdate: {
            immediate: true,
            handler(newData) {
                if (newData) {
                    this.form = {
                        post_id: newData.post_id || "",
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
