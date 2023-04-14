<template>
    <NavBar :active="''" :role="role" class="z-0 absolute" />

    <profileMenu class="z-20 absolute" />

    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>
    
    <!-- Log Test Popup -->
    <div id="update" :class="logTest ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <div class="w-[576px] h-fit grid m-auto p-6 bg-white rounded-[8px] shadow-newdrop">
            <div class="w-full h-fit flow-root">
                <p class="float-left text-[24px] text-custom-gray font-semibold">Log Error Test</p>
                <XCircleIcon @click="closeTest()" class="h-[30px] text-custom-gray float-right hover:text-custom-red cursor-pointer" />
            </div>

            <div class="w-full h-fit grid gap-4 mt-6">
                <div class="w-fit h-fit flex gap-2">
                    <Toggle v-model="test_passed" class="my-auto focus:ring-0" />
                    <p :class="test_passed ? 'text-custom-green' : 'text-custom-red'" class="my-auto text-[16px] font-medium">{{ test_passed ? 'Passed' : 'Failed' }}</p>
                </div>

                <div class="w-full h-fit grid">
                    <label for="error_fix" class="text-custom-light-gray text-[16px] font-medium">Enter more test details<span class="text-custom-red">*</span></label>
                    <textarea v-model="test_details" @paste="pasteEvent($event, 'test')" name="error_fix" id="error_reason" class="h-[96px] text-[14px] text-custom-gray bg-custom-bg border-[1px] border-custom-light rounded-[4px] focus:ring-0 focus:border-custom-light" style="resize: none;"></textarea>
                </div>

                <div class="w-full h-fit grid grid-cols-2">
                    <div class="w-full h-[75px] grid overflow-y-scroll">
                        <div class="w-full h-fit grid">
                            <div v-for="(upload, index) in test_uploads" :key="index" class="w-full h-[1.5em] flex gap-2">
                                <XCircleIcon @click="removeUpload(index, 'test')" class="h-[20px] my-auto text-custom-gray cursor-pointer hover:text-custom-red" />
                                <p class="text-[14px] w-full my-auto text-custom-blue underline">{{ upload.name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-fit flow-root">
                        <!-- Upload Attachments -->
                        <input @change="uploadFile($event, 'test')" type="file" multiple accept=".png, .jpg, .jpeg" ref="test_upload" class="hidden">
                        <button @click="$refs.test_upload.click()" class="float-right flex gap-4 w-fit h-[40px] px-6 group text-[16px] text-custom-gray font-medium border-[1px] border-custom-gray rounded-[2px] hover:text-custom-blue hover:border-custom-blue">
                            <ArrowUpTrayIcon class="my-auto h-[24px]" />
                            <p class="my-auto">Upload File</p>
                        </button>
                    </div>
                </div>

                <button @click="submitTest()" class="w-full h-fit p-2 text-center text-[16px] text-white font-medium bg-custom-blue rounded-[4px]">Submit</button>
            </div>
        </div>
    </div>

    <!-- Ready for Updates and DigiPrompt Popup -->
    <div id="update" :class="update || digipromptUpdate ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <div class="w-[576px] h-fit grid m-auto p-6 bg-white rounded-[8px] shadow-newdrop">
            <div class="w-full h-fit flow-root">
                <p class="float-left text-[24px] text-custom-gray font-semibold">{{ update ? 'Ready for Update(s)' : 'Error Fixed' }}</p>
                <XCircleIcon @click="closeUpdate()" class="h-[30px] text-custom-gray float-right hover:text-custom-red cursor-pointer" />
            </div>

            <div class="w-full h-fit grid gap-4 mt-6">
                <div class="w-full h-fit grid">
                    <label for="error_reason" class="text-custom-light-gray text-[16px] font-medium">Cause of Error <span class="text-custom-red">*</span></label>
                    <textarea v-model="error_cause" @paste="pasteEvent($event, 'update')" name="error_reason" id="error_reason" class="h-[75px] text-[14px] text-custom-gray bg-custom-bg border-[1px] border-custom-light rounded-[4px] focus:ring-0 focus:border-custom-light" style="resize: none;"></textarea>
                </div>

                <div class="w-full h-fit grid">
                    <label for="error_fix" class="text-custom-light-gray text-[16px] font-medium">{{ update ? 'Updates required to fix error' : 'Updates implemented to fix error' }} <span class="text-custom-red">*</span></label>
                    <textarea v-model="error_updates" @paste="pasteEvent($event, 'update')" name="error_fix" id="error_reason" class="h-[96px] text-[14px] text-custom-gray bg-custom-bg border-[1px] border-custom-light rounded-[4px] focus:ring-0 focus:border-custom-light" style="resize: none;"></textarea>
                </div>

                <div class="w-full h-fit grid grid-cols-2">
                    <div class="w-full h-[75px] grid overflow-y-scroll">
                        <div class="w-full h-fit grid">
                            <div v-for="(upload, index) in update_uploads" :key="index" class="w-full h-[1.5em] flex gap-2">
                                <XCircleIcon @click="removeUpload(index, 'update')" class="h-[20px] my-auto text-custom-gray cursor-pointer hover:text-custom-red" />
                                <p class="text-[14px] w-full my-auto text-custom-blue underline">{{ upload.name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-fit flow-root">
                        <!-- Upload Attachments -->
                        <input @change="uploadFile($event, 'update')" type="file" multiple accept=".png, .jpg, .jpeg" ref="update_upload" class="hidden">
                        <button @click="$refs.update_upload.click()" class="float-right flex gap-4 w-fit h-[40px] px-6 group text-[16px] text-custom-gray font-medium border-[1px] border-custom-gray rounded-[2px] hover:text-custom-blue hover:border-custom-blue">
                            <ArrowUpTrayIcon class="my-auto h-[24px]" />
                            <p class="my-auto">Upload File</p>
                        </button>
                    </div>
                </div>

                <button @click="submitUpdate()" class="w-full h-fit p-2 text-center text-[16px] text-white font-medium bg-custom-blue rounded-[4px]">Submit</button>
            </div>
        </div>
    </div>
    
    <div v-if="ready" class="sm:ml-64 mt-24 md:mt-[90px] px-4 md:px-10 h-fit w-full md:w-[84%] bg-custom-bg z-10 absolute">
        <!-- Breadcrumb Menu -->
        <div class="flex gap-2 w-fit h-fit">
            <router-link :to="{name: 'Tasks'}" class="grid my-auto w-[24px] h-[24px] bg-white rounded-[2px] shadow-newdrop"><ArrowLeftIcon class="h-[18px] text-custom-blue m-auto" /></router-link>
            <p class="my-auto text-[14px] text-custom-gray font-medium">Task/<span class="text-custom-blue">{{ app_id }}</span></p>
        </div>

        <!-- Header -->
        <div class="w-full h-fit mt-4 grid gap-2 md:gap-0 md:flow-root">
            <div class="w-fit h-fit grid float-left">
                <p class="w-fit text-[20px] md:text-[32px] text-custom-gray font-medium">{{ app_id }} - <span :class="source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'">{{ source == 'MGA' ? 'RocketMGA' : 'RocketAutomation' }}</span></p>
                <p class="w-fit text-[14px] md:text-[16px] text-custom-light-gray">Here you can see details regarding {{ product == "FLOOD" ? carriers[errors[selected_error].carrier].name : carriers[errors[selected_error].carrier.substring(3)].name }}'s error on application {{ app_id }},</p>
            </div>
            <div class="w-fit h-fit flex gap-8 md:float-right">
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
        <div class="w-full h-fit grid md:flex gap-2 md:gap-6 mt-6">
            <!-- Mobile Status -->
            <img :src="`/images/${errors[selected_error].status}.png`" alt="Error Status Bar" class="md:hidden h-[47px] my-2">
            
            <!-- Carrier -->
            <div class="w-full md:w-fit h-fit grid my-auto">
                <div v-if="product == 'HOME'" class="w-full h-fit grid mb-2">
                    <p class="text-[16px] text-custom-light-gray font-medium">Rater</p>
                    <div class="w-full md:w-[200px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                        <p class="my-auto text-[15px] text-custom-gray font-medium">{{ rater }}</p>
                    </div>
                </div>

                <p class="text-[16px] text-custom-light-gray font-medium">Carrier Name</p>
                <div class="w-full md:w-[200px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                    <p class="my-auto text-[15px] text-custom-gray font-medium truncate">{{ product == "FLOOD" ? carriers[errors[selected_error].carrier].name : carriers[errors[selected_error].carrier.substring(3)].name }}</p>
                </div>
            </div>

            <!-- Product -->
            <div class="w-full md:w-fit h-fit grid">
                <p class="text-[16px] text-custom-light-gray font-medium">Product</p>
                <div class="w-full md:w-[200px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                    <p class="my-auto text-[15px] text-custom-gray font-medium uppercase">{{ errors[selected_error].product }}</p>
                </div>
            </div>

            <!-- Type -->
            <div class="w-full md:w-fit h-fit grid">
                <p class="text-[16px] text-custom-light-gray font-medium">Error Type</p>
                <div class="w-full md:w-[200px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                    <p :class="errors[selected_error].type == 'API' ? 'text-custom-blue' : 'text-custom-gray'" class="my-auto text-[15px] font-medium">{{ errors[selected_error].type }}</p>
                </div>
            </div>

            <!-- Assigned -->
            <div class="w-full md:w-fit h-fit grid">
                <p class="text-[16px] text-custom-light-gray font-medium">Assigned To</p>
                <div class="w-full md:w-[200px] h-[40px] bg-white grid px-4 border-[1px] border-custom-light rounded-[4px]">
                    <p class="my-auto text-[15px] text-custom-gray font-medium">{{ errors[selected_error].assigned }}</p>
                </div>
            </div>

            <!-- Status -->
            <img :src="`/images/${errors[selected_error].status}.png`" alt="Error Status Bar" class="hidden md:block h-[47px]">
        </div>

        <!-- Errors View -->
        <div class="w-full h-fit my-12 grid grid-cols-4 rounded-[8px] shadow-newdrop">
            <!-- Error List -->
            <div class="w-full h-fit grid bg-custom-bg rounded-tl-[8px] rounded-bl-[8px]">
                <p class="p-4 text-[20px] text-custom-gray font-semibold">Errors Fixed {{ errors_fixed }}/<span class="text-custom-red">{{ errors.length }}</span></p>
            
                <div class="w-full h-fit grid">
                    <div v-for="(error, index) in errors" :key="index" @click="selectError(index, error.carrier)" class="grid p-4 cursor-pointer" :class="error.carrier == errors[selected_error].carrier ? 'bg-white' : 'hover:shadow-newdrop'">
                        <div class="w-full h-fit flow-root">
                            <p class="w-[150px] truncate float-left text-[16px] text-custom-gray font-medium">{{ error.product == "FLOOD" ? carriers[error.carrier].name : carriers[error.carrier.substring(3)].name }}</p>
                            <p :class="error.fixed ? 'text-custom-green' : 'text-custom-red'" class="float-right text-[13px]">{{ error.status == 'debug' ? 'Debugging' : (error.status == 'update' ? 'Updates in Progress' : (error.status == 'test' ? 'Testing in Progress' : (error.status == 'digiprompt' ? 'DigiPrompt Queue' : "Fixed"))) }}</p>
                        </div>

                        <div class="w-full h-fit flow-root">
                            <p class="w-[200px] truncate float-left text-[16px] text-custom-light-gray">{{ error.desc }}</p>
                            <p :class="error.type == 'API' ? 'text-custom-blue' : 'text-custom-gray'" class="float-right text-[13px]">{{ error.type }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Information -->
            <div class="col-span-3 grid p-6 bg-white rounded-tr-[8px] rounded-br-[8px]">
                <div class="w-full h-fit grid">
                    <div class="w-full h-fit flow-root">
                        <p class="text-[24px] text-custom-gray font-semibold float-left">Error Description and Notes</p>

                        <!-- Update Status Button -->
                        <button @click="errors[selected_error].status == 'debug'  ? update = true : ( errors[selected_error].status == 'digiprompt' ? digipromptUpdate = true : updateStatus())" v-if="!errors[selected_error].fixed" class="text-[18px] text-white px-4 bg-custom-blue border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop active:shadow-none float-right">{{ errors[selected_error].status == 'debug' ? 'Ready for Updates' : (errors[selected_error].status) == 'update' ? 'Ready to Test' : 'Error Fixed' }}</button>
                    </div>

                    <div class="w-fit h-fit flex gap-4">
                        <!-- Application URL -->
                        <a target="_blank" :href="source == 'MGA' ? 'https://agentportal.rocketmga.com/agent-portal/application-details/'+app_id : 'https://agent.rocketflood.com/agent-portal/application-details/'+app_id" class="text-custom-gray">
                            <div class="w-[37px] h-[37px] grid p-[2px] border-[1px] border-custom-light rounded-[4px] shadow-newdrop">
                                <img src="../../assets/favicon.png" alt="Rocket Logo" class="h-full">
                            </div>
                            <!-- Application Link -->
                        </a>
                        
                        <!-- Backend Debugging URL -->
                        <a target="_blank" :href="errors[selected_error].product == 'FLOOD' ? carriers[errors[selected_error].carrier].backend[source]+app_id : carriers[errors[selected_error].carrier.substring(3)].backend[source]+app_id" :class="source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'">
                            <div class="w-[37px] h-[37px] grid p-[2px] border-[1px] border-custom-light rounded-[4px] shadow-newdrop">
                                <img src="../../assets/code.svg" alt="Rocket Logo" class="h-full rounded-full bg-custom-light">
                            </div>
                            <!-- Backend Debugging URL -->
                        </a>
                    </div>

                    <!-- Error Test -->
                    <div v-if="errors[selected_error].status == 'test'" class="w-fit h-fit flex gap-6 mt-4">
                        <p class="text-[16px] text-custom-light-gray font-medium">Completed Tests:</p>
                        <img :src="`/images/${errors[selected_error].tests}.png`" alt="Test Progress Bar" class="h-[16px] my-auto">
                        <button v-if="errors[selected_error].tests < 3" @click="logTest = true" class="my-auto text-[12px] text-white font-medium bg-custom-blue px-2 rounded-[2px]">Log Test</button>
                    </div>

                    <!-- Error Description and Attachments -->
                    <div class="w-full h-fit grid grid-cols-2 gap-8 my-4">
                        <div class="grid">
                            <p class="text-[16px] text-custom-light-gray font-medium">Error Description</p>
                            <textarea v-model="errors[selected_error].desc" disabled style="resize: none;" class="h-[127px] w-full text-custom-light-gray border-custom-light bg-custom-bg rounded-[4px]"></textarea>
                        </div>

                        <div class="grid">
                            <p class="text-[16px] text-custom-light-gray font-medium">Attachment(s)</p>
                            <div class="grid h-[127px] overflow-y-scroll">
                                <div class="h-fit grid">
                                    <a v-for="(upload, index) in error_uploads" :key="index" target="_blank" :href="'https://'+baseURL+upload.path" class="w-fit text-custom-blue truncate underline hover:font-medium">{{ upload.name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Cause and Updates Implemented -->
                    <div v-if="errors[selected_error].status != 'debug' && errors[selected_error].status != 'digiprompt'" class="w-full h-fit grid grid-cols-2 gap-8 mb-6">
                        <div class="grid h-[96px] overflow-y-scroll">
                            <div class="grid gap-2 h-fit">
                                <p class="text-[16px] text-custom-light-gray font-medium">Error Cause</p>
                                <p class="text-[14px] italic text-custom-red">{{ errors[selected_error].cause }}</p>
                            </div>
                        </div>

                        <div class="grid h-[96px] overflow-y-scroll">
                            <div class="grid gap-2 h-fit">
                                <p class="text-[16px] text-custom-light-gray font-medium">Error Updates Implemented</p>
                                <p class="text-[14px] italic text-custom-blue">{{ errors[selected_error].updates }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="w-full h-fit grid grid-cols-3 gap-8 my-6 relative">
                        <div class="w-full h-fit grid gap-4">
                            <div class="w-full h-fit flow-root">
                                <p class="text-[16px] text-custom-light-gray font-medium float-left">Error Notes</p>
                                <!-- Add Note Button -->
                                <button @click="addNote()" class="w-fit px-4 text-white bg-custom-gray border-l-[4px] border-b-[5px] border-[#808080] active:border-custom-gray rounded-[2px] shadow-newdrop active:shadow-none float-right">Create Note</button>
                            </div>


                            <div class="w-full h-[300px] grid overflow-y-scroll">
                                <div class="w-full h-fit grid gap-4">
                                    <!-- List of Notes -->
                                    <div @click="selectNote(index)" v-for="(note, index) in error_notes" :key="index" :class="selected_note == index ? 'inner-border-2' : ''" class="w-full h-fit grid p-2 border-[1px] border-custom-light rounded-[4px] cursor-pointer hover:inner-border-2">
                                        <div class="w-full h-fit flow-root">
                                            <p class="w-[130px] truncate float-left text-[16px] text-custom-gray">{{ note.user }}</p>
                                            <p class="text-[12px] text-custom-light-gray float-right">{{ !note.id ? note.created_at : moment(note.updated_at).format('MM/DD/YYYY hh:mm A') }}</p>
                                        </div>
                                        <p class="w-full truncate text-[14px] text-custom-light-gray">{{ note.note }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Note Description and Attachments -->
                        <div v-if="error_notes.length > 0" class="col-span-2 w-full h-fit grid gap-4">
                            <div class="w-full h-fit flow-root">
                                <!-- Save Note -->
                                <button @click="saveNotes()" class="text-white px-4 bg-custom-blue border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop active:shadow-none float-right">Save Note</button>
                            </div>

                            <!-- Note Description -->
                            <textarea v-model="error_notes[selected_note].note" @paste="pasteEvent($event, 'note')" :disabled="error_notes[selected_note].user != user ? true : false || error_notes[selected_note].disabled" class="w-full h-[144px] text-[16px] text-custom-light-gray border-[1px] border-custom-light rounded-[4px] focus:border-custom-light focus:ring-0 disabled:bg-custom-bg" style="resize: none;"></textarea>
                        
                            <div class="w-full h-fit flow-root">
                                <!-- Upload Attachments -->
                                <input @change="uploadFile($event, 'note')" type="file" multiple accept=".png, .jpg, .jpeg" ref="note_upload" class="hidden">
                                <button @click="$refs.note_upload.click()" v-if="error_notes[selected_note].user == user && !error_notes[selected_note].disabled" class="float-left flex gap-4 w-fit h-[40px] px-6 group text-[16px] text-custom-gray font-medium border-[1px] border-custom-gray rounded-[2px] hover:text-custom-blue hover:border-custom-blue">
                                    <ArrowUpTrayIcon class="my-auto h-[24px]" />
                                    <p class="my-auto">Upload File</p>
                                </button>
                                
                                <!-- Delete Note -->
                                <button @click="deleteNote(selected_note)" v-if="error_notes[selected_note].user == user && !error_notes[selected_note].disabled" class="float-right flex gap-4 w-fit h-[40px] px-6 group text-[16px] text-custom-gray font-medium border-[1px] border-custom-gray rounded-[2px] hover:text-custom-red hover:border-custom-red">
                                    <TrashIcon class="my-auto h-[24px]" />
                                    <p class="my-auto">Delete Note</p>
                                </button>
                            </div>

                            <div class="w-full h-[77px] overflow-y-scroll">
                                <div class="grid h-fit">
                                    <div v-for="(upload, index) in note_uploads" :key="index" class="w-fit h-fit flex gap-2">
                                        <XCircleIcon @click="removeUpload(index, 'note')" v-if="error_notes[selected_note].user == user && !error_notes[selected_note].disabled" class="my-auto h-[24px] text-custom-gray hover:text-custom-red cursor-pointer" />
                                        <a target="_blank" :href="'https://'+baseURL+upload.path" class="text-custom-blue underline">{{ upload.name }}</a>
                                    </div>
                                </div>
                            </div>
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
import textInput from '../components/textInput.vue'

import { ArrowLeftIcon, TrashIcon, ArrowUpTrayIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import Toggle from '@vueform/toggle'
import moment from 'moment'
import carriers from '../../assets/carriers.json'
import raters from '../../assets/raters.json'

export default {
    name: "Task View",
    data(){
        return {
            baseURL: "",
            user: "",
            role: "",
            type: "",
            product: "",
            digipromptUpdate: false,
            update: false,
            logTest: false,
            loading: true,
            ready: false,
            app_id: "",
            source: "",
            selected_error: 0,
            errors_fixed: 0,
            errors: [],
            carriers: carriers,
            raters: raters,
            selected_note: 0,
            error_notes: [],
            error_uploads: [],
            note_uploads: [],
            update_uploads: [],
            error_cause: "",
            error_updates: "",
            test_details: "",
            test_uploads: [],
            test_passed: false,
            rater: ""
        }
    },
    async created(){
        this.moment = moment
        this.baseURL = window.location.host
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
            this.product = response.data.errors[0].product

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

        await axios.get('/api/uploads/'+this.app_id+'/'+this.errors[this.selected_error].carrier)
        .then(response => {
            this.error_uploads = response.data.uploads
        })

        if(this.error_notes.length > 0){
            await axios.get('/api/note/uploads/'+this.error_notes[this.selected_note].id)
            .then(response => {
                this.note_uploads = response.data.uploads
            })
        }

        if(this.product == 'HOME'){
            this.raters.forEach(rater => {
                if(rater.code == this.errors[this.selected_error].carrier.substring(0, 2)){
                    this.rater = rater.name
                }
            })
        }

        this.loading = false
        this.ready = true
    },
    methods: {
        closeUpdate(){
            if(this.update){
                this.update = false
            }else if(this.digipromptUpdate){
                this.digipromptUpdate = false
            }

            this.update_uploads = []
            this.error_cause = ""
            this.error_updates = ""
        },
        closeTest(){
            this.logTest = false
            this.test_details = ""
            this.test_uploads = []
            this.test_passed = false
        },
        async selectError(index, name){
            this.loading = true

            this.selected_error = index
            this.$router.push({name: 'Task', params: {app_id: this.app_id, carrier: name}})

            await axios.get('/api/notes/'+this.app_id+'/'+this.errors[this.selected_error].carrier)
            .then(response => {
                this.error_notes = response.data.notes
            })

            await axios.get('/api/uploads/'+this.app_id+'/'+this.errors[this.selected_error].carrier)
            .then(response => {
                this.error_uploads = response.data.uploads
            })

            if(this.product == 'HOME'){
                this.raters.forEach(rater => {
                    if(rater.code == this.errors[index].carrier.substring(0, 2)){
                        this.rater = rater.name
                    }
                })
            }

            this.selected_note = 0

            this.loading = false
        },
        async removeUpload(index, type){
            if(type == 'note'){
                if(this.note_uploads[index].id){
                    await axios.delete('/api/upload/'+this.note_uploads[index].id)
                }

                this.note_uploads.splice(index, 1)
            }else if(type == 'update'){
                this.update_uploads.splice(index, 1)
            }else if(type == 'test'){
                this.test_uploads.splice(index, 1)
            }
        },
        async uploadFile(e, upload){
            const keys = Object.keys(e.target.files)

            keys.forEach(key => {
                const type = e.target.files[key].name.split('.').pop()

                if(upload == 'note'){
                    const newFile = new File([e.target.files[key]], this.app_id+"_"+this.errors[this.selected_error].carrier+"_upload"+this.note_uploads.length +"."+type)
                    this.note_uploads.push(newFile)
                }else if(upload == 'update'){
                    const newFile = new File([e.target.files[key]], this.app_id+"_"+this.errors[this.selected_error].carrier+"_update"+this.update_uploads.length+"."+type)
                    this.update_uploads.push(newFile)
                }else if(upload == 'test'){
                    const newFile = new File([e.target.files[key]], this.app_id+"_"+this.errors[this.selected_error].carrier+"_test"+this.test_uploads.length+"."+type)
                    this.test_uploads.push(newFile)
                }
            })
        },
        async pasteEvent(e, upload){
            if(e.clipboardData.items[0].getAsFile()){
                const type = e.clipboardData.items[0].getAsFile().name.split('.').pop()

                if(upload == 'note'){
                    const newFile = new File([e.clipboardData.items[0].getAsFile()], this.app_id+"_"+this.errors[this.selected_error].carrier+"_upload"+this.note_uploads.length+"."+type)
                    this.note_uploads.push(newFile)
                }
                else if(upload == 'update'){
                    const newFile = new File([e.clipboardData.items[0].getAsFile()], this.app_id+"_"+this.errors[this.selected_error].carrier.replace("-", "_")+"_update"+this.update_uploads.length+"."+type)
                    this.update_uploads.push(newFile)
                }
                else if(upload == 'test'){
                    const newFile = new File([e.clipboardData.items[0].getAsFile()], this.app_id+"_"+this.errors[this.selected_error].carrier.replace("-", "_")+"_test"+this.test_uploads.length+"."+type)
                    this.test_uploads.push(newFile)
                }
            }
        },
        async selectNote(index){
            this.loading = true

            this.selected_note = index
            await axios.get('/api/note/uploads/'+this.error_notes[index].id)
            .then(response => {
                this.note_uploads = response.data.uploads
            })

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

            this.selected_note = this.error_notes.length - 1
            this.note_uploads = []

            this.loading = false
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
            this.selected_note = 0

            this.loading = false
        },
        async saveNotes(){
            this.loading = true

            if(this.error_notes[this.selected_note].id){
                await axios.put('/api/note/'+this.error_notes[this.selected_note].id, this.error_notes[this.selected_note])
            } else {
                await axios.post('/api/note', this.error_notes[this.selected_note])
                this.selected_note = 0
            }

            await axios.get('/api/notes/'+this.app_id+'/'+this.errors[this.selected_error].carrier)
            .then(response => {
                this.error_notes = response.data.notes
            })

            let uploads = []

            if(this.note_uploads.length > 0){
                this.note_uploads.forEach(upload => {
                    if(!upload.id){
                        uploads.push(upload)
                    }
                })
            }

            const header = {
                headers: {'content-type': 'multipart/form-data'}
            }

            if(uploads.length > 0){
                await axios.post('/api/note/upload', {
                    "uploads": uploads,
                    "app_id": this.app_id,
                    "carrier": this.errors[this.selected_error].carrier,
                    "note": this.error_notes[this.selected_note].id
                }, header)
            }

            await axios.get('/api/note/uploads/'+this.error_notes[this.selected_note].id)
            .then(response => {
                this.note_uploads = response.data.uploads
            })

            this.loading = false
        },
        async submitUpdate(){
            let valid = true

            if(!this.error_cause){
                this.$alert({
                    title: "Update Error",
                    text: "Please enter an error cause.",
                    type: 'warn'
                })

                valid = false
            }

            if(!this.error_updates){
                this.$alert({
                    title: "Update Error",
                    text: "Please enter updates that need to be made.",
                    type: 'warn'
                })

                valid = false
            }

            if(valid){
                const header = {
                    headers: {'content-type': 'multipart/form-data'}
                }

                await axios.post('/api/status/'+this.errors[this.selected_error].id, {
                    "cause": this.error_cause,
                    "updates": this.error_updates,
                    "uploads": this.update_uploads
                }, header)
                .then(response => {
                    location.reload()
                })
            }
        },
        async submitTest(){
            let valid = true

            if(!this.test_details){
                this.$alert({
                    title: "Test Log Error",
                    text: "Please enter test details.",
                    type: 'warn'
                })

                valid = false
            }

            if(valid){
                const header = {
                    headers: {'content-type': 'multipart/form-data'}
                }

                await axios.post('/api/log-test/'+this.errors[this.selected_error].id, {
                    "details": this.test_details,
                    "passed": this.test_passed,
                    "uploads": this.test_uploads
                }, header)
                .then(response => {
                    location.reload()
                })
            }
        },
        async updateStatus(){
            if(this.errors[this.selected_error].status == 'test' && this.errors[this.selected_error].tests < 3){
                this.$alert({
                    title: "Update Error",
                    text: "Please complete a total of 3 test for this application error before you can mark it as Fixed.",
                    type: 'warn'
                })
            }else {
                await axios.post('/api/status/'+this.errors[this.selected_error].id)
                .then(response => {
                    location.reload()
                })
            }
        }
    },
    components: {
        NavBar,
        profileMenu,
        loading,
        textInput,
        Toggle,
        ArrowLeftIcon,
        TrashIcon,
        ArrowUpTrayIcon,
        XCircleIcon
    }
}
</script>

<style src="@vueform/toggle/themes/default.css"></style>