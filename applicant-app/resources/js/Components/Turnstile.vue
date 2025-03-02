<script setup>
import { ref } from 'vue';
import VueTurnstile from 'vue-turnstile';

const props = defineProps({
  modelValue: String,
});

const emit = defineEmits(['update:modelValue']);

const turnstileRef = ref(null);

const turnstileSiteKey = import.meta.env.VITE_TURNSTILE_SITE_KEY;

const onVerify = (token) => {
  emit('update:modelValue', token);
};

const onExpire = () => {
  emit('update:modelValue', '');
};

const onError = () => {
  emit('update:modelValue', '');
};

defineExpose({
  reset: () => turnstileRef.value?.reset(),
});
</script>

<template>
    <VueTurnstile
        v-if="turnstileSiteKey"
        ref="turnstileRef"
        :siteKey="turnstileSiteKey"
        :modelValue="modelValue"
        @verify="onVerify"
        @expire="onExpire"
        @error="onError"
    />
    <p v-else class="text-red-500">Turnstile site key is not configured.</p>
</template>
