<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <div class="w-screen h-screen grid">
        <!-- Header -->
        <div class="w-full h-[50px] grid border-b-[1px] border-custom-light bg-white">
            <div class="m-auto flex gap-4">
                <img src="../../assets/logo.png" alt="Rocket Automation Logo" class="h-[35px] my-auto">
                <p class="mt-2 text-[30px] text-custom-blue font-medium">R.O.V.E.R</p>
            </div>
        </div>
        
        <!-- Login Form -->
        <form @submit.prevent="login" class="w-fit h-fit mx-auto bg-white border-[1px] border-custom-light p-6 rounded-[6px]">
            <div class="grid gap-2 w-[300px] my-8">
                <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email'" :placeholderText="'john@doe.com'" :email="true" />
                <textInput @inputUpdate="inputChange" :inputValue="form.password" :id="'password'" :label="'Password'" :password="true" />

                <input type="submit" value="Login" class="mt-4 cursor-pointer bg-custom-blue text-[15px] text-white font-medium py-2 border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop active:shadow-none">
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="w-full md:absolute bottom-0 py-2 px-6 border-t border-custom-light text-center text-[14px] text-custom-dark-blue bg-white">
        <p>© 2023 <a target="_blank" href="https://rocketmga.com">ROCKETFLOOD</a>   |  ALL RIGHTS RESERVED  |  <a target="_blank" href="https://rocketflood.com/legal/privacy-policy">PRIVACY POLICY</a></p>
    </div>
</template>

<script>
import loading from '../components/loading.vue'
import textInput from '../components/textInput.vue'

export default {
    name: "Home",
    data(){
        return {
            loading: true,
            form: {
                email: "",
                password: ""
            },
            errors: [
                {
                    name: 'email',
                    errors: []
                },
                {
                    name: 'password',
                    errors: []
                }
            ]
        }
    },
    async created(){
        const accessToken = localStorage.getItem('token');
        if (accessToken) {
            axios.post('/api/token/validate')
            .then(response => {
                if (response.data.valid) {
                    this.$router.push({name: 'Dashboard'})
                }
            })
            .catch(error => {
                console.error(error);
            });
        }

        this.loading = false
    },
    methods: {
        inputChange(id, value, errors){
            this.form[id] = value
            
            if(errors){
                this.errors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.errors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        async login(){
            this.loading = true

            await axios.post('/api/login', this.form)
            .then(response => {
                if(response.data.success){
                    localStorage.setItem('token', response.data.token)
                    this.$router.push({name: 'Dashboard'})
                    this.$alert({
                        title: 'Login',
                        text: response.data.message,
                        type: 'success'
                    })
                }
            })
            .catch(error => {
                this.form.email = ''
                this.form.password = ''
                if(error.response.status == 400){
                    const keys = Object.keys(error.response.data.message)
                    keys.forEach(key => {
                        error.response.data.message[key].forEach(error => {
                            this.$alert({
                                title: 'Login Error',
                                text: error,
                                type: 'error'
                            })
                        })
                    })
                } else if(error.response.status == 401){
                    this.$alert({
                        title: 'Login Error',
                        text: error.response.data.message,
                        type: 'error'
                    })
                }

                this.loading = false
            })
        }
    },
    components: {
        loading,
        textInput
    }
}
</script>