<template>
    <NavBar :active="'tasks'" :role="role" class="z-0 absolute" />

    <profileMenu class="z-20 absolute" />

    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <div class="sm:ml-64 mt-4 md:mt-[72px] px-4 md:px-10 h-fit w-full md:w-[84%] bg-custom-bg z-10 absolute">
        <div class="w-full grid mt-6 md:flow-root">
            <!-- Header -->
            <div class="md:float-left w-fit grid">
                <h1 class="text-[32px] text-custom-gray font-medium">Tasks</h1>
                <p class="text-[16px] text-custom-light-gray">Below is a task list of application errors,</p>
            </div>

            <!-- Search Bar -->
            <div class="md:float-right w-full md:w-[300px] h-[48px] flex bg-white border-custom-light border-[1px] rounded-[4px] px-4">
                <MagnifyingGlassIcon class="h-[24px] my-auto" />
                <input @input="filterTasks()" v-model="filter" type="text" class="w-full text-custom-gray border-none focus:border-none focus:ring-0">
            </div>
        </div>

        <!-- Task Status Selection -->
        <div class="w-full h-fit md:h-[48px] grid md:grid-cols-4 gap-[4px] mt-4 md:mt-8 p-[4px] bg-custom-light rounded-[4px] text-center text-[14px] font-medium">
            <div @click="changeView('debug')" :class="view == 'debug'? 'bg-custom-blue text-white' : 'bg-white text-custom-gray'" class="grid h-[40px] rounded-[4px] cursor-pointer">
                <p class="m-auto">Debugging</p>
            </div>

            <div @click="changeView('update')" :class="view == 'update'? 'bg-custom-blue text-white' : 'bg-white text-custom-gray'" class="grid h-[40px] rounded-[4px] cursor-pointer">
                <p class="m-auto">Updates in Progress</p>
            </div>

            <div @click="changeView('test')" :class="view == 'test'? 'bg-custom-blue text-white' : 'bg-white text-custom-gray'" class="grid h-[40px] rounded-[4px] cursor-pointer">
                <p class="m-auto">Testing</p>
            </div>

            <div @click="changeView('fixed')" :class="view == 'fixed'? 'bg-custom-blue text-white' : 'bg-white text-custom-gray'" class="grid h-[40px] rounded-[4px] cursor-pointer">
                <p class="m-auto">Fixed</p>
            </div>
        </div>

        <normal v-if="view == 'debug' || view == 'update' || view == 'fixed' && setup" :tasks="filterView" />
        <test v-else-if="view == 'test' && setup" :tasks="filterView" />
    </div>
</template>
    
<script>
import NavBar from '../components/navbar.vue'
import profileMenu from '../components/profileMenu.vue'
import loading from '../components/loading.vue'

import normal from '../components/tasks/normal.vue'
import test from '../components/tasks/test.vue'

import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import moment from 'moment'

export default {
    name: "Dashboard",
    data(){
        return {
            setup: false,
            role: "",
            loading: true,
            view: "",
            tasks: [],
            filter: "",
            filterView: []
        }
    },
    async created(){
        this.role = this.$route.meta.role
        this.type = this.$route.meta.type
        this.view = 'debug'

        await axios.get('/api/tasks/'+this.view)
        .then(response => {
            let taskDates = []

            response.data.tasks.forEach(task => {
                if(!taskDates.includes(task.created_at.slice(0, 10))){
                    taskDates.push(task.created_at.slice(0, 10))
                }
            })

            taskDates.forEach(date => {
                let dateTasks = []

                response.data.tasks.forEach(task => {
                    if(task.created_at.includes(date)){
                        dateTasks.push(task)
                    }
                })

                this.tasks.push({
                    "date": moment(date).format('MM/DD/YYYY'),
                    "tasks": dateTasks
                })
            })
        })

        this.filterView = this.tasks

        this.setup = true
        this.loading = false
    },
    methods: {
        async filterTasks(){
            this.filterView = []

            this.tasks.forEach(date => {
                let dateTasks = []

                date.tasks.forEach(task => {
                    if(task.app_id.toLowerCase().includes(this.filter.toLowerCase())){
                        dateTasks.push(task)
                    }
                })

                if(dateTasks.length > 0){
                    this.filterView.push({
                        "date": date.date,
                        "tasks": dateTasks
                    })
                }
            })
        },
        async changeView(view){
            this.loading = true
            this.view = view
            this.tasks = []

            await axios.get('/api/tasks/'+view)
            .then(response => {
                let taskDates = []

                response.data.tasks.forEach(task => {
                    if(!taskDates.includes(task.created_at.slice(0, 10))){
                        taskDates.push(task.created_at.slice(0, 10))
                    }
                })

                taskDates.forEach(date => {
                    let dateTasks = []

                    response.data.tasks.forEach(task => {
                        if(task.created_at.includes(date)){
                            dateTasks.push(task)
                        }
                    })

                    this.tasks.push({
                        "date": moment(date).format('MM/DD/YYYY'),
                        "tasks": dateTasks
                    })
                })
            })

            this.filterView = this.tasks
            this.loading = false
        }
    },
    components: {
        NavBar,
        profileMenu,
        loading,
        normal,
        test,
        MagnifyingGlassIcon
    }
}
</script>