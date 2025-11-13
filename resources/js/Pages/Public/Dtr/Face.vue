<template>
  <div>
    <video ref="video" autoplay playsinline width="320" height="240"></video>
    <button @click="capture">Recognize</button>
    <canvas ref="canvas" width="320" height="240" style="display:none"></canvas>
  </div>
</template>

<script>
export default {
  layout: null,
  mounted() {
    // Access webcam
    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => { this.$refs.video.srcObject = stream; })
      .catch(err => console.error('Error accessing webcam', err));
  },
  methods: {
    capture() {
      // Capture current frame
      const canvas = this.$refs.canvas;
      const video = this.$refs.video;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

      // Convert to blob
      canvas.toBlob(blob => {
        const formData = new FormData();
        formData.append('image', blob, 'capture.png');

        // Send to backend for recognition
        axios.post('/recognize', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        }).then(res => {
          console.log('Recognition result:', res.data);
          alert(`Recognized user: ${res.data.user_name} (ID: ${res.data.user_id})`);
        }).catch(err => console.error(err));
      }, 'image/png');
    }
  }
}
</script>