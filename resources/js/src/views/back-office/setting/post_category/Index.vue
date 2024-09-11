<template>
    <div>
        <div class="container-fluid">
            <div class="col px-0">
                <div class="row">
                    <div class="col-lg-12 pt-sm pb-5" ref="modalHapusContainer">
                        <h4 id="dashboard">Data Post Category</h4>
                        <card shadow>
                            <base-button size="sm" type="success" class="mb-3" @click="openAddModal">
                                <i class="fa fa-plus"></i> Tambah Data Post Category
                            </base-button>
                            <div class="mb-3">
                                <base-input v-model="searchQuery" size="small" placeholder="Cari data..." addon-left-icon="fa fa-search"></base-input>
                            </div>
                            <div class="table-responsive">
                                <a-table :pagination="pagination" :columns="columns" :data-source="filteredPostCategory"
                                    row-key="id" size="small">
                                    <template #noRender="{ index }">
                                        {{ index + 1}}
                                    </template>
                                    <template slot="actionsRender" slot-scope="record">

                                        <base-button type="primary" size="sm" @click="openEditModal(record)">
                                        <i class="fa fa-pencil"></i> Edit
                                        </base-button>
                                        <base-button type="danger" size="sm" @click="deleteData(record.id)">
                                        <i class="fa fa-trash"></i> Hapus
                                        </base-button>
                                    </template>
                                </a-table>
                            </div>
                        </card>
                    </div>
                </div>
            </div>
        </div>

        <modal :show.sync="modals.tambah">
            <h6 slot="header" class="modal-title" id="modal-title-defaultt">Tambah Data Post Category</h6>
            <TambahPostCategory :post_category="post_category" @data-added="handleDataSubmit"></TambahPostCategory>
            <template slot="footer">
                <button type="submit" class="btn btn-primary" form="proses-tambah-post-category"><i class="fa fa-send"></i> Simpan</button>
                <button type="button" class="btn btn-outline-primary" @click="modals.tambah = false"><i class="fa fa-reply-all"></i> Keluar</button>
            </template>
        </modal>

        <modal :show.sync="modals.edit">
            <h6 slot="header" class="modal-title" id="modal-title-defaulte">Edit Data Post Category</h6>
            <EditPostCategory :dataUpdate="selectedItem" @data-updated="handleDataSubmit"></EditPostCategory>
            <template slot="footer">
                <button type="submit" class="btn btn-primary" form="proses-edit-post-category"><i class="fa fa-send"></i> Update</button>
                <button type="button" class="btn btn-outline-primary" @click="modals.edit = false"><i class="fa fa-reply-all"></i> Keluar</button>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "~/components-argon/Modal.vue";
import TambahPostCategory from "./Tambah.vue";
import EditPostCategory from "./Edit.vue";

import { Modal as ModalAntDesign } from 'ant-design-vue';

import axios from 'axios';

export default {
    name: "IndexPostCategory",
    components: {
        Modal,
        ModalAntDesign,
        TambahPostCategory,
        EditPostCategory
    },
    data() {
        return {
            modals: {
                tambah: false,
                edit: false
            },
            post_category: [], // Menyimpan data posts
            selectedItem: null, // Menyimpan item yang dipilih untuk diedit
            searchQuery: '',
            pagination: {
                pageSize: 5, // Ukuran pagination
            },
            columns: [
                {
                    title: 'No.',
                    key: 'no',
                    width: '5%',
                    customRender: (text, record, index) => index + 1
                },
                {
                    title: 'Category',
                    dataIndex: 'name',
                    key: 'category',
                    width: '25%',
                },
                {
                    title: 'Actions',
                    key: 'actions',
                    width: '25%',
                    scopedSlots: { customRender: 'actionsRender' }
                }
            ],
        };
    },
    methods: {
        async fetchData() {
            try {
                const response = await axios.get('/api/categories'); // Ambil data dari API
                this.post_category = response.data;
                console.log(this.post_category);

                console.log(this.columns);
            } catch (error) {
                console.error('Gagal mengambil data:', error);
            }
        },

        handleDataSubmit() {
            this.modals.tambah = false; // Tutup modal tambah
            this.modals.edit = false; // Tutup modal edit
            this.fetchData(); // Ambil data terbaru
        },

        showDeleteConfirm() {
            return new Promise((resolve) => {
                ModalAntDesign.confirm({
                title: 'Konfirmasi',
                content: 'Apakah Anda yakin ingin hapus data ini?',
                okText: 'Hapus',
                cancelText: 'Batal',
                getContainer: () => this.$refs.modalHapusContainer, // Tentukan container untuk modal konfirmasi
                    onOk() {
                    console.log('User confirmed delete');
                    resolve();
                    },
                    okType: 'danger',
                });
            });
        },


        async deleteData(id) {
            try {
                await this.showDeleteConfirm();

                await axios.delete(`/api/categories/${id}`); // Hapus data dari API
                this.$message.success('Data berhasil dihapus!');
                this.fetchData(); // Ambil data terbaru
            } catch (error) {
                if (error.message !== 'User cancelled delete') {
                    console.error('Gagal menghapus data:', error);
                    this.$message.error('Gagal menghapus data');
                }
            }
        },

        openAddModal() {
            this.selectedItem = null; // Reset selected item
            this.modals.tambah = true; // Tampilkan modal tambah
        },

        openEditModal(record) {
            console.log("Record:", record);
            console.log("post_category", this.post_category);

            // Cek jika dataUpdate sudah diisi
            const target = this.post_category.find(item => item.id === record.id);

            if (target) {
                this.selectedItem = target;
                console.log("Selected", this.selectedItem);
                this.modals.edit = true; // Pindahkan setelah `selectedItem` terisi
            } else {
                console.error("Item not found");
            }
        }


    },
    mounted() {
        this.fetchData(); // Ambil data saat komponen dimuat
    },
    computed: {
        filteredPostCategory() {
            if (!this.searchQuery) {
                return this.post_category;
            }
            const query = this.searchQuery.toLowerCase();
            return this.post_category.filter(item => {
                console.log("item", item)
                return item.name.toLowerCase().includes(query)
            });
        }
    },
};
</script>
