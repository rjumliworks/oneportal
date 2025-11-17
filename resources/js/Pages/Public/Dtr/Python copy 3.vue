<template>

    <Head title="Human Resource - Date Time Record" />

   <body>
    <div class="account-pages my-4 pt-sm-1">
        <div class="container" style="max-width: 1400px;">
                
           
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="text-center mb-4">
                        <img src="/images/logo-sm.png" alt="" class="avatar-sm mb-2">
                        <p class="fs-14 fw-semibold text-p text-uppercase">Department of Science & Technology - IX</p>
                        <p class="fs-13 text-muted" style="margin-top: -20px;">Human Resource - Date Time Record</p>
                    </div>
                    <div class="card border bg-white">
                        <div class="card-header bg-primary">
                            <span class="text-white float-end fs-20 fw-semibold text-center dfw-medium" v-text="currentTime"></span>
                            <h4 class="text-white text-uppercase fw-semibold  fs-20 mt-1 mb-n2">{{ currentDate}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5"> 
                                    <video
                                        ref="video"
                                        autoplay
                                        playsinline
                                        class="qr-child img-thumbnail">
                                    </video>
                                </div>
                                <div class="col-md-7">
                                    <!-- <div class="d-flex w-100 justify-content-center align-items-center" v-if="error">
                                        <div class="p-4 w-100 border rounded bg-danger-subtle text-center">
                                            <p class="mb-0 text-danger fw-semibold">Hi, {{ error.name }}</p>
                                            <p class="mb-0 text-danger fs-11" v-if="error.type == 'not'">You are <b>not registered</b> as a participant. Please go to the <b>Sessions tab</b> to complete your registration</p>
                                            <p class="mb-0 text-danger fs-11" v-else>Your attendance has already been recorded</p>
                                        </div>
                                    </div> -->
                                    <!-- <div v-if="employee" class="pt-1 ps-1 profile-wrapper">
                                        <div class="row g-4">
                                            <div class="col-auto">
                                                <div>
                                                    <img :src="employee.avatar" alt="user-img" class="avatar-lg">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="">
                                                    <p class="text-primary text-opacity-75 mb-1">Welcome, and thank you..</p>
                                                    <h3 class="text-primary mb-1">{{ employee.name }}</h3>
                                                    <p class="text-primary text-muted fs-14">Attendance confirmed on <b class="text-primary">{{employee.time}}</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div v-if="status == 'New'" class="d-flex w-100 justify-content-center align-items-center mb-2">
                                        <div class="p-4 w-100 border rounded bg-success-subtle">
                                            <div class="d-flex mb-n3">
                                                <div class="flex-shrink-0 me-3">
                                                    <div style="height:2.5rem;width:2.5rem;">
                                                        <img :src="employee.avatar" alt="user-img" class="avatar-sm mt-n2">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-0 fs-14"><span class="text-body">{{employee.name}}</span></h5>
                                                    <p class="text-muted text-truncate-two-lines fs-12">{{employee.division}}</p>
                                                </div>
                                                <div class="flex-0">
                                                    <div class="mb-4">{{ employee.time }}</div>
                                                </div>
                                            </div>
                                            <!-- <div class="row g-1">
                                                <div class="col-auto">
                                                    <div>
                                                        <img src="/images/logo-sm.png" alt="user-img" class="avatar-md">
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="">
                                                        <p class="text-primary text-opacity-75 mb-0">Welcome, and thank you..</p>
                                                        <h3 class="text-primary mb-0">Ra-ouf Jumli</h3>
                                                        <p class="text-primary text-muted fs-14">Attendance confirmed on <b class="text-primary">November 17, 2025</b></p>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                    <div v-else-if="status == 'Duplicate'" class="d-flex w-100 justify-content-center align-items-center mb-2">
                                        <div class="p-4 w-100 border rounded bg-warning-subtle text-center">
                                            <p class="mb-0 text-dark fs-12">Duplicate attendance detected for <b class="text-danger">{{ employee.name }}</b>.</p>
                                            <p class="mb-0 text-muted fs-11">The system has already logged this employee's time-in/time-out. No additional entry is needed.</p>
                                        </div>
                                    </div>
                                    <div v-else-if="status == 'Error'" class="d-flex w-100 justify-content-center align-items-center mb-2">
                                        <div class="p-4 w-100 border rounded bg-danger-subtle text-center">
                                            <p class="mb-0 text-dark fs-12">Employee not found in the system.</p>
                                            <p class="mb-0 text-muted fs-11">No matching employee was found based on the QR code or face data. Please verify your credentials or seek assistance.</p>
                                        </div>
                                    </div>
                                    <div v-else class="d-flex w-100 justify-content-center align-items-center mb-2">
                                        <div class="p-4 w-100 border rounded bg-dark-subtle text-center">
                                            <p class="mb-0 text-dark fs-12"> Please face the camera to begin.</p>
                                            <p class="mb-0 text-muted fs-11"> Make sure your face is clearly visible for accurate recognition.</p>
                                        </div>
                                    </div>
                                    <!-- <h1 style="font-size: 120px; margin-top: -10px; margin-bottom: 5px;" class="text-primary text-center dfw-medium" v-text="currentTime"></h1> -->
                                    <!-- <div class="p-2">
                                        <div class="text-center">
                                        <div class=" mt-n2 mb-n2">
                                                <b-tabs v-model="activebutton" pills nav-class="bg-light rounded nav-justified fw-bold" content-class="mt-3">
                                                    <b-tab title="AM IN" v-on:click="captureFrame()"></b-tab>
                                                    <b-tab title="AM OUT" v-on:click="swap('Time Out (am)','1')"></b-tab>
                                                    <b-tab title="PM IN" v-on:click="swap('Time In (pm)','2')"></b-tab>
                                                    <b-tab title="PM OUT" v-on:click="swap('Time Out (pm)','3')"></b-tab>
                                                </b-tabs>
                                            </div>
                                            <input @keyup.enter="find" v-model="form.username" autofocus type="text" class="form-control form-control-lg text-center" style="font-size: 30px; text-transform: uppercase; background-color: #eff2f7;">
                                        </div>
                                    </div> -->
                                    <div class="card bg-light-subtle shadow-none border">
                                        <div class="card-header bg-light-subtle">
                                            <div class="d-flex mb-n3">
                                                <div class="flex-shrink-0 me-3">
                                                    <div style="height:2.5rem; width:2.5rem;">
                                                        <span class="avatar-title bg-primary-subtle rounded p-2 mt-n1">
                                                            <i class="ri-file-list-3-line text-primary fs-24"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-0 fs-14"><span class="text-body">List of Attendees</span></h5>
                                                    <p class="text-muted fs-12">
                                                        Shows participants who have successfully scanned the QR code.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body bg-white rounded-bottom">
                                            <div class="table-responsive table-card"
                                                style="height:calc(100vh - 550px); overflow-x:hidden;">
                                                <table class="table table-nowrap align-middle mb-0">
                                                    <thead class="bg-light thead-fixed">
                                                        <tr class="fs-11">
                                                            <th class="text-center">#</th>
                                                            <th>Name</th>
                                                            <th class="text-center">Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-if="lists.length">
                                                        <tr v-for="(list,index) in lists"
                                                            :key="index"
                                                            :class="['fs-12',{ 'fw-semibold bg-success-subtle': index === 0 }]">
                                                            <td class="text-center">{{ index + 1 }}</td>
                                                            <td>{{ list.name }}</td>
                                                            <td class="text-center">{{ list.time }}</td>
                                                        </tr>
                                                    </tbody>
                                                    <tbody v-else>
                                                        <tr><td colspan="3" class="text-center text-muted">No employees found.</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-lg fw-semibold btn-light flex-fill" @click="captureFrame('Time In (am)')">AM IN</button>
                                            <button class="btn btn-lg fw-semibold btn-light flex-fill" @click="captureFrame('Time Out (am)')">AM OUT</button>
                                            <button class="btn btn-lg fw-semibold btn-light flex-fill" @click="captureFrame('Time In (pm)')">PM IN</button>
                                            <button class="btn btn-lg fw-semibold btn-light flex-fill" @click="captureFrame('Time Out (pm)')">PM OUT</button>
                                        </div>
                                    </div>
                                    <!-- <template v-if="user">
                                        <div v-if="status == 'New' || status == 'Success'" class="alert alert-success alert-dismissible alert-additional mt-3 mb-n1" role="alert">
                                            <div class="alert-body">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <img :src="form.image" alt="" class="rounded-circle" style="height: 2.5rem; width: 2.5rem;">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="alert-heading mb-0">{{user.name}}</h5>
                                                        <p class="mb-0"> {{user.division}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert-content">
                                                <p class="mb-0" v-html="user.message.message"></p>
                                            </div>
                                        </div>
                                        <div v-else :class="'alert alert-'+user.message.status+' alert-dismissible bg-'+user.message.status+' mt-3 text-white alert-label-icon mb-xl-0'" role="alert">
                                            <i class="ri-error-warning-line label-icon"></i><span v-html="user.message.message"></span>
                                        </div>
                                    </template>
                                    <template v-if="status == 'Error'">
                                        <div class="alert alert-danger alert-dismissible bg-danger mt-3 text-white alert-label-icon mb-xl-0" role="alert">
                                            <i class="ri-error-warning-line label-icon"></i><strong>Danger</strong> - Your record could not be found. Please contact the administrator for assistance.
                                        </div>
                                    </template> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mt-5 text-center">
                        <p>2025 © DOST-IX ICT TEAM</p>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</body>
</template>
<script>
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'  };
    const options1 = { hour12: false  };
    const twelve = new Date("2022-03-25 11:00:00").toLocaleTimeString("en-US",options1);
    const twelvethirty = new Date("2022-03-25 12:30:00").toLocaleTimeString("en-US",options1);
    const one = new Date("2022-03-25 15:00:00").toLocaleTimeString("en-US",options1);
    import { useForm } from '@inertiajs/vue3';
import { isError } from 'lodash';
    export default {
        layout: null,
        data() {
            return {
                currentUrl: window.location.origin,
                currentDate: null,
                currentTime: null,
                currentTime2: null,
                user: '',
                activebutton: 0,
                inactive: false,
                message: '',
                status: '',
                employee: null,
                form: useForm({
                    image: null,
                    username: null,
                    type:'Time In (am)',
                    option: 'dtr'
                }),
                lists: []
            };
        },

        // created(){
        //     this.filter();
        // },
        mounted() {
            setInterval(() => {
                this.currentSecond = new Date().toLocaleTimeString([],{seconds: '2-digit'});
                this.currentTime = new Date().toLocaleTimeString("en-US");
                this.currentDate = new Date().toLocaleDateString("en-US",options);
            }, 1000);
            this.keepAliveInterval = setInterval(() => {
                axios.get('/keep-alive'); 
            }, 1000 * 60 * 30); 
            this.initCamera();
        },
        beforeUnmount() {
            clearInterval(this.keepAliveInterval);
        },
        methods: {
            find(){
                this.user = ''; 
                this.inactive = false;
                this.capturePhoto();
                this.form.post('/attendance',{
                    preserveScroll: true,
                    onSuccess: (response) => {
                        if(response.props.flash.info == 'Error'){
                            this.status = response.props.flash.info;
                            setInterval(() => {
                                this.status = null;
                            }, 9000);
                        }else{
                            this.status = response.props.flash.info;
                            this.user = response.props.flash.data;
                            this.form.username = null;
                            setInterval(() => {
                                this.user = null;
                                this.status = null;
                            }, 9000);
                        } 
                    },
                });
            },

            filter(){
                // this.currentTime2 = new Date().toLocaleTimeString("en-US",options1);
                // if(this.currentTime2 < twelve){
                //     this.activebutton = 0; this.form.type = 'Time In (am)';
                // }else if(this.currentTime2 >= twelve && this.currentTime2 < twelvethirty){
                //     this.activebutton = 1; this.form.type = 'Time Out (am)';
                // }else if(this.currentTime2 >= twelvethirty && this.currentTime2 < one){
                //     this.activebutton = 2; this.form.type = 'Time In (pm)';
                // }else{
                //     this.activebutton = 3; this.form.type = 'Time Out (pm)';
                // }
            },

            // swap(type,action){
            //     this.form.type = type;
            //     this.activebutton = action;
            //     setInterval(() => {
            //         this.filter();
            //         this.user = '';
            //     }, 20000); 
            // },
            async initCamera() {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                this.$refs.video.srcObject = stream;
            },  
            async captureFrame(type) {
                const video = this.$refs.video;
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg'));
                const formData = new FormData();
                formData.append('image', blob); 
                formData.append('type',type); 
                formData.append('option','dtr'); 

                try {
                    const res = await axios.post('/recognize', formData); 
                    const data = res.data;
                    this.user = res.data.data;
                    this.status = res.data.info;
                    if(this.status == 'New'){
                        this.lists.unshift(this.user);
                    }
                    this.employee = this.user;
                    // if (data.faces.length > 0) {
                    // // At least one person detected → green box around first face
                    // const face = data.faces[0];
                    // this.drawBox(face.box, "Person Detected", "green");
                    // } else {
                    // // No person → red box covering the whole video
                    // const videoCanvas = this.$refs.canvas;
                    // const ctx = videoCanvas.getContext('2d');
                    // ctx.clearRect(0, 0, videoCanvas.width, videoCanvas.height);
                    // ctx.strokeStyle = "red";
                    // ctx.lineWidth = 4;
                    // ctx.strokeRect(0, 0, videoCanvas.width, videoCanvas.height);
                    // ctx.font = "20px Arial";
                    // ctx.fillStyle = "red";
                    // ctx.fillText("No Person Detected", 10, 30);
                    // }
                } catch (e) {
                    console.error(e);
                }
            },
        }
    }
</script>
<style>
    .nav-pills .nav-link {
        font-weight: bold;
        font-size: 16px;
    }
    .qr-child {
        padding-top: 8px;
        padding-left: 8px;
        padding-bottom: 8px;
        width: 100%;
        height: 100%;
        object-fit: cover;   
    }
</style>