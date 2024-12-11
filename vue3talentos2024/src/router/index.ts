import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/layout',
      name: 'layout',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/LayoutView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../components/User/Login.vue'),
    },
    {
      path: '/login2',
      name: 'login2',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../components/User/LoginWithStore.vue'),
    },
    {
      path: '/register',
      name: 'register',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../components/User/Register.vue'),
    },
    {
      path: '/register2',
      name: 'register2',
      component: () => import('../components/User/RegisterWithStore.vue'),
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('../views/ProductsView.vue'),
    },
    {
      path:'/productById/:id',
      name:'productById',
      component: () => import('../components/Product/Product.vue'),
    },
    {
      path:'/productStore',
      name:'productStore',
      component: () => import('../components/Product/Store.vue'),
    }

    
  ],
})
router.beforeEach(async (to) =>{
  const publicPages = ['/login','/login2','/register','/register2','/','/about']
  const authRequired = !publicPages.includes(to.path)
  const authStore = useAuthStore();
  if(authRequired && !authStore.authUser){
    //auth.returnUrl = to.fullPath;
    return '/login2';
  }
})

export default router
