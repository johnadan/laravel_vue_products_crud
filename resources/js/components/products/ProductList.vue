<template>
  <!-- <router-link to="/products" class="nav-link">Products</router-link> -->
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Product List</h2>
      <router-link to="/products/create" class="btn btn-primary">Create</router-link>
    </div>

    <div class="mb-3">
      <input v-model="search" @keyup.enter="fetchProducts" class="form-control" placeholder="Search by name or description..." />
      <button @click="fetchProducts" class="btn btn-secondary mt-2">Search</button>
    </div>

    <div v-if="alert" :class="`alert alert-${alert.type}`">{{ alert.message }}</div>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Name</th>
          <th>Category</th>
          <th>Description</th>
          <th>Date & Time</th>
          <th>Images</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products.data" :key="product.id">
          <td>{{ product.name }}</td>
          <td>{{ product.category?.name }}</td>
          <td v-html="product.description"></td>
          <td>{{ product.date_time }}</td>
          <td>
            <img v-for="img in product.images" :src="imageUrl(img.image_path)" :key="img.id" width="50" class="me-1" />
          </td>
          <td>
            <router-link :to="`/products/${product.id}/edit`" class="btn btn-sm btn-warning me-1">Edit</router-link>
            <button @click="removeProduct(product.id)" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <nav v-if="products.last_page > 1">
      <ul class="pagination">
        <li class="page-item" :class="{disabled: products.current_page === 1}">
          <a class="page-link" href="#" @click.prevent="fetchProducts(products.current_page - 1)">Previous</a>
        </li>
        <li class="page-item"
            v-for="page in products.last_page"
            :key="page"
            :class="{active: page === products.current_page}">
          <a class="page-link" href="#" @click.prevent="fetchProducts(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{disabled: products.current_page === products.last_page}">
          <a class="page-link" href="#" @click.prevent="fetchProducts(products.current_page + 1)">Next</a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref({ data: [], current_page: 1, last_page: 1 })
const search = ref('')
const alert = ref(null)

// function imageUrl(path) {
//   return `/storage/${path}`
// }
function imageUrl(path) {
  if (!path) return '';
  // If path starts with http or https, return as is
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }
  // Otherwise, treat as local storage path
  return `/storage/${path}`;
}

async function fetchProducts(page = 1) {
  try {
    const { data } = await axios.get('/api/products', {
      params: { search: search.value, page }
    })
    products.value = data
  } catch (e) {
    alert.value = { type: 'danger', message: 'Failed to fetch products.' }
  }
}

async function removeProduct(id) {
  if (!confirm('Are you sure you want to delete this product?')) return
  try {
    await axios.delete(`/api/products/${id}`)
    alert.value = { type: 'success', message: 'Product deleted.' }
      fetchProducts(products.value.current_page)
    setTimeout(() => {
      alert.value = null
    }, 2000) // Dismiss after 2 seconds
  } catch (e) {
      alert.value = { type: 'danger', message: 'Failed to delete product.' }
    setTimeout(() => {
      alert.value = null
    }, 2000) // Dismiss after 2 seconds
  }
}

onMounted(() => {
  fetchProducts()
})
</script>

<style scoped>
.table img {
  max-height: 50px;
}
</style>