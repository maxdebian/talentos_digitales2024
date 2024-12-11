import axios from "axios";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useAuthStore } from "./auth";
interface IProduct {
    id:number,
    description:string,
    image:string,
    price:number,
    increase:number,
    productStock:number,
    enabled:boolean
    _user_created:number,
    _user_updated:number
}

export const useProductStore = defineStore('product',()=>{
    const products = ref<IProduct[]|null>(null);
/*     const product = ref<IProduct|null>(null); */
    const getProducts = async () => {
        try {
            const result = await axios.get('products');
            if(result.status==200){
                products.value = result.data.data
                return
            } 
            Swal.fire({
                icon: "error",
                title: "Oops...",
            });
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: error,
            });
        }
    } 
    const getProductById = (id:number)=>{
        return products.value.filter(element => element.id == id)[0];
    }
    const storeProduct = async(form)=>{
        
        const authStore = useAuthStore()
        const res = await axios.post(`product/store`,form, {
            headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${authStore.authToken}`
            }
        })
        if(res.status==201){
            
            Swal.fire(res.data.message);
            products.value=null
        }
    }
        

    return {/* product, */products,getProducts,getProductById,storeProduct}
},{
    persist:true
}  );