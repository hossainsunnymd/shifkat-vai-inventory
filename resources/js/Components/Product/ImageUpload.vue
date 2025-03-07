<script setup>
import { ref } from "vue";

const props = defineProps({
    productImage: String,
});

const emit=defineEmits(['image'])
const preview = ref();
const imageSelected = (e) => {
    preview.value = URL.createObjectURL(e.target.files[0]);

    emit('image',e.target.files[0])

};
</script>
<template>
    <div>
        <label for="image">
            <img
            :src="preview ? preview : productImage ? `/images/${productImage}`:'placeholder.png'"
                class="img-thumbnail"
                height="50px" width="50px"
                alt="Product Image"
            />
        </label>
        <input
            @input="imageSelected($event)"
            type="file"
            name="image"
            id="image"

        />
    </div>
</template>
