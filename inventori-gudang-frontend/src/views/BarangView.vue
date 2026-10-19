<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import http from '../api/http'

const fields = [
  { key: 'kode_barang', label: 'Kode barang', type: 'text' },
  { key: 'nama', label: 'Nama barang', type: 'text' },
  { key: 'kategori', label: 'Kategori', type: 'text' },
  { key: 'satuan', label: 'Satuan (pcs, box, roll)', type: 'text' },
  { key: 'stok', label: 'Stok', type: 'number' },
  { key: 'harga', label: 'Harga satuan (Rp)', type: 'number' },
  { key: 'lokasi_rak', label: 'Lokasi rak', type: 'text' },
]
const STOK_MENIPIS = 20

const items = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const cari = ref('')
const loading = ref(false)
const loadError = ref('')
const notice = ref('')

const drawer = ref(false)
const editingId = ref(null)
const saving = ref(false)
const errors = ref({})
const formError = ref('')

const emptyForm = () => ({
  kode_barang: '', nama: '', kategori: '', satuan: '', stok: 0, harga: 0, lokasi_rak: '',
})
const form = reactive(emptyForm())

const rupiah = (n) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n)

function showNotice(text) {
  notice.value = text
  setTimeout(() => (notice.value = ''), 3000)
}

async function load(page = 1) {
  loading.value = true
  loadError.value = ''
  try {
    const { data } = await http.get('/barang', {
      params: { page, cari: cari.value || undefined },
    })
    items.value = data.data
    meta.value = { current_page: data.current_page, last_page: data.last_page, total: data.total }
  } catch (e) {
    loadError.value = e.response?.data?.message || 'Data barang gagal dimuat. Periksa koneksi ke API.'
  } finally {
    loading.value = false
  }
}

let timer
watch(cari, () => {
  clearTimeout(timer)
  timer = setTimeout(() => load(1), 350)
})
onMounted(() => load())

function openCreate() {
  Object.assign(form, emptyForm())
  editingId.value = null
  errors.value = {}
  formError.value = ''
  drawer.value = true
}

function openEdit(item) {
  Object.assign(form, {
    kode_barang: item.kode_barang,
    nama: item.nama,
    kategori: item.kategori ?? '',
    satuan: item.satuan,
    stok: item.stok,
    harga: Number(item.harga),
    lokasi_rak: item.lokasi_rak ?? '',
  })
  editingId.value = item.id
  errors.value = {}
  formError.value = ''
  drawer.value = true
}

async function save() {
  saving.value = true
  errors.value = {}
  formError.value = ''
  const payload = { ...form, kategori: form.kategori || null, lokasi_rak: form.lokasi_rak || null }
  try {
    if (editingId.value) {
      await http.put(`/barang/${editingId.value}`, payload)
      showNotice('Barang berhasil diubah.')
    } else {
      await http.post('/barang', payload)
      showNotice('Barang berhasil ditambahkan.')
    }
    drawer.value = false
    await load(editingId.value ? meta.value.current_page : 1)
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors
    } else {
      formError.value = e.response?.data?.message || 'Data gagal disimpan.'
    }
  } finally {
    saving.value = false
  }
}

async function remove(item) {
  if (!confirm(`Hapus "${item.nama}" dari gudang?`)) return
  try {
    await http.delete(`/barang/${item.id}`)
    showNotice('Barang berhasil dihapus.')
    const page = items.value.length === 1 && meta.value.current_page > 1
      ? meta.value.current_page - 1
      : meta.value.current_page
    await load(page)
  } catch (e) {
    loadError.value = e.response?.data?.message || 'Barang gagal dihapus.'
  }
}
</script>

<template>
  <main class="page">
    <div class="toolbar">
      <div>
        <h1>Daftar barang</h1>
        <p class="muted">{{ meta.total }} barang tercatat</p>
      </div>
      <div class="actions">
        <input v-model="cari" class="search" type="search" placeholder="Cari nama atau kode" aria-label="Cari barang" />
        <button class="btn primary" @click="openCreate">Tambah barang</button>
      </div>
    </div>

    <p v-if="notice" class="notice" role="status">{{ notice }}</p>
    <p v-if="loadError" class="alert" role="alert">{{ loadError }}</p>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th class="num">Stok</th>
            <th class="num">Harga</th>
            <th>Rak</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="empty">Memuat data...</td>
          </tr>
          <tr v-else-if="!items.length">
            <td colspan="7" class="empty">
              {{ cari ? 'Tidak ada barang yang cocok dengan pencarian.' : 'Belum ada barang. Klik "Tambah barang" untuk mulai mencatat.' }}
            </td>
          </tr>
          <tr v-for="item in items" v-else :key="item.id">
            <td class="kode">{{ item.kode_barang }}</td>
            <td>{{ item.nama }}</td>
            <td>{{ item.kategori || '-' }}</td>
            <td class="num">
              <span :class="['stok', { low: item.stok <= STOK_MENIPIS }]">
                {{ item.stok }} {{ item.satuan }}
              </span>
            </td>
            <td class="num">{{ rupiah(item.harga) }}</td>
            <td>{{ item.lokasi_rak || '-' }}</td>
            <td class="row-actions">
              <button class="btn ghost" @click="openEdit(item)">Ubah</button>
              <button class="btn danger" @click="remove(item)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <nav v-if="meta.last_page > 1" class="pager" aria-label="Halaman">
      <button class="btn ghost" :disabled="meta.current_page <= 1" @click="load(meta.current_page - 1)">Sebelumnya</button>
      <span>Halaman {{ meta.current_page }} dari {{ meta.last_page }}</span>
      <button class="btn ghost" :disabled="meta.current_page >= meta.last_page" @click="load(meta.current_page + 1)">Berikutnya</button>
    </nav>

    <div v-if="drawer" class="backdrop" @click.self="drawer = false">
      <aside class="drawer" role="dialog" aria-modal="true" :aria-label="editingId ? 'Ubah barang' : 'Tambah barang'">
        <h2>{{ editingId ? 'Ubah barang' : 'Tambah barang' }}</h2>
        <form @submit.prevent="save" novalidate>
          <p v-if="formError" class="alert" role="alert">{{ formError }}</p>

          <template v-for="f in fields" :key="f.key">
            <label :for="f.key">{{ f.label }}</label>
            <input
              :id="f.key"
              v-model="form[f.key]"
              :type="f.type"
              :min="f.type === 'number' ? 0 : undefined"
              :step="f.key === 'harga' ? 'any' : undefined"
              @input="errors[f.key] = null"
            />
            <p v-if="errors[f.key]" class="err">{{ errors[f.key][0] }}</p>
          </template>

          <div class="drawer-actions">
            <button type="button" class="btn ghost" @click="drawer = false">Batal</button>
            <button class="btn primary" :disabled="saving">{{ saving ? 'Menyimpan...' : 'Simpan' }}</button>
          </div>
        </form>
      </aside>
    </div>
  </main>
</template>
