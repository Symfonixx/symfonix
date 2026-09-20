<template>
    <div class="input-box password-input-box">
        <input
            :id="id"
            :value="modelValue"
            :type="showPassword ? 'text' : 'password'"
            :name="name"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            :autocomplete="autocomplete"
            @input="emit('update:modelValue', $event.target.value)"
        >
        <button
            type="button"
            class="password-input-box__toggle"
            :aria-label="showPassword ? hideLabel : showLabel"
            :aria-pressed="showPassword"
            :disabled="disabled"
            @click="showPassword = !showPassword"
        >
            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    modelValue: { type: String, default: '' },
    id: { type: String, default: '' },
    name: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    autocomplete: { type: String, default: '' },
    showLabel: { type: String, default: 'Show password' },
    hideLabel: { type: String, default: 'Hide password' },
});

const emit = defineEmits(['update:modelValue']);

const showPassword = ref(false);
</script>
