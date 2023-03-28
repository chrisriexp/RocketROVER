<template>
    <NavBar :active="'users'" :role="role" class="z-0 absolute" />

    <profileMenu class="z-20 relative" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen md:mt-[-96px] grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Create User Popup -->
    <div v-if="create" class="w-screen h-screen md:mt-[-96px] grid bg-[#3F3F3F] bg-opacity-[26%] z-50 fixed">
        <form @submit.prevent="createUser()" class="m-auto w-[343px] md:w-[438px] h-fit grid bg-white rounded-[4px] md:rounded-[6px]">
            <div class="w-full h-[56px] md:h-[68px] flow-root px-6 text-[20px] md:text-[24px] text-custom-gray font-medium bg-custom-bg border-b-[1px] border-custom-light rounded-tr-[4px] rounded-tl-[4px] md:rounded-tr-[6px] md:rounded-tl-[6px]">
                <div class="grid w-fit h-full float-left"><p class="my-auto">Create User</p></div>
                <button @click="create = false" class="grid w-fit h-full float-right"><XCircleIcon class="h-[24px] my-auto hover:text-custom-red" /></button>
            </div>

            <div class="w-full h-fit grid gap-4 px-6 my-6">
                <textInput @inputUpdate="newInputChange" :inputValue="newUser.name" :id="'name'" :label="'Name'" />
                <textInput @inputUpdate="newInputChange" :inputValue="newUser.email" :id="'email'" :label="'Email'" :email=true />

                <div class="grid gap-[4px] w-full h-fit">
                    <label for="role" class="text-[16px] text-custom-light-gray font-medium">Role <span class="text-custom-red">*</span></label>
                    <VueMultiselect
                        v-model="newUser.role"
                        :options="roles"
                        :searchable="false"
                        :close-on-select="true"
                        :allow-empty="false"
                        :show-labels="false"
                        label="name"
                        :track-by="code"
                        :block-keys="['Tab', 'Enter']"
                        placeholder="Select user role"
                    />
                </div>

                <div class="grid gap-[4px] w-full h-fit">
                    <label for="role" class="text-[16px] text-custom-light-gray font-medium">Error Source <span class="text-custom-red">*</span></label>
                    <VueMultiselect
                        v-model="newUser.source"
                        :options="sources"
                        :searchable="false"
                        :close-on-select="true"
                        :allow-empty="false"
                        :show-labels="false"
                        label="name"
                        :track-by="code"
                        :block-keys="['Tab', 'Enter']"
                        placeholder="Select allowed error source"
                    />
                </div>

                <div class="grid gap-[4px] w-full h-fit">
                    <label for="role" class="text-[16px] text-custom-light-gray font-medium">Error Type <span class="text-custom-red">*</span></label>
                    <VueMultiselect
                        v-model="newUser.type"
                        :options="types"
                        :searchable="false"
                        :close-on-select="true"
                        :allow-empty="false"
                        :show-labels="false"
                        label="name"
                        :track-by="code"
                        :block-keys="['Tab', 'Enter']"
                        placeholder="Select allowed error types"
                    />
                </div>

                <textInput @inputUpdate="newInputChange" :inputValue="newUser.password" :id="'password'" :label="'Password'" />
                <textInput @inputUpdate="newInputChange" :inputValue="newUser.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" />

                <input :disabled="loading" type="submit" value="Create User" class="mt-2 md:mt-4 bg-custom-blue text-white font-medium py-2 px-6 border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop cursor-pointer">
            </div>
        </form>
    </div>

    <!-- Update User Popup -->
    <div v-if="update" class="w-screen h-screen md:mt-[-96px] grid bg-[#3F3F3F] bg-opacity-[26%] z-50 fixed">
        <form @submit.prevent="userUpdate()" class="m-auto w-[343px] md:w-[438px] h-fit grid bg-white rounded-[4px] md:rounded-[6px]">
            <div class="w-full h-[56px] md:h-[68px] flow-root px-6 text-[20px] md:text-[24px] text-custom-gray font-medium bg-custom-bg border-b-[1px] border-custom-light rounded-tr-[4px] rounded-tl-[4px] md:rounded-tr-[6px] md:rounded-tl-[6px]">
                <div class="grid w-fit h-full float-left"><p class="my-auto">Updates User</p></div>
                <button @click="update = false" class="grid w-fit h-full float-right"><XCircleIcon class="h-[24px] my-auto" /></button>
            </div>

            <div class="w-full h-fit grid gap-4 px-6 my-6">
                <textInput @inputUpdate="newInputChange" :inputValue="updateUser.name" :id="'name'" :label="'Name'" />
                <textInput @inputUpdate="newInputChange" :inputValue="updateUser.email" :id="'email'" :label="'Email'" :email=true />

                <div class="grid gap-[4px] w-full h-fit">
                    <label for="role" class="text-[16px] text-custom-light-gray font-medium">Role <span class="text-custom-red">*</span></label>
                    <VueMultiselect
                        v-model="updateUser.role"
                        :options="roles"
                        :searchable="false"
                        :close-on-select="true"
                        :allow-empty="false"
                        :show-labels="false"
                        label="name"
                        :track-by="code"
                        :block-keys="['Tab', 'Enter']"
                        placeholder="Select user role"
                    />
                </div>

                <div class="grid gap-[4px] w-full h-fit">
                    <label for="role" class="text-[16px] text-custom-light-gray font-medium">Error Source <span class="text-custom-red">*</span></label>
                    <VueMultiselect
                        v-model="updateUser.source"
                        :options="sources"
                        :searchable="false"
                        :close-on-select="true"
                        :allow-empty="false"
                        :show-labels="false"
                        label="name"
                        :track-by="code"
                        :block-keys="['Tab', 'Enter']"
                        placeholder="Select allowed error source"
                    />
                </div>

                <div class="grid gap-[4px] w-full h-fit">
                    <label for="role" class="text-[16px] text-custom-light-gray font-medium">Error Type <span class="text-custom-red">*</span></label>
                    <VueMultiselect
                        v-model="updateUser.type"
                        :options="types"
                        :searchable="false"
                        :close-on-select="true"
                        :allow-empty="false"
                        :show-labels="false"
                        label="name"
                        :track-by="code"
                        :block-keys="['Tab', 'Enter']"
                        placeholder="Select allowed error types"
                    />
                </div>
                
                <textInput @inputUpdate="newInputChange" :inputValue="updateUser.password" :id="'password'" :label="'Password'" />
                <textInput @inputUpdate="newInputChange" :inputValue="updateUser.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" />

                <input :disabled="loading" type="submit" value="Update User" class="mt-2 md:mt-4 bg-custom-blue text-white font-medium py-2 px-6 border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop cursor-pointer">
            </div>
        </form>
    </div>

    <div class="grid gap-6 sm:ml-64 px-4 mt-24 md:mt-0 md:px-10 h-fit w-full md:w-[84%] bg-custom-bg z-10 absolute">
        <div class="w-full h-fit grid gap-4 md:gap-0 md:flow-root">
            <div class="w-fit float-left grid">
                <p class="text-[22px] md:text-[32px] text-custom-gray font-medium">Users</p>
                <p class="text-[14px] md:text-[16px] text-custom-light-gray">Below you can find the full list of users,</p>
            </div>

            <div class="float-right grid md:flex gap-6">
                <!-- Search Bar -->
                <div class="md:float-right w-full md:w-[300px] h-[48px] flex bg-white border-custom-light border-[1px] rounded-[4px] px-4">
                    <MagnifyingGlassIcon class="h-[24px] my-auto" />
                    <input @input="filterUsers()" v-model="filter" type="text" class="w-full text-custom-gray border-none focus:border-none focus:ring-0">
                </div>

                <!-- Create User -->
                <button @click="create = true" :disabled="loading || create" class="w-[199px] h-[48px] flex gap-4 bg-custom-blue disabled:opacity-50 text-white font-medium px-6 border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop disabled:cursor-not-allowed">
                    <PlusIcon class="h-[24px] my-auto" />
                    <p class="my-auto text-[18px] font-medium">Create User</p>
                </button>
            </div>
        </div>

        <!-- Desktop Headers -->
        <div class="hidden w-full h-fit md:grid grid-cols-7 px-6 pb-[5px] border-b-[1px] border-custom-light text-[16px] text-custom-light-gray font-medium">
            <p>Name</p>
            <p>Email</p>
            <p>Role</p>
            <p class="col-span-2">Error Source</p>
            <p class="ml-[-10px]">Error Type</p>
        </div>

        <div class="w-full h-[568px] md:h-[600px] grid mb-12 md:mb-0 bg-transparent overflow-y-scroll scrollbar">
            <div class="h-fit grid gap-4">
                <!-- Desktop View -->
                <div v-for="(user, index) in filterView" :key="index" class="hidden w-full h-[48px] md:grid grid-cols-7 px-6 text-[14px] text-custom-gray bg-white border-[1px] border-cusom-light rounded-[4px]">
                    <p class="my-auto mr-4 truncate">{{ user.name }}</p>
                    <p class="my-auto mr-4 truncate">{{ user.email }}</p>
                    <div class="w-fit h-[28px] grid my-auto px-4 bg-custom-dark-blue rounded-[2px]"><p class="m-auto text-[14px] text-white font-medium">{{ user.role }}</p></div>
                    <p :class="user.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="col-span-2 my-auto mr-4  font-medium truncate">{{ user.source == 'ALL' ? 'RocketAutomation, RocketMGA' : user.source == 'MGA' ? 'RocketMGA' : 'RocketAutomation'}}</p>
                    <p class="my-auto mr-4 font-medium truncate">{{ user.type == "ALL" ? "API, BOT" : user.type }}</p>
                    <button :disabled="update" @click="showUpdate(user)" class="w-fit h-fit grid my-auto bg-custom-blue disabled:opacity-50 text-white font-medium py-[4px] px-6 border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop disabled:cursor-not-allowed">Update User</button>
                </div>

                <!-- Mobile View -->
                <div v-for="(user, index) in filterView" :key="index" class="md:hidden w-full h-fit p-2 grid grid-cols-3 bg-white border-[1px] border-custom-light rounded-[2px]">
                    <div class="grid gap-2 text-[14px] text-custom-light-gray">
                        <p class="my-auto">Name</p>
                        <p class="my-auto">Email</p>
                        <p class="my-auto">Role</p>
                        <p :class="user.source == 'ALL' ? 'mt-[8px]' : ''" class="my-auto">Error Source</p>
                        <p class="my-auto">Error Type</p>
                    </div>

                    <div class="grid col-span-2 gap-2 text-[14px] text-custom-gray font-medium">
                        <p class="my-auto">{{ user.name }}</p>
                        <p class="my-auto">{{ user.email }}</p>
                        <div class="grid w-fit my-auto px-4 py-[4px] bg-custom-dark-blue rounded-[2px]"><p class="m-auto text-white">{{ user.role }}</p></div>
                        <p :class="user.source == 'ALL' ? 'text-[13px]' : ''" class="my-auto">{{ user.source == 'ALL' ? 'RocketAutomation, RocketMGA' : user.source == "MGA" ? 'RocketMGA' : 'RocketAutomation' }}</p>
                        <p class="my-auto truncate">{{ user.type == "ALL" ? 'API, BOT' : user.type}}</p>
                    </div>

                    <button :disabled="update" @click="showUpdate(user)" class="w-full h-fit grid col-span-3 mt-4 text-[14px] bg-custom-blue disabled:opacity-50 text-white font-medium py-[4px] px-6 border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop disabled:cursor-not-allowed">Update User</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NavBar from '../components/navbar.vue'
import textInput from '../components/textInput.vue'
import profileMenu from '../components/profileMenu.vue'
import loading from '../components/loading.vue'

import { PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
import { XCircleIcon } from '@heroicons/vue/24/outline'

import VueMultiselect from 'vue-multiselect'

export default{
    name: "Users",
    data(){
        return {
            create: false,
            update: false,
            loading: false,
            roles: [
                {
                    code: 'user',
                    name: 'User'
                },
                {
                    code: 'admin',
                    name: 'Admin'
                },
                {
                    code: 'super-admin',
                    name: 'Super Admin'
                }
            ],
            sources: [
                {
                    name: "RocketAutomation, RocketMGA",
                    code: "ALL"
                },
                {
                    name: "RocketMGA",
                    code: "MGA"
                },
                {
                    name: "RocketAutomation",
                    code: "RA"
                }
            ],
            types: [
                {
                    name: "API, BOT",
                    code: "ALL"
                },
                {
                    name: "API",
                    code: "API"
                },
                {
                    name: "BOT",
                    code: "BOT"
                }
            ],
            users : [],
            filterView: [],
            newUser: {
                role: '',
                name: '',
                email: '',
                password: '',
                confirm_password: '',
                source: '',
                type: ''
            },
            newErrors: [
                {
                    name: 'name',
                    errors: []
                },
                {
                    name: 'email',
                    errors: []
                },
                {
                    name: 'password',
                    errors: []
                },
                {
                    name: 'confirm_password',
                    errors: []
                }
            ],
            updateUser: {
                id: '',
                role: '',
                name: '',
                email: '',
                source: '',
                type: '',
                password: '',
                confirm_password: ''
            },
            updateErrors: [
                {
                    name: 'name',
                    errors: []
                },
                {
                    name: 'email',
                    errors: []
                }
            ]
        }
    },
    async created(){
        await axios.get('/api/users')
        .then(response => {
            this.users = response.data.users
            this.filterView = this.users
        })
    },
    methods: {
        async filterUsers(){
            this.filterView = []

            this.users.forEach(user => {
                if(user.name.toLowerCase().includes(this.filter.toLowerCase()) || user.email.toLowerCase().includes(this.filter.toLowerCase())){
                    this.filterView.push(user)
                }
            })
        },
        async showUpdate(user){
            const keys = Object.keys(this.updateUser)

            keys.forEach(key => {
                if(key != 'role' && key != 'source' && key != 'type'){
                    this.updateUser[key] = user[key]
                }
            })

            this.roles.forEach(role => {
                if(role.code == user.role){
                    this.updateUser.role = role
                }
            })

            this.sources.forEach(source => {
                if(source.code == user.source){
                    this.updateUser.source = source
                }
            })

            this.types.forEach(type => {
                if(type.code == user.type){
                    this.updateUser.type = type
                }
            })

            this.update = true
        },
        newInputChange(id, value, errors){
            this.newUser[id] = value
            
            if(errors){
                this.newErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.newErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        updateInputChange(id, value, errors){
            this.updateUser[id] = value
            
            if(errors){
                this.updateErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.updateErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        async createUser(){
            this.loading = true
            let valid = true

            this.newErrors.forEach(item => {
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

            const keys = Object.keys(this.newUser)

            keys.forEach(key => {
                if(this.newUser[key] == ''){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            let userCreated = false

            if(valid){
                this.newUser.role = this.newUser.role.code
                this.newUser.source = this.newUser.source.code
                this.newUser.type = this.newUser.type.code

                await axios.post('/api/register', this.newUser)
                .then(response => {
                    if(response.data.success){
                        this.$alert({
                            title: 'User Registration',
                            text: response.data.message,
                            type: 'success'
                        })

                        this.create = false

                        userCreated = true
                    }
                })
                .catch(error => {
                    if(error.response.status == 400){
                        const keys = Object.keys(error.response.data.message)

                        keys.forEach(key => {
                            error.response.data.message[key].forEach(error => {
                                this.$alert({
                                    title: 'Registration Error',
                                    text: error,
                                    type: 'error'
                                })
                            })
                        })

                    } else if(error.response.status == 401){
                        this.$alert({
                            title: 'Registration Error',
                            text: error.response.data.message,
                            type: 'error'
                        })
                    }
                })

                keys.forEach(key => {
                    this.newUser[key] = ''
                })
            }

            if(userCreated){
                this.users = []

                await axios.get('/api/users')
                .then(response => {
                    const keys = Object.keys(response.data.users)

                    keys.forEach(key => {
                        this.users.push(response.data.users[key])
                    })
                })

                this.filterView = this.users
            }
            
            this.loading = false
        },
        async userUpdate(){
            this.loading = true
            let valid = true

            this.updateErrors.forEach(item => {
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

            const keys = Object.keys(this.updateUser)

            keys.forEach(key => {
                if(this.updateUser[key] == '' && key != 'password' && key != 'confirm_password'){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            let userUpdated = false

            if(valid){
                this.updateUser.role = this.updateUser.role.code
                this.updateUser.source = this.updateUser.source.code
                this.updateUser.type = this.updateUser.type.code

                let data = {}

                if(this.updateUser.password == ''){
                    data.id = this.updateUser.id
                    data.role = this.updateUser.role
                    data.name = this.updateUser.name
                    data.email = this.updateUser.email
                } else {
                    data = this.updateUser
                }

                await axios.post('/api/update', data)
                .then(response => {
                    if(response.data.success){
                        this.$alert({
                            title: 'User Update',
                            text: response.data.message,
                            type: 'success'
                        })

                        this.update = false

                        userUpdated = true
                    }
                })
                .catch(error => {
                    if(error.response.status == 400){
                        const keys = Object.keys(error.response.data.message)

                        keys.forEach(key => {
                            error.response.data.message[key].forEach(error => {
                                this.$alert({
                                    title: 'Registration Error',
                                    text: error,
                                    type: 'error'
                                })
                            })
                        })
                    }
                })
            }

            if(userUpdated){
                keys.forEach(key => {
                    this.updateUser[key] = ''
                })

                this.users = []

                await axios.get('/api/users')
                .then(response => {
                    const keys = Object.keys(response.data.users)

                    keys.forEach(key => {
                        this.users.push(response.data.users[key])
                    })
                })

                this.filterView = this.users
            }

            this.loading = false
        }
    },
    components: {
        NavBar,
        profileMenu,
        loading,
        textInput,
        VueMultiselect,
        PlusIcon,
        MagnifyingGlassIcon,
        XCircleIcon
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style>
.multiselect__option--highlight {
    background: #5080C7;
    outline: none;
    color: white;
}

.multiselect__tags {
    min-height: 40px;
    display: block;
    padding: 8px 40px 0 8px;
    border-radius: 2px;
    border: 1px solid #DEDEDE;
    background: #fff;
    font-size: 14px;
}
</style>