import { createRouter, createWebHistory } from 'vue-router'
import ProductList from '../components/products/ProductList.vue'
import ProductCreate from '../components/products/ProductCreate.vue'
import ProductEdit from '../components/products/ProductEdit.vue'

const routes = [
  { path: '/products', component: ProductList },
  { path: '/products/create', component: ProductCreate },
    // { path: '/products/:id/edit', component: ProductEdit, props: true },
  {
    path: '/products/:id/edit',
    name: 'products.edit',
    //   component: () => import('../components/products/ProductEdit.vue'),
    component: ProductEdit,
    props: true
  }
  // ...other routes
]

export default createRouter({
  history: createWebHistory(),
  routes,
})