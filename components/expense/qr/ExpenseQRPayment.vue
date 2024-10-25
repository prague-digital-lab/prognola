<template>
  <qrcode-vue :value="value" :size="size" level="H" render-as="svg" />
</template>

<script>
import QrcodeVue, { QrcodeCanvas, QrcodeSvg } from "qrcode.vue";

export default {
  data() {
    return {
      value: "https://prognola.com",
      size: 300,
    };
  },
  components: {
    QrcodeVue,
    QrcodeCanvas,
    QrcodeSvg,
  },
};
</script>
