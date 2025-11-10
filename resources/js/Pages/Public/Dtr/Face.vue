<template>
  <div>
    <h3>Face Recognition Attendance</h3>
    <video ref="video" autoplay playsinline width="320" height="240"></video>
    <button @click="captureAndSend">Capture & Verify</button>

    <div v-if="result">
      <p v-if="result.match">✅ Recognized: {{ result.name }}</p>
      <p v-else>❌ Face not recognized</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      result: null,
    };
  },
  mounted() {
    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => {
        this.$refs.video.srcObject = stream;
      });
  },
  methods: {
    async captureAndSend() {
      const canvas = document.createElement('canvas');
      canvas.width = 320;
      canvas.height = 240;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(this.$refs.video, 0, 0, 320, 240);

      const base64Image = canvas.toDataURL('image/jpeg');

      const response = await fetch('http://127.0.0.1:5000/recognize', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ image: base64Image })
      });

      this.result = await response.json();
    }
  }
};
</script>
