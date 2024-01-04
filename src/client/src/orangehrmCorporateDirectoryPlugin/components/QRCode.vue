<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

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
