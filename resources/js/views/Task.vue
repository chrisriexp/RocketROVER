<template>
    <NavBar :active="''" :role="role" class="z-0 absolute" />

    <profileMenu class="z-20 absolute" />

    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>
    
    <div class="sm:ml-64 mt-4 md:mt-[90px] px-4 md:px-10 h-fit w-full md:w-[84%] bg-custom-bg z-10 absolute">
        <!-- Breadcrumb Menu -->
        <div class="flex gap-2 w-fit h-fit">
            <router-link :to="{name: 'Tasks'}" class="grid my-auto w-[24px] h-[24px] bg-white rounded-[2px] shadow-newdrop"><ArrowLeftIcon class="h-[18px] text-custom-blue m-auto" /></router-link>
            <p class="my-auto text-[14px] text-custom-gray font-medium">Task/<span class="text-custom-blue">{{ app_id }}</span></p>
        </div>

        <!-- Header -->
        <div class="w-full h-fit mt-4 flow-root">
            <div class="w-fit h-fit grid float-left">
                <p class="w-fit text-[32px] text-custom-gray font-medium">{{ app_id }} - <span :class="source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'">{{ source == 'MGA' ? 'RocketMGA' : 'RocketAutomation' }}</span></p>
                <p class="w-fit text-[16px] text-custom-light-gray">Here you can see details regarding {{ carriers[errors[selected_error].carrier].name }}'s error on application {{ app_id }},</p>
            </div>
            <div class="w-fit h-fit flex gap-8 float-right">
                <div class="grid">
                    <p class="text-[16px] text-custom-light-gray font-medium">Created:</p>
                    <p class="text-[14px] text-custom-gray">{{ moment(errors[selected_error].created_at).format('dddd, MMMM Do YYYY hh:mm A') }}</p>
                </div>

                <div v-if="errors[selected_error].fixed" class="grid">
                    <p class="text-[16px] text-custom-light-gray font-medium">Fixed:</p>
                    <p class="text-[14px] text-custom-gray">{{ moment(errors[selected_error].fixed_date).format('dddd, MMMM Do YYYY hh:mm A') }}</p>
                </div>
            </div>
        </div>

        <!-- Error Carrier, Type, Assigned to, Status -->
        <div class="w-full h-fit flex gap-6 mt-6">
            <!-- Carrier -->
            <div class="w-fit h-fit grid my-auto">
                <p class="text-[16px] text-custom-light-gray font-medium">Carrier Name</p>
                <div class="w-[213px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                    <p class="my-auto text-[15px] text-custom-gray font-medium">{{ carriers[errors[selected_error].carrier].name }}</p>
                </div>
            </div>

            <!-- Type -->
            <div class="w-fit h-fit grid my-auto">
                <p class="text-[16px] text-custom-light-gray font-medium">Error Type</p>
                <div class="w-[213px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                    <p :class="errors[selected_error].type == 'API' ? 'text-custom-blue' : 'text-custom-gray'" class="my-auto text-[15px] font-medium">{{ errors[selected_error].type }}</p>
                </div>
            </div>

            <!-- Assigned -->
            <div class="w-fit h-fit grid my-auto">
                <p class="text-[16px] text-custom-light-gray font-medium">Assigned To</p>
                <div class="w-[213px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                    <p class="my-auto text-[15px] text-custom-gray font-medium">{{ errors[selected_error].assigned }}</p>
                </div>
            </div>

            <!-- Status -->
            <img :src="`/images/${errors[selected_error].status}.png`" alt="Error Status Bar" class="h-[47px] my-auto">
        </div>

        <div class="w-full h-fit grid grid-cols-4 gap-12 mt-8">
            <!-- Error List -->
            <div class="w-full h-fit grid gap-4">
                <!-- Fixed Errors Count -->
                <p class="text-[16px] text-custom-gray font-medium">Errors Fixed {{ errors_fixed }}/<span class="text-custom-red">{{ errors.length }}</span></p>
                <div class="w-full h-[400px] overflow-y-scroll scrollbar">
                    <div class="w-full h-fit grid gap-4">
                        <!-- Errors -->
                        <div @click="selectError(index, error.carrier)" v-for="(error, index) in errors" :key="index" :class="error.fixed ? 'border-custom-green' : 'border-custom-light' && selected_error == index ? 'inner-border-2' : ''" class="w-full h-[60px] grid px-4 text-[14px] text-custom-gray bg-white border-[1px] rounded-[2px] cursor-pointer hover:inner-border-2">
                            <div class="w-full h-fit my-auto grid">
                                <p class="w-full flow-root font-medium"><span class="float-left">{{ carriers[error.carrier].name }}</span> <span :class="error.type == 'API' ? 'text-custom-blue' : 'text-custom-light-gray'" class="float-right">{{ error.type }}</span></p>
                                <p class="text-custom-light-gray truncate">{{ error.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Details -->
            <div class="grid col-span-3 gap-4 h-fit mb-8 p-6 bg-white border-[1px] border-custom-light rounded-[4px]">
                <div class="w-full h-fit flow-root">
                    <p class="text-[20px] text-custom-gray font-semibold float-left">Error Description and Notes</p>

                    <div class="w-fit h-fit flex gap-6 float-right">
                        <div class="">
                            <!-- Update Error Status -->
                            <button @click="updateStatus()" v-if="!errors[selected_error].fixed" class="text-[18px] text-white px-4 bg-custom-blue border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop active:shadow-none float-right">{{ errors[selected_error].status == 'debug' ? 'Ready for Updates' : '' || errors[selected_error].status == 'update' ? 'Ready to Test' : '' || errors[selected_error].status == 'test' ? 'Error Fixed' : ''}}</button>
                        </div>
                    </div>
                </div>

                <!-- Error Test -->
                <div v-if="errors[selected_error].status == 'test'" class="w-fit flex gap-6">
                    <p class="my-auto text-[16px] text-custom-light-gray">Completed Tests:</p>
                    <img :src="`/images/${errors[selected_error].tests}.png`" alt="Test Progress Bar" class="h-[16px] my-auto">
                    <button v-if="errors[selected_error].tests < 3" @click="logTest()" class="my-auto text-[12px] text-white font-medium bg-custom-blue px-2 rounded-[2px]">Log Test</button>
                </div>

                <!-- Backend URL -->
                <a v-if="carriers[errors[selected_error].carrier].backend[source]" target="_blank" :href="carriers[errors[selected_error].carrier].backend[source]+app_id" class="text-[16px] text-custom-blue">Backend Debugging URL</a>
                <p v-else class="text-[16px] text-custom-orange">{{ carriers[errors[selected_error].carrier].name }} does not have a backend debugging URL</p>

                <!-- Error Desc when reported -->
                <div>
                    <p class="text-[16px] text-custom-light-gray font-medium">Error Description</p>
                    <textarea disabled v-model="errors[selected_error].desc" name="errorDesc" id="errorDesc" class="w-full h-[75px] text-[14px] text-custom-light-gray border-custom-light bg-custom-bg rounded-[2px]"></textarea>
                </div>

                <hr>
                
                <!-- Error Notes -->
                <div class="w-full h-fit grid gap-4">
                    <div class="w-full h-fit text-[18px] flow-root">
                        <p class="text-custom-light-gray font-medium float-left">Error Notes</p>
                        <!-- Save Notes -->
                        <button @click="saveNotes()" class="text-white px-4 bg-custom-blue border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop active:shadow-none float-right">Save Notes</button>
                    </div>

                    <div class="w-full h-fit grid grid-cols-3 gap-8">
                        <div class="grid gap-4">
                            <!-- Add Note -->
                            <div class="grid gap-2 h-fit text-[16px]">
                                <button @click="addNote()" class="w-fit px-4 text-white bg-custom-gray border-l-[4px] border-b-[5px] border-[#808080] active:border-custom-gray rounded-[2px] shadow-newdrop active:shadow-none">Create Note</button>
                            </div>

                            <div class="grid h-[300px] overflow-y-scroll scrollbar">
                                <div class="grid w-full h-fit gap-4">
                                    <!-- List of Notes -->
                                    <div @click="selected_note = index" v-for="(note, index) in error_notes" :key="index" :class="selected_note == index ? 'inner-border-2' : ''" class="w-full h-fit grid p-2 text-[14px] text-custom-light-gray border-[1px] border-custom-light rounded-[2px] cursor-pointer">
                                        <p class="italic">{{ !note.id ? note.created_at : moment(note.created_at).format('MM/DD/YYYY hh:mm A') }}</p>
                                        <p class="font-medium">{{ note.user }}</p>
                                        <p class="truncate">{{ note.note }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="error_notes.length > 0" class="col-span-2 grid gap-4 h-fit">
                            <!-- Note Text -->
                            <textarea :disabled="error_notes[selected_note].user != user ? true : false || error_notes[selected_note].disabled"  v-model="error_notes[selected_note].note" name="note" id="note" class="h-[150px] text-custom-gray border-[2px] border-custom-light rounded-[4px] focus:ring-0 focus:border-custom-light disabled:bg-custom-bg"></textarea>
                            <!-- Delete Note -->
                            <button @click="deleteNote(selected_note)" v-if="error_notes[selected_note].user == user && !error_notes[selected_note].test && !error_notes[selected_note].disabled" class="flex gap-4 w-fit h-[40px] px-6 group text-[16px] text-custom-gray font-medium border-[1px] border-custom-gray rounded-[2px] hover:text-custom-red hover:border-custom-red">
                                <TrashIcon class="my-auto h-[24px]" />
                                <p class="my-auto">Delete Note</p>
                            </button>
                        </div>
                        <!-- No Notes Disclaimer -->
                        <div v-else class="col-span-2 grid h-fit">
                            <p class="italic text-[16px] text-custom-light-gray font-medium">There are currently no notes for this error, please create a note to display more information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NavBar from '../components/navbar.vue'
import profileMenu from '../components/profileMenu.vue'
import loading from '../components/loading.vue'

import { ArrowLeftIcon, TrashIcon } from '@heroicons/vue/24/outline'
import moment from 'moment'
import carriers from '../../assets/carriers.json'

export default {
    name: "Task View",
    data(){
        return {
            user: "",
            role: "",
            type: "",
            loading: true,
            app_id: "",
            source: "",
            selected_error: 0,
            errors_fixed: 0,
            errors: [],
            carriers: carriers,
            selected_note: 0,
            error_notes: []
        }
    },
    async created(){
        this.moment = moment
        this.role = this.$route.meta.role
        this.type = this.$route.meta.type
        this.app_id = this.$route.params.app_id

        await axios.get('/api/user')
        .then(response => {
            this.user = response.data.name
        })

        await axios.get('/api/task/'+this.app_id)
        .then(response => {
            this.errors = response.data.errors
            this.source = response.data.errors[0].source

            let i = 0
            response.data.errors.forEach(error => {
                if(error.fixed){
                    this.errors_fixed += 1
                }

                if(error.carrier == this.$route.params.carrier){
                    this.selected_error = i
                } else {
                    i += 1
                }
            })
        })

        await axios.get('/api/notes/'+this.app_id+'/'+this.errors[this.selected_error].carrier)
        .then(response => {
            this.error_notes = response.data.notes
        })

        this.loading = false
    },
    methods: {
        async selectError(index, name){
            this.loading = true

            this.selected_error = index
            this.$router.push({name: 'Task', params: {app_id: this.app_id, carrier: name}})

            await axios.get('/api/notes/'+this.app_id+'/'+this.errors[this.selected_error].carrier)
            .then(response => {
                this.error_notes = response.data.notes
            })

            this.selected_note = 0

            this.loading = false
        },
        async addNote(){
            this.loading = true

            this.error_notes.push({
                id: "",
                app_id: this.app_id,
                carrier: this.errors[this.selected_error].carrier,
                user: this.user,
                note: "",
                created_at: moment(new Date).format('MM/DD/YYYY hh:mm A'),
                test: false,
                disabled: false
            })

            const index = this.error_notes.length - 1

            this.selected_note = index

            this.loading = false
            console.log(this.error_notes[this.selected_note])
        },
        async deleteNote(index){
            this.loading = true

            if(this.error_notes[index].id){
                await axios.delete('/api/note/'+this.error_notes[index].id)
                .then(response => {
                    this.$alert({
                        title: 'Note Deleted',
                        text: 'The note has been deleted successfully.',
                        type: 'success'
                    })
                })
            }

            this.error_notes.splice(index, 1)

            this.loading = false
        },
        async saveNotes(){
            this.loading = true

            for(const note of this.error_notes.reverse()){
                if(note.id){
                    await axios.put('/api/note/'+note.id, note)
                } else {
                    await axios.post('/api/note', note)
                }
            }

            await axios.get('/api/notes/'+this.app_id+'/'+this.errors[this.selected_error].carrier)
            .then(response => {
                this.error_notes = response.data.notes
            })

            this.selected_note = 0

            this.loading = false
        },
        async logTest(){
            this.loading = true

            await axios.post('/api/test/'+this.errors[this.selected_error].id)
            .then(response => {
                location.reload()
            })
        },
        async updateStatus(){
            await axios.post('/api/status/'+this.errors[this.selected_error].id)
            .then(response => {
                location.reload()
            })
        }
    },
    components: {
        NavBar,
        profileMenu,
        loading,
        ArrowLeftIcon,
        TrashIcon
    }
}
</script>