<template>
    <NavBar :active="'profile'" :role="role" class="z-0 absolute" />

    <profileMenu class="z-20 relative" />

    <div class="sm:ml-64 mt-16 md:mt-0 md:px-10 w-full md:w-auto h-fit bg-custom-bg absolute md:relative">
        <p class="hidden md:block text-[32px] text-custom-gray font-medium px-4">Profile</p>
        <div class="w-full md:mt-8 md:px-4 grid justify-items-center md:flex gap-12">
            <!-- Profile Picture Box -->
            <div class="grid mt-8 md:mt-0 md:w-[318px] md:h-[312px] md:bg-white border-custom-light md:border-[1px] rounded-[4px]">
                <div class="h-fit grid md:gap-4 justify-items-center my-auto">
                    <div class="grid h-[120px] w-[120px] bg-[#F5F06E] rounded-full">
                        <p class="text-[40px] text-custom-gray font-medium m-auto">{{ user.initals }}</p>
                    </div>

                    <p class="mt-2 md:mt-0 text-custom-gray text-[24px] font-medium">{{ user.name }}</p>

                    <p class="text-[16px] text-custom-blue">{{ user.role }}</p>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="h-fit mb-16 md:mb-0 md:w-[70%] grid gap-4 justify-items-center">
                <!-- User Name -->
                <div class="w-[311px] md:w-full h-[48px] md:h-[64px] flex gap-8 text-[16px] text-custom-light-gray bg-white border-custom-light border-[1px] rounded-[4px] px-6">
                    <div class="hidden h-fit w-[200px] my-auto md:flex gap-6">
                        <UserIcon class="h-[24px] my-auto" />
                        <p class="">Name:</p>
                    </div>

                    <UserIcon class="md:hidden h-[20px] my-auto" />
                    <p class="my-auto text-custom-gray font-medium">{{ user.name }}</p>
                </div>

                <!-- User Email -->
                <div class="w-[311px] md:w-full h-[48px] md:h-[64px] flex gap-8 text-[16px] text-custom-light-gray bg-white border-custom-light border-[1px] rounded-[4px] px-6">
                    <div class="hidden h-fit w-[200px] my-auto md:flex gap-6">
                        <EnvelopeIcon class="h-[24px] my-auto" />
                        <p class="">Email:</p>
                    </div>

                    <EnvelopeIcon class="md:hidden h-[20px] my-auto" />
                    <p class="my-auto text-custom-gray font-medium">{{ user.email }}</p>
                </div>

                <!-- User Error Source -->
                <div :class="user.source == 'ALL' ? 'h-fit py-2 md:py-0' : 'h-[48px]'" class="w-[311px] md:w-full md:h-[64px] flex gap-8 text-[16px] text-custom-light-gray bg-white border-custom-light border-[1px] rounded-[4px] px-6">
                    <div class="hidden h-fit w-[200px] my-auto md:flex gap-6">
                        <MapPinIcon class="h-[24px] my-auto" />
                        <p class="">Error Source:</p>
                    </div>

                    <MapPinIcon :class="user.source == 'ALL' ? 'ml-[-5px] h-[30px]' : 'h-[20px]'" class="md:hidden my-auto" />
                    <p :class="user.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="my-auto font-medium">{{ user.source == 'ALL' ? 'RocketAutomation, RocketMGA' : user.source == 'MGA'? 'RocketMGA' : 'RocketAutomation' }}</p>
                </div>

                <!-- User Error Type -->
                <div class="w-[311px] md:w-full h-[48px] md:h-[64px] flex gap-8 text-[16px] text-custom-light-gray bg-white border-custom-light border-[1px] rounded-[4px] px-6">
                    <div class="hidden h-fit w-[200px] my-auto md:flex gap-6">
                        <ExclamationTriangleIcon class="h-[24px] my-auto" />
                        <p class="">Error Type:</p>
                    </div>

                    <ExclamationTriangleIcon class="md:hidden h-[20px] my-auto" />
                    <p class="my-auto text-custom-gray font-medium">{{ user.type == 'ALL' ? 'API, BOT' : user.type }}</p>
                </div>

                <!-- Update Email and Password -->
                <div class="w-full grid md:grid-cols-2 gap-4">
                    <!-- Email Update -->
                    <div class="grid gap-4 h-fit">
                        <button @click="changeEmail = !changeEmail" class="w-fit flex gap-2 text-white bg-custom-blue p-2 rounded-[4px] text-md font-medium">
                            <LockClosedIcon v-if="!changeEmail" class="h-4 my-auto" />
                            <LockOpenIcon v-else class="h-4 my-auto" />
                            Update Email
                        </button>

                        <form @submit.prevent="updateEmail" v-if="changeEmail" class="grid gap-4">
                            <textInput @inputUpdate="emailInputChange" :inputValue="emailForm.email" :id="'email'" :label="'Email'" :email=true />
                            <textInput @inputUpdate="emailInputChange" :inputValue="emailForm.confirm_email" :id="'confirm_email'" :label="'Confirm Email'" :email=true />
                            <textInput @inputUpdate="emailInputChange" :inputValue="emailForm.password" :id="'password'" :label="'Password'" :password=true />
                            <input type="submit" value="change" class="uppercase text-sm font-bold text-white bg-custom-blue rounded-[4px] p-2 hover:cursor-pointer">
                        </form>
                    </div>

                    <!-- Password Update -->
                    <div class="grid gap-4 h-fit">
                        <button @click="changePassword = !changePassword" class="w-fit flex gap-2 text-white bg-custom-blue p-2 rounded-[4px] text-md font-medium">
                            <LockClosedIcon v-if="!changePassword" class="h-4 my-auto" />
                            <LockOpenIcon v-else class="h-4 my-auto" />
                            Update Password
                        </button>

                        <form @submit.prevent="updatePassword" v-if="changePassword" class="grid gap-4">
                            <textInput @inputUpdate="passwordInputChange" :inputValue="passwordForm.password" :id="'password'" :label="'Password'" />
                            <textInput @inputUpdate="passwordInputChange" :inputValue="passwordForm.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" />
                            <input type="submit" value="change" class="uppercase text-sm font-bold text-white bg-custom-blue rounded-[4px] p-2 hover:cursor-pointer">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { UserIcon, ExclamationTriangleIcon, MapPinIcon, EnvelopeIcon, LockOpenIcon, LockClosedIcon } from '@heroicons/vue/24/solid'
import NavBar from '../components/navbar.vue'
import textInput from '../components/textInput.vue'
import profileMenu from '../components/profileMenu.vue'

export default {
    name: "Profile",
    data(){
        return {
            user: {},
            changeEmail: false,
            changePassword: false,
            emailForm: {
                email: '',
                confirm_email: '',
                password: ''
            },
            passwordForm: {
                email: '',
                password: '',
                confirm_password: ''
            },
            emailErrors: [
                {
                    name: 'email',
                    errors: []
                },
                {
                    name: 'confirm_email',
                    errors: []
                }
            ],
            passwordErrors: [
                {
                    name: 'password',
                    errors: []
                },
                {
                    name: 'confirm_password',
                    errors: []
                }
            ]
        }
    },
    async created(){
        await axios.get('/api/user')
        .then(response => {
            this.user = response.data
            const names = this.user.name.split(" ")
            const i1 = Array.from(names[0])[0]
            const i2 = Array.from(names[1])[0]
            this.user.initals = i1+i2
        })
    },
    methods: {
        emailInputChange(id, value, errors){
            this.emailForm[id] = value

            if(errors){
                this.emailErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.emailErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        passwordInputChange(id, value, errors){
            this.passwordForm[id] = value
            
            if(errors){
                this.passwordErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.passwordErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        async updateEmail(){
            let valid = true

            this.emailErrors.forEach(item => {
                if(item.errors.length > 0){
                    valid = false

                    item.errors.forEach(error => {
                        this.$alert({
                            title: 'Validation Error',
                            text: error,
                            type: 'warn'
                        })
                    })
                }
            })

            const emailKeys = Object.keys(this.emailForm)

            emailKeys.forEach(key => {
                if(this.emailForm[key] == ''){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            if(valid){
                await axios.post('/api/resetEmail', this.emailForm)
               .then(response => {
                    location.reload()
                    this.$alert({
                        title: 'Email Reset',
                        text: response.data.message,
                        type: 'success'
                    })
               })
               .catch(error => {
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
                        setTimeout(() => {
                            location.reload()
                        }, 500)
                        
                    }
                })
            }
        },
        async updatePassword(){
            this.passwordForm.email = this.user.email
            let valid = true

            this.passwordErrors.forEach(item => {
                if(item.errors.length > 0){
                    valid = false

                    item.errors.forEach(error => {
                        this.$alert({
                            title: 'Validation Error',
                            text: error,
                            type: 'warn'
                        })
                    })
                }
            })

            const passwordKeys = Object.keys(this.passwordForm)

            passwordKeys.forEach(key => {
                if(this.passwordForm[key] == ''){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            if(valid){
                await axios.post('/api/resetPassword', this.passwordForm)
               .then(response => {
                    location.reload()
                    this.$alert({
                        title: 'Password Reset',
                        text: response.data.message,
                        type: 'success'
                    })
               })
               .catch(error => {
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
                        setTimeout(() => {
                            location.reload()
                        }, 500)
                        
                    }
                })
            }
        }
    },
    components: {
        NavBar,
        profileMenu,
        textInput,
        UserIcon,
        EnvelopeIcon,
        MapPinIcon,
        ExclamationTriangleIcon,
        LockOpenIcon,
        LockClosedIcon
    }
}
</script>