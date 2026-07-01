<template>
    <div
        class="modal fade product-contact-modal"
        :id="modalId"
        tabindex="-1"
        :aria-labelledby="`${modalId}Label`"
        aria-hidden="true"
        ref="modalElement"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" :id="`${modalId}Label`">{{ title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" :aria-label="trans('Close')"></button>
                </div>
                <div class="modal-body pt-2">
                    <p v-if="description" class="product-contact-modal__description mb-4">{{ description }}</p>
                    <form @submit.prevent="handleSubmit" class="contact-one__form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h4 class="contact-one__input-title">{{ trans('Full Name') }}</h4>
                                <div class="contact-one__input-box">
                                    <div class="contact-one__input-icon">
                                        <span class="icon-user-1"></span>
                                    </div>
                                    <input
                                        v-model="contactForm.name"
                                        type="text"
                                        name="name"
                                        :placeholder="trans('Full Name')"
                                        :disabled="contactForm.processing"
                                        required
                                    >
                                </div>
                                <div v-if="contactForm.errors.name" class="text-danger mt-1 small">
                                    {{ contactForm.errors.name }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="contact-one__input-title">{{ trans('Email') }}</h4>
                                <div class="contact-one__input-box">
                                    <div class="contact-one__input-icon">
                                        <span class="icon-email"></span>
                                    </div>
                                    <input
                                        v-model="contactForm.email"
                                        type="email"
                                        name="email"
                                        :placeholder="trans('Email')"
                                        :disabled="contactForm.processing"
                                        required
                                    >
                                </div>
                                <div v-if="contactForm.errors.email" class="text-danger mt-1 small">
                                    {{ contactForm.errors.email }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="contact-one__input-title">{{ trans('Phone Number') }}</h4>
                                <div class="contact-one__input-box">
                                    <div class="contact-one__input-icon">
                                        <span class="icon-phone-call"></span>
                                    </div>
                                    <input
                                        v-model="contactForm.mobile"
                                        type="text"
                                        name="mobile"
                                        :placeholder="trans('Phone Number')"
                                        :disabled="contactForm.processing"
                                        required
                                    >
                                </div>
                                <div v-if="contactForm.errors.mobile" class="text-danger mt-1 small">
                                    {{ contactForm.errors.mobile }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="contact-one__input-title">{{ trans('Subject') }}</h4>
                                <div class="contact-one__input-box">
                                    <div class="contact-one__input-icon">
                                        <span class="icon-edit"></span>
                                    </div>
                                    <input
                                        v-model="contactForm.subject"
                                        type="text"
                                        name="subject"
                                        :placeholder="trans('Subject')"
                                        :disabled="contactForm.processing"
                                        required
                                    >
                                </div>
                                <div v-if="contactForm.errors.subject" class="text-danger mt-1 small">
                                    {{ contactForm.errors.subject }}
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <h4 class="contact-one__input-title">{{ trans('Message') }}</h4>
                                <div class="contact-one__input-box text-message-box">
                                    <div class="contact-one__input-icon">
                                        <span class="icon-edit"></span>
                                    </div>
                                    <textarea
                                        v-model="contactForm.message"
                                        name="message"
                                        :placeholder="trans('Message')"
                                        :disabled="contactForm.processing"
                                        required
                                    ></textarea>
                                </div>
                                <div v-if="contactForm.errors.message" class="text-danger mt-1 small">
                                    {{ contactForm.errors.message }}
                                </div>
                            </div>
                        </div>
                        <div v-if="submitSuccess" class="alert alert-success">
                            {{ trans('Thank you for contacting us! We will get back to you soon.') }}
                        </div>
                        <div class="contact-one__btn-box">
                            <button type="submit" class="thm-btn" :disabled="contactForm.processing">
                                <span v-if="contactForm.processing">{{ trans('Sending...') }}</span>
                                <span v-else>{{ submitLabel }}</span>
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    modalId: {
        type: String,
        default: 'productContactModal',
    },
    title: {
        type: String,
        default: 'Contact Us',
    },
    description: {
        type: String,
        default: '',
    },
    defaultSubject: {
        type: String,
        default: '',
    },
    defaultMessage: {
        type: String,
        default: '',
    },
    submitLabel: {
        type: String,
        default: 'Submit',
    },
})

const page = usePage()
const trans = (key) => page.props.translations[key] || key
const locale = computed(() => page.props.locale || 'en')

const modalElement = ref(null)
const submitSuccess = ref(false)
let modalInstance = null

const contactForm = useForm({
    name: '',
    email: '',
    mobile: '',
    subject: props.defaultSubject,
    message: props.defaultMessage,
})

watch(() => props.defaultSubject, (value) => {
    contactForm.subject = value
})

watch(() => props.defaultMessage, (value) => {
    contactForm.message = value
})

const handleSubmit = () => {
    if (contactForm.processing) {
        return
    }

    contactForm.post(route('contact-us.store'), {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
            submitSuccess.value = false
        },
        onSuccess: () => {
            submitSuccess.value = true
            contactForm.reset('name', 'email', 'mobile')
            contactForm.subject = props.defaultSubject
            contactForm.message = props.defaultMessage
            contactForm.clearErrors()
            setTimeout(() => {
                submitSuccess.value = false
                hide()
            }, 2500)
        },
        onError: () => {
            submitSuccess.value = false
        },
    })
}

const show = () => {
    contactForm.subject = props.defaultSubject
    contactForm.message = props.defaultMessage

    if (modalInstance) {
        modalInstance.show()
    }
}

const hide = () => {
    if (modalInstance) {
        modalInstance.hide()
    }
}

onMounted(() => {
    if (modalElement.value && window.bootstrap?.Modal) {
        modalInstance = new window.bootstrap.Modal(modalElement.value)
    }
})

defineExpose({
    show,
    hide,
})
</script>

<style scoped>
.product-contact-modal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
}

.product-contact-modal .modal-title {
    font-weight: 700;
    color: #0b192c;
}

.product-contact-modal .modal-body :deep(.text-muted) {
    color: #475569 !important;
}

.product-contact-modal__description {
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.25rem;
}

.product-contact-modal :deep(.contact-one__input-title) {
    color: #0b192c;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
}

.product-contact-modal :deep(.contact-one__input-box) {
    margin-bottom: 0;
}

.product-contact-modal :deep(.contact-one__input-box::before) {
    display: none;
}

.product-contact-modal :deep(.contact-one__input-box input[type="text"]),
.product-contact-modal :deep(.contact-one__input-box input[type="email"]),
.product-contact-modal :deep(.contact-one__input-box textarea) {
    background-color: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    border-radius: 12px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.product-contact-modal :deep(.contact-one__input-box input[type="text"]::placeholder),
.product-contact-modal :deep(.contact-one__input-box input[type="email"]::placeholder),
.product-contact-modal :deep(.contact-one__input-box textarea::placeholder) {
    color: #94a3b8;
    opacity: 1;
}

.product-contact-modal :deep(.contact-one__input-box input[type="text"]:focus),
.product-contact-modal :deep(.contact-one__input-box input[type="email"]:focus),
.product-contact-modal :deep(.contact-one__input-box textarea:focus) {
    background-color: #fff;
    border-color: #2189ca;
    box-shadow: 0 0 0 3px rgba(33, 137, 202, 0.15);
    outline: none;
}

.product-contact-modal :deep(.contact-one__input-icon span) {
    color: #64748b;
}

.product-contact-modal :deep(.contact-one__input-box:focus-within .contact-one__input-icon span) {
    color: #2189ca;
}

.product-contact-modal :deep(.contact-one__btn-box) {
    margin-top: 8px;
}
</style>
