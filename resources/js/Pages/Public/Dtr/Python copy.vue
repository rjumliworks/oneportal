<template>
  <div>
    <video ref="video" autoplay playsinline width="640" height="480"></video>
    <canvas ref="canvas" width="640" height="480" style="position:absolute; top:0; left:0;"></canvas>
    <button @click="captureFrame" style="position:absolute; top:500px;">Detect Person</button>
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
      const stream = await navigator.mediaDevices.getUserMedia({ video: true });
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
video, canvas {
  position: absolute;
}
</style>
