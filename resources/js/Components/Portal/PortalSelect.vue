<template>
    <div
        ref="root"
        class="portal-select"
        :class="{
            'portal-select--open': isOpen,
            'portal-select--error': hasError,
            'portal-select--disabled': disabled,
        }"
    >
        <button
            :id="id"
            type="button"
            class="portal-select__trigger"
            :class="{ 'portal-select__trigger--error': hasError }"
            :disabled="disabled"
            :aria-expanded="isOpen"
            aria-haspopup="listbox"
            @click.stop="toggle"
        >
            <span
                class="portal-select__value"
                :class="{ 'portal-select__value--placeholder': !hasValue }"
            >
                {{ selectedLabel }}
            </span>
            <i class="fas fa-chevron-down portal-select__icon" :class="{ 'portal-select__icon--open': isOpen }"></i>
        </button>

        <ul
            v-if="isOpen"
            class="portal-select__menu"
            role="listbox"
            :aria-labelledby="id"
            @click.stop
        >
            <li
                v-for="option in options"
                :key="String(option.value)"
                role="option"
                class="portal-select__option"
                :class="{ 'portal-select__option--selected': isSelected(option.value) }"
                :aria-selected="isSelected(option.value)"
                @mousedown.prevent="selectOption(option)"
            >
                {{ option.label }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    options: { type: Array, default: () => [] },
    placeholder: { type: String, default: '' },
    id: { type: String, default: '' },
    hasError: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const root = ref(null);
const isOpen = ref(false);

const hasValue = computed(() => props.modelValue !== '' && props.modelValue != null);

const selectedLabel = computed(() => {
    const match = props.options.find((option) => String(option.value) === String(props.modelValue));
    return match?.label ?? props.placeholder;
});

const isSelected = (value) => String(value) === String(props.modelValue);

const toggle = () => {
    if (props.disabled) {
        return;
    }

    isOpen.value = !isOpen.value;
};

const close = () => {
    isOpen.value = false;
};

const selectOption = (option) => {
    emit('update:modelValue', option.value);
    close();
};

const handleClickOutside = (event) => {
    if (!root.value?.contains(event.target)) {
        close();
    }
};

const handleEscape = (event) => {
    if (event.key === 'Escape') {
        close();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
});
</script>

<style scoped>
.portal-select {
    position: relative;
    width: 100%;
}

.portal-select__trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    min-height: 48px;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid rgba(var(--techguru-white-rgb), 0.12);
    background: rgba(var(--techguru-white-rgb), 0.04);
    color: var(--techguru-white);
    font-size: 15px;
    line-height: 1.4;
    text-align: left;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
}

.portal-select__trigger:focus {
    outline: none;
    border-color: var(--techguru-base);
}

.portal-select__trigger--error {
    border-color: #f1416c;
}

.portal-select__value {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.portal-select__value--placeholder {
    color: var(--techguru-gray);
}

.portal-select__icon {
    flex-shrink: 0;
    font-size: 12px;
    color: var(--techguru-gray);
    transition: transform 0.2s ease;
}

.portal-select__icon--open {
    transform: rotate(180deg);
}

.portal-select__menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    z-index: 50;
    margin: 0;
    padding: 6px;
    list-style: none;
    border-radius: 10px;
    border: 1px solid rgba(var(--techguru-white-rgb), 0.12);
    background: #12151c;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
    max-height: 240px;
    overflow-y: auto;
}

.portal-select__option {
    padding: 12px 14px;
    border-radius: 8px;
    color: var(--techguru-gray);
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
}

.portal-select__option:hover,
.portal-select__option--selected {
    background: rgba(var(--techguru-base-rgb), 0.15);
    color: var(--techguru-white);
}

.portal-select--disabled .portal-select__trigger {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
