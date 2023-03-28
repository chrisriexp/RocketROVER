<template>
    <div class="hidden w-full md:flow-root p-6">
        <div @click="menu = !menu" class="float-right w-fit h-fit flex gap-4 cursor-pointer">
            <div class="grid h-fit">
                <p class="text-[16px] text-custom-gray font-medium">{{ name }}</p>
                <p class="text-[16px] text-custom-blue text-right">{{ role }}</p>
            </div>
            
            <div class="grid h-[48px] w-[48px] bg-[#F5F06E] rounded-full">
                <p class="text-[20px] text-custom-gray font-medium mx-auto my-auto">{{ initals }}</p>
            </div>
        </div>

        <div v-if="menu" class="z-10 absolute grid w-fit h-fit right-6 mt-16 bg-white rounded-[4px] border-custom-light border-[1px] text-custom-gray">
            <router-link :to="{name: 'Dashboard'}" class="w-[205px] h-[40px] flex gap-4 px-4 border-b-[1px] rounded-tr-[4px] rounded-tl-[4px] hover:bg-custom-blue hover:text-white">
                <UserIcon class="h-[24px] my-auto" />
                <p class="text-[16px] my-auto">Profile</p>
            </router-link>

            <router-link v-if="role == 'super-admin'" :to="{name: 'AdminUsers'}" class="w-[205px] h-[40px] flex gap-4 px-4 border-b-[1px] hover:bg-custom-blue hover:text-white">
                <AdjustmentsHorizontalIcon class="h-[24px] my-auto" />
                <p class="text-[16px] my-auto">Users</p>
            </router-link>

            <div @click="logout()" class="w-[205px] h-[40px] flex gap-4 px-4 cursor-pointer hover:bg-custom-blue hover:text-white rounded-br-[4px] rounded-bl-[4px]">
                <ArrowLeftOnRectangleIcon class="h-[24px] my-auto" />
                <p class="text-[16px] my-auto">Logout</p>
            </div>
        </div>
    </div>
</template>

<script>
import { UserIcon, ArrowLeftOnRectangleIcon, AdjustmentsHorizontalIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Profile Menu",
    data(){
        return {
            menu: false,
            initals: '',
            role: '',
            name: ''
        }
    },
    async created(){
        this.role = this.$route.meta.role

        await axios.get('/api/user')
        .then(response => {
            this.name = response.data.name
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
    components: {
        UserIcon,
        ArrowLeftOnRectangleIcon,
        AdjustmentsHorizontalIcon
    }
}
</script>