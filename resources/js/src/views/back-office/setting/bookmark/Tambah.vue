<template>
    <div>
      <form id="proses-tambah-bookmark" @submit.prevent="submitForm" method="POST" ref="modalTambahContainer">
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
import Tabs from "~/components-argon/Tabs/Tabs.vue";
import TabPane from "~/components-argon/Tabs/TabPane.vue";

import { Modal } from 'ant-design-vue';
import axios from "axios";

export default {
    name: "TambahBookmark",
    components: {
        Tabs,
        TabPane,
        Modal
    },
    props: {
        bookmarks: Array,
        posts: Array, 
    },
    data() {
        return {
            form: {
                post_id: "",
            },
            container:null,
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

                await this.showTambahConfirm();

                const formData = new FormData();
                
                formData.append("post_id", this.form.post_id);
                
                const response = await axios.post("/api/bookmarks", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });

                this.$message.success('Data berhasil ditambahkan!');
                this.clear();
                this.$emit("data-added");
                this.$emit("post-added");

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
                post_id: "",
            };
        },
    },
};
</script>