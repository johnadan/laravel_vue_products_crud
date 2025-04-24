<template>
  <div>
    <div v-if="alert" :class="`alert alert-${alert.type} mt-3`">{{ alert.message }}</div>
    <h2>Create Product</h2>
    <!-- <form @submit.prevent="nextStep"> -->
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
        <!-- <button type="submit" class="btn btn-primary">Next</button> -->
        <button type="button" class="btn btn-primary" @click="nextStep">Next</button>
      </div>
      <!-- Steps 2 and 3 will go here -->
      <!-- <div v-if="step === 2">
        <div class="mb-3">
          <label>Images</label>
          <input type="file" @change="handleImageUpload" class="form-control" :class="{'is-invalid': errors.images}" required>
          <div v-if="errors.images" class="invalid-feedback">{{ errors.images }}</div>
        </div>
        <div class="mb-3">
          <label>Date & Time</label>
          <input type="datetime-local" v-model="form.date_time" class="form-control" :class="{'is-invalid': errors.date_time}" required>
          <div v-if="errors.date_time" class="invalid-feedback">{{ errors.date_time }}</div>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
      </div> -->

      <div v-else-if="step === 2">
        <div class="mb-3">
          <label>Images</label>
          <input type="file" multiple @change="onFilesChange" class="form-control" :class="{'is-invalid': errors.images}">
          <!-- <div v-if="errors.images" class="invalid-feedback">{{ errors.images }}</div> -->
          <div v-if="errors.images" class="invalid-feedback">
            <div v-for="(err, idx) in errors.images" :key="idx">{{ err }}</div>
          </div>
          <div class="mt-2">
            <span v-for="(img, idx) in previewImages" :key="idx">
              <img :src="img" width="80" class="me-2 mb-2" />
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
            <span v-for="(img, idx) in previewImages" :key="idx">
              <img :src="img" width="80" class="me-2 mb-2" />
            </span>
          </li>
          <li><b>Date & Time:</b> {{ form.date_time }}</li>
        </ul>
        <button type="button" class="btn btn-secondary me-2" @click="prevStep">Back</button>
        <button type="submit" class="btn btn-success">Create Product</button>
      </div>
    </form>
  </div>
</template>

<script setup>
// import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
const router = useRouter()
import { ref, onMounted, computed } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import axios from 'axios'

const step = ref(1)
const categories = ref([])
const form = ref({
  name: '',
  category_id: '',
  description: '',
    // Will add images & date_time in later steps
  images: [],
  date_time: '',
})
const errors = ref({})
const alert = ref(null)
const previewImages = ref([])

onMounted(async () => {
  const { data } = await axios.get('/api/categories')
  categories.value = data
})

const selectedCategoryName = computed(() => {
  const cat = categories.value.find(c => c.id === form.value.category_id)
  return cat ? cat.name : ''
})

// function nextStep() {
//   errors.value = {}
//   if (!form.value.name) errors.value.name = 'Name is required'
//   if (!form.value.category_id) errors.value.category_id = 'Category is required'
//   if (!form.value.description) errors.value.description = 'Description is required'
//   if (Object.keys(errors.value).length === 0) {
//     step.value = 2 // Proceed to next step
//   }
// }

function nextStep() {
  errors.value = {}
  if (step.value === 1) {
    if (!form.value.name) errors.value.name = 'Name is required'
    if (!form.value.category_id) errors.value.category_id = 'Category is required'
    if (!form.value.description) errors.value.description = 'Description is required'
    if (Object.keys(errors.value).length === 0) step.value++
  } else if (step.value === 2) {
    if (!form.value.images.length) errors.value.images = 'At least one image is required'
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

async function handleSubmit() {
  errors.value = {}
  if (!form.value.date_time) errors.value.date_time = 'Date & Time is required'
  if (Object.keys(errors.value).length > 0) return

  // Prepare form data for file upload
  const payload = new FormData()
  payload.append('name', form.value.name)
  payload.append('category_id', form.value.category_id)
  payload.append('description', form.value.description)
  payload.append('date_time', form.value.date_time)
  form.value.images.forEach((img, idx) => payload.append('images[]', img))

  try {
    await axios.post('/api/products', payload, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    alert.value = { type: 'success', message: 'Product created successfully!' }
    setTimeout(() => router.push('/products'), 1200)
    // Optionally, redirect to product list after a delay
  } catch (e) {
    alert.value = { type: 'danger', message: e.response?.data?.message || 'Failed to create product.' }
    //   if (e.response?.data?.errors) errors.value = e.response.data.errors
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