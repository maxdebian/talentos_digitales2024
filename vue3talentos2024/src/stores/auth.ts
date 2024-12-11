
import axios from "axios";
import { defineStore } from "pinia";

export const useAuthStore = defineStore('auth',{
    
    state:()=>({authUser:null, authToken:null}),
    getters:{
        user:(state)=>state.authUser,
        token:(state)=>state.authToken
    },
    actions:{
        /* async getToken(){
            await axios.get('/sanctum/csrf-cookie');
        }, */
        async login(form){
            //const router = useRouter()
            //const x = await this.getToken();
            
            try{
                const result = await axios.post('login',form);
/*                 console.log(result); 
                debugger */
                if(result.status == 200){
                    this.authToken = result.data.token;
                    this.authUser = result.data.user;
                    return true
                }
                return false 

            }catch(errors){
                   alert(errors)
            } 
        },
        async register(form){
            //await this.getToken();
            await axios.post('register',form).then(
                (res)=>{
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: res.data.message,
                        showConfirmButton: false,
                        timer: 1500
                      });
/*                     Swal.fire(); */
                    //alert(res.data.message);
/*                     setTimeout(()=>//router.push('/login') console.log('ok'),
                    2000) */
                }
            ).catch(
                (errors) =>{
                    console.log(errors)
                }
            )
        },
        async logout(){
/*             console.log(this.token) */

             const result = await axios.post('logout',this.authUser, {
                headers: {
                  'Authorization': `Bearer ${this.token}`
                }}); 
            /* this.token */
            /* const result = await axios.post('http://localhost:9000/api/logout',this.authUser); */
            /* console.log(result) */
            this.authToken=null;
            this.authUser=null;

        }
    },
    persist:true
})