<template>
  <div>
    <div v-if="alert" :class="`alert alert-${alert.type} mt-3`">{{ alert.message }}</div>
    <h2>Edit Product</h2>
    <form @submit.prevent="handleSubmit">
      <div v-if="step === 1">
        <div class="mb-3">
          <label>Name</label>
          <input v-model="form.name" type="text" class="form-control" :class="{'is-invalid': errors.name}" required>
          <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
        </div>
        <div class="mb-3">
          <label>Category</label>
          <select v-model="form.category_id" class="form-control" :class="{'is-invalid': errors.category_id}" required>
            <option value="">Select category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <div v-if="errors.category_id" class="invalid-feedback">{{ errors.category_id }}</div>
        </div>
        <div class="mb-3">
          <label>Description</label>
          <QuillEditor v-model:content="form.description" contentType="html" class="bg-white" />
          <div v-if="errors.description" class="text-danger">{{ errors.description }}</div>
        </div>
        <button type="button" class="btn btn-primary" @click="nextStep">Next</button>
      </div>

      <div v-else-if="step === 2">
        <div class="mb-3">
          <label>Images</label>
          <input type="file" multiple @change="onFilesChange" class="form-control" :class="{'is-invalid': errors.images}">
          <div v-if="errors.images" class="invalid-feedback">
            <div v-for="(err, idx) in errors.images" :key="idx">{{ err }}</div>
          </div>
          <div class="mt-2">
            <span v-for="(img, idx) in allImages" :key="idx" style="position:relative;display:inline-block;">
              <img :src="img.existing ? imageUrl(img.url) : img.url" width="80" class="me-2 mb-2" />
              <button v-if="img.existing" type="button" class="btn btn-sm btn-danger" style="position:absolute;top:0;right:0"
                @click="removeExistingImage(idx)">×</button>
              <button v-else type="button" class="btn btn-sm btn-danger" style="position:absolute;top:0;right:0"
                @click="removeNewImage(idx)">×</button>
            </span>
          </div>
        </div>
        <button type="button" class="btn btn-secondary me-2" @click="prevStep">Back</button>
        <button type="button" class="btn btn-primary" @click="nextStep">Next</button>
      </div>

      <div v-else-if="step === 3">
        <div class="mb-3">
          <label>Date & Time</label>
          <input v-model="form.date_time" type="datetime-local" class="form-control" :class="{'is-invalid': errors.date_time}">
          <div v-if="errors.date_time" class="invalid-feedback">{{ errors.date_time }}</div>
        </div>
        <h5 class="mt-4">Review</h5>
        <ul>
          <li><b>Name:</b> {{ form.name }}</li>
          <li><b>Category:</b> {{ selectedCategoryName }}</li>
          <li><b>Description:</b> <span v-html="form.description"></span></li>
          <li><b>Images:</b>
            <span v-for="(img, idx) in allImages" :key="idx">
              <!-- <img :src="img.url" width="80" class="me-2 mb-2" /> -->
              <img :src="img.existing ? imageUrl(img.url) : img.url" width="80" class="me-2 mb-2" />
            </span>
          </li>
          <li><b>Date & Time:</b> {{ form.date_time }}</li>
        </ul>
        <button type="button" class="btn btn-secondary me-2" @click="prevStep">Back</button>
        <button type="submit" class="btn btn-success">Update Product</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted, computed } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const step = ref(1)
const categories = ref([])
const form = ref({
  name: '',
  category_id: '',
  description: '',
  images: [], // new images
  date_time: '',
})
const errors = ref({})
const alert = ref(null)
const existingImages = ref([]) // {id, url}
const previewImages = ref([]) // new images (object URLs)

const allImages = computed(() => [
  ...existingImages.value.map(img => ({ ...img, existing: true })),
  ...previewImages.value.map((url, idx) => ({ url, existing: false, idx }))
])

onMounted(async () => {
  // Fetch categories
  const { data: cats } = await axios.get('/api/categories')
  categories.value = cats

  // Fetch product details
  const { data: prod } = await axios.get(`/api/products/${route.params.id}`)
  form.value.name = prod.name
  form.value.category_id = prod.category_id
  form.value.description = prod.description
  form.value.date_time = prod.date_time ? prod.date_time.slice(0,16) : ''
  existingImages.value = (prod.images || []).map(img => ({
    id: img.id,
    url: img.image_path
  }))
})

const selectedCategoryName = computed(() => {
  const cat = categories.value.find(c => c.id === form.value.category_id)
  return cat ? cat.name : ''
})

function imageUrl(path) {
  if (!path) return '';
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }
  // For local images, prefix with /storage/
  return `/storage/${path}`;
}

function nextStep() {
  errors.value = {}
  if (step.value === 1) {
    if (!form.value.name) errors.value.name = 'Name is required'
    if (!form.value.category_id) errors.value.category_id = 'Category is required'
    if (!form.value.description) errors.value.description = 'Description is required'
    if (Object.keys(errors.value).length === 0) step.value++
  } else if (step.value === 2) {
    if (!existingImages.value.length && !form.value.images.length)
      errors.value.images = ['At least one image is required']
    if (Object.keys(errors.value).length === 0) step.value++
  }
}

function prevStep() {
  if (step.value > 1) step.value--
}

function onFilesChange(e) {
  form.value.images = Array.from(e.target.files)
  previewImages.value = form.value.images.map(file => URL.createObjectURL(file))
}

function removeExistingImage(idx) {
  existingImages.value.splice(idx, 1)
}
function removeNewImage(idx) {
  form.value.images.splice(idx, 1)
  previewImages.value.splice(idx, 1)
}

async function handleSubmit() {
  errors.value = {}
  if (!form.value.date_time) errors.value.date_time = 'Date & Time is required'
  if (Object.keys(errors.value).length > 0) return

  // Prepare form data for file upload and image removals
  const payload = new FormData()
  payload.append('name', form.value.name)
  payload.append('category_id', form.value.category_id)
  payload.append('description', form.value.description)
  payload.append('date_time', form.value.date_time)
  form.value.images.forEach((img, idx) => payload.append('images[]', img))
  // Send IDs of images to keep (backend should delete others)
  existingImages.value.forEach(img => payload.append('existing_image_ids[]', img.id))

  try {
    await axios.post(`/api/products/${route.params.id}?_method=PUT`, payload, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    alert.value = { type: 'success', message: 'Product updated successfully!' }
    setTimeout(() => router.push('/products'), 1200)
  } catch (e) {
    alert.value = { type: 'danger', message: e.response?.data?.message || 'Failed to update product.' }
    if (e.response?.data?.errors) {
      const rawErrors = e.response.data.errors
      const friendlyErrors = {}
      for (const key in rawErrors) {
        if (key.startsWith('images.')) {
          friendlyErrors['images'] = rawErrors[key]
        } else {
          friendlyErrors[key] = rawErrors[key]
        }
      }
      errors.value = friendlyErrors
    }
  }
}
</script>