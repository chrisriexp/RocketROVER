<template>
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full pt-8 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0" aria-label="Sidebar">
        <div class="h-screen p-4 pb-4 overflow-y-auto bg-white">
            <div class="grid gap-2 h-fit">
                <img src="../../assets/logo.png" alt="Rocket Automation Logo" class="pb-4 border-b-2 mb-6">
                <router-link :to="{name: 'Profile'}" :class="active == 'profile' ? 'text-white bg-custom-blue' : ''" class="flex md:hidden gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-blue"><UserCircleIcon class="h-6 my-auto" />Profile</router-link>
                <router-link v-if="role == 'super-admin'" :to="{name: 'Users'}" :class="active == 'users' ? 'text-white bg-custom-blue' : ''" class="flex md:hidden gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-blue"><AdjustmentsHorizontalIcon class="h-6 my-auto" />Users</router-link>
                <router-link :to="{name: 'Dashboard'}" :class="active == 'dashboard' ? 'text-white bg-custom-blue' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-blue"><Squares2X2Icon class="h-6 my-auto" />Dashboard</router-link>
                <router-link :to="{name: 'Tasks'}" :class="active == 'tasks' ? 'text-white bg-custom-blue' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-blue"><ClipboardDocumentListIcon class="h-6 my-auto" />Tasks</router-link>
                <button @click="logout" class="flex md:hidden gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-blue"><ArrowLeftOnRectangleIcon class="h-6 my-auto" />Sign Out</button>             
            </div>

            <div class="bottom-2 fixed text-custom-gray text-sm mx-auto w-fit pl-4">
                <p>© 2023 <a target="_blank" href="https://rocketmga.com">ROCKETFLOOD.COM</a></p>
            </div>
        </div>
    </aside>

    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="fixed z-30 w-full bg-white flex items-center p-4 text-sm text-custom-gray border-b-[1px] border-custom-light md:hidden">
        <div class="flow-root w-fit">
            <Bars3Icon aria-hidden="true" class="h-10 float-left" />
        </div>

        <div class="w-full grid justify-items-center">
            <p v-if="active == 'profile'" class="text-[24px] font-medium">Profile</p>
            <p v-if="active == 'users'" class="text-[24px] font-medium">Users</p>
            <p v-else-if="active == 'dashboard'" class="text-[24px] font-medium">Dashboard</p>
            <p v-else-if="active == 'tasks'" class="text-[24px] font-medium">Tasks</p>
        </div>

        <div class="flow-root w-fit">
            <div class="grid h-[40px] w-[40px] bg-[#F5F06E] rounded-full float-right mr-4">
                <p class="text-center h-fit my-auto text-[18px] text-[#3F3F3F] font-medium">{{ initals }}</p>
            </div>
        </div>
    </button>
</template>

<script>
import { HomeIcon, ArrowLeftOnRectangleIcon, UserCircleIcon, Squares2X2Icon, Bars3Icon, ClipboardDocumentListIcon, AdjustmentsHorizontalIcon } from '@heroicons/vue/24/outline'

export default {
    name: "Nav Bar",
    data(){
        return{
            initals: ''
        }
    },
    async created(){
        await axios.get('/api/user')
        .then(response => {
            const names = response.data.name.split(" ")
            const i1 = Array.from(names[0])[0]
            const i2 = Array.from(names[1])[0]
            this.initals = i1+i2
        })
    },
    methods: {
        async logout(){
            await axios.get('/api/logout')
            .then(response => {
                localStorage.removeItem('token')
                this.$router.push({name: 'Home'})
                this.$alert({
                    title: 'Logout',
                    text: response.data.message,
                    type: 'success'
                })
            })
        }
    },
    props: {
        active: String,
        role: String
    },
    components: {
        HomeIcon,
        UserCircleIcon,
        Squares2X2Icon,
        ArrowLeftOnRectangleIcon,
        Bars3Icon,
        ClipboardDocumentListIcon,
        AdjustmentsHorizontalIcon
    }
}
</script>