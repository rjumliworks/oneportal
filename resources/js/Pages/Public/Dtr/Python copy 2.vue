<template>
    <div class="auth-page-wrapper d-flex flex-column">
        <div class="auth-page-content d-flex justify-content-center"
            style="background-color:#EFF0F3; min-height:100vh; overflow:hidden;">
            <div class="row p-5">
                <!-- Header Info -->
                <div class="col-lg-12 text-center mb-3">
                    <img src="@assets/images/logos/dost.png" alt="" class="avatar-xs mb-1">
                    <img src="@assets/images/logos/bagongpilipinas.png" alt="" class="avatar-xs mb-1">
                    <h1 class="mb-0 ff-secondary fw-semibold text-capitalize lh-base fs-22">
                        <span class="text-primary">asd</span>
                    </h1>
                    <h1 class="mb-0 ff-secondary fw-semibold text-capitalize lh-base fs-14">
                        <span class="text-warning">asda</span>
                    </h1>
                    <h1 class="mb-0 ff-secondary fw-semibold text-capitalize lh-base fs-12">
                        <span class="text-success">asda</span>
                    </h1>
                    <!-- <p class="text-muted mb-2 fs-12">{{ selected.detail.description }}</p> -->
                </div>

                <!-- QR / Camera Box -->
                <div class="col-lg-6">
                    <div class="text-center">
                        <div class="position-relative d-inline-block" style="width:700px; height:400px;">
                            <div class="position-absolute qr-box" style="transform:translate(-50%, -50%);">
                                
                            <video ref="video" autoplay playsinline width="auto" height="500"></video>
                             <button @click="captureFrame" style="position:absolute; top:500px;">Detect Person</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="col-lg-6">
                    <div class="card bg-light-subtle shadow-none border">
                        <div class="card-header bg-light-subtle" style="height:120px;">

                            <div class="d-flex w-100 justify-content-center align-items-center">
                                <div class="p-4 w-100 border rounded bg-dark-subtle text-center">
                                    <p class="mb-0 text-dark fs-12">Please use the <b>QR Scanner</b> in the application
                                        to scan the provided QR code.</p>
                                    <p class="mb-0 text-muted fs-11">If you are using the mobile browser, please allow
                                        camera access to enable QR code scanning.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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

                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No participants found.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
    export default {
        layout: null,
        mounted() {
            this.initCamera();
        },
        methods: {
            async initCamera() {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                this.$refs.video.srcObject = stream;
            },

            async captureFrame() {
                const video = this.$refs.video;
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg'));
                const formData = new FormData();
                formData.append('image', blob);

                try {
                    const res = await axios.post('/recognize', formData);
                    const data = res.data;

                    if (data.faces.length > 0) {
                        // At least one person detected → green box around first face
                        const face = data.faces[0];
                        this.drawBox(face.box, "Person Detected", "green");
                    } else {
                        // No person → red box covering the whole video
                        const videoCanvas = this.$refs.canvas;
                        const ctx = videoCanvas.getContext('2d');
                        ctx.clearRect(0, 0, videoCanvas.width, videoCanvas.height);
                        ctx.strokeStyle = "red";
                        ctx.lineWidth = 4;
                        ctx.strokeRect(0, 0, videoCanvas.width, videoCanvas.height);
                        ctx.font = "20px Arial";
                        ctx.fillStyle = "red";
                        ctx.fillText("No Person Detected", 10, 30);
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            drawBox(box, label, color) {
                const canvas = this.$refs.canvas;
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                ctx.strokeStyle = color;
                ctx.lineWidth = 3;
                ctx.strokeRect(box.left, box.top, box.right - box.left, box.bottom - box.top);

                ctx.fillStyle = color;
                ctx.font = "16px Arial";
                ctx.fillText(label, box.left, box.top - 5);
            },
        }
    }

</script>

<style scoped>
    video,
    canvas {
        position: absolute;
    }

</style>
