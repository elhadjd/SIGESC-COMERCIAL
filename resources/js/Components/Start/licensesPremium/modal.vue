<template>
   <div class="Container">
      <div class="modal">

      </div>
   </div>
</template>

<script setup>
import axios from "axios"
import { onMounted } from "vue"
onMounted(() => {
    getTypeLicenses()
})
async function getTypeLicenses() {

   await axios.get('http://localhost:8000/api/newClient', {
        headers: {
            Authorization: 'Bearer eyJpdiI6Iks1WERaejBxL1RnVTB5N0FmU2pJT3c9PSIsInZhbHVlIjoiWXNPL2JZWjR6U2JIbDFFSjM1ekVHNEpmR2w5WmVORmJ3d0VOWHNIaXgyZG5NZGEvamFNM0czWkNwZDJPWlB0aXA3NCttcUhjQWRnSk1ScUJZaENYaGQrd3BoeUhKV3BXYVN4VTJBbUNBSk1WeHpPM2ZvQlVXQkJVODFXM05SUUIiLCJtYWMiOiIyNDNjZDg2NzQ1ZDI2ZGM4MWU4MTEyNjNkZjU1YzlmZGQxZDE3ZjkxMDMwYjNhMmVlMmYyZDQzNGQ5MjRjNGM4IiwidGFnIjoiIn0%3D'
        }
    })
    .then((response) => {
        console.log(response);
    })
    .catch((err) => {
        console.log(err);
    });
}
</script>

<style lang="scss" scoped>

</style>
