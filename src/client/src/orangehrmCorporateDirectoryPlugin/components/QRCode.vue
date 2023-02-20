<script>
import {toDataURL} from 'qrcode';
import {h, ref, watch, onBeforeMount} from 'vue';

export default {
  name: 'QRCode',
  props: {
    value: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const qrImgSrc = ref('');

    const generateQR = async () => {
      const url = await toDataURL(String(props.value), {
        type: 'image/png',
        width: 140,
      });
      qrImgSrc.value = url || '';
    };

    watch(() => props.value, generateQR);

    onBeforeMount(generateQR);

    return () =>
      h('img', {
        src: qrImgSrc.value,
        class: 'orangehrm-qr-code',
      });
  },
};
</script>

<style scoped>
.orangehrm-qr-code {
  margin: auto;
  width: 140px;
  display: block;
}
</style>
