<script setup lang="ts">
import { ref } from 'vue';

interface Props {
    modelValue?: File;
    modelValueError?: string;
    placeholder?: string;
    vueError?: any;
    accept?: string;
    preview?: string;
}

const emit = defineEmits(['update:modelValue'])

const props = withDefaults(defineProps<Props>(), {
    accept: 'image/*'
});

const file = ref<File[]>([]);
const preview = ref<string | null>(props.preview ?? null);
const fileInput = ref<HTMLInputElement | null>(null);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files) return;

    file.value = Array.from(target.files);

    if (file.value.length > 0 && file.value[0].type.startsWith('image/')) {
        preview.value = URL.createObjectURL(file.value[0]);
    } else {
        preview.value = null;
    }

    emit('update:modelValue', file.value[0]);
};
</script>

<template>
    <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 p-6 rounded-lg hover:border-gray-500 transition-colors cursor-pointer"
        @click="triggerFileInput">
        <input type="file" ref="fileInput" class="hidden" @change="handleFileChange" :accept="props.accept" multiple />

        <!-- Show preview if file exists and is an image -->
        <img v-if="preview" :src="preview" alt="Preview" class="max-w-full max-h-64 object-contain" />

        <!-- Placeholder text when no file selected -->
        <p v-else class="text-gray-400">{{ props.placeholder || 'Click to upload an image' }}</p>
    </div>
    <p v-if="props.modelValueError || props.vueError?.$errors?.[0]?.$message" class="text-sm text-red-500 mt-2">
        {{ props.modelValueError || props.vueError?.$errors?.[0]?.$message }}
    </p>
</template>
