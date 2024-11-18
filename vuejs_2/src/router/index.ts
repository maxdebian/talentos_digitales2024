import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/quienes-somos',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/counter',
      name: 'counter',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/CounterView.vue'),
    },
    {
      path:'/usuarios',
      name:'users',
      component: ()=> import('../views/UserAllView.vue')
    },
    {
      path:'/usuarios-detalle/:userId/:firstName',
      name:'userDetail',
      component: ()=> import('../views/UserDetailView.vue')
    }
  ],
})

export default router
