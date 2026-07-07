<template>
    <portal-shell
        :title="t('profile.title')"
        :subtitle="t('profile.subtitle')"
        active="profile"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('profile.title') },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div v-if="flash.success" class="portal-alert portal-alert--success" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ flash.success }}
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('profile.information') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <form class="portal-profile-form" @submit.prevent="submitProfile">
                            <div class="portal-profile-avatar">
                                <div class="portal-profile-avatar__preview">
                                    <img :src="avatarPreview || user.avatar" :alt="t('profile.avatar')">
                                </div>
                                <div class="portal-profile-avatar__actions">
                                    <label class="portal-profile-avatar__upload thm-btn thm-btn--sm">
                                        <i class="fas fa-camera"></i>
                                        {{ t('profile.change_photo') }}
                                        <input
                                            type="file"
                                            accept="image/jpeg,image/png,image/jpg,image/webp"
                                            class="d-none"
                                            @change="onAvatarChange"
                                        >
                                    </label>
                                    <button
                                        v-if="avatarPreview"
                                        type="button"
                                        class="portal-panel__action"
                                        @click="clearAvatar"
                                    >
                                        {{ t('profile.remove_photo') }}
                                    </button>
                                </div>
                                <p v-if="profileForm.errors.avatar" class="portal-form-error">{{ profileForm.errors.avatar }}</p>
                            </div>

                            <div class="portal-form-group">
                                <label for="profile-name">{{ t('profile.name') }} *</label>
                                <input
                                    id="profile-name"
                                    v-model="profileForm.name"
                                    type="text"
                                    class="portal-input"
                                    :class="{ 'portal-input--error': profileForm.errors.name }"
                                    required
                                >
                                <p v-if="profileForm.errors.name" class="portal-form-error">{{ profileForm.errors.name }}</p>
                            </div>

                            <div class="portal-form-group">
                                <label for="profile-email">{{ t('profile.email') }} *</label>
                                <input
                                    id="profile-email"
                                    v-model="profileForm.email"
                                    type="email"
                                    class="portal-input"
                                    :class="{ 'portal-input--error': profileForm.errors.email }"
                                    required
                                >
                                <p v-if="profileForm.errors.email" class="portal-form-error">{{ profileForm.errors.email }}</p>
                            </div>

                            <div class="portal-form-group">
                                <label for="profile-mobile">{{ t('profile.mobile') }}</label>
                                <input
                                    id="profile-mobile"
                                    v-model="profileForm.mobile"
                                    type="text"
                                    class="portal-input"
                                    :class="{ 'portal-input--error': profileForm.errors.mobile }"
                                >
                                <p v-if="profileForm.errors.mobile" class="portal-form-error">{{ profileForm.errors.mobile }}</p>
                            </div>

                            <div class="portal-profile-form__actions">
                                <button type="submit" class="thm-btn" :disabled="profileForm.processing">
                                    {{ t('profile.save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="portal-panel mb-4">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('profile.change_password') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <form class="portal-profile-form" @submit.prevent="submitPassword">
                            <div class="portal-form-group">
                                <label for="current-password">{{ t('profile.current_password') }} *</label>
                                <input
                                    id="current-password"
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="portal-input"
                                    :class="{ 'portal-input--error': passwordForm.errors.current_password }"
                                    required
                                    autocomplete="current-password"
                                >
                                <p v-if="passwordForm.errors.current_password" class="portal-form-error">
                                    {{ passwordForm.errors.current_password }}
                                </p>
                            </div>

                            <div class="portal-form-group">
                                <label for="new-password">{{ t('profile.new_password') }} *</label>
                                <input
                                    id="new-password"
                                    v-model="passwordForm.password"
                                    type="password"
                                    class="portal-input"
                                    :class="{ 'portal-input--error': passwordForm.errors.password }"
                                    required
                                    autocomplete="new-password"
                                >
                                <p v-if="passwordForm.errors.password" class="portal-form-error">{{ passwordForm.errors.password }}</p>
                            </div>

                            <div class="portal-form-group">
                                <label for="confirm-password">{{ t('profile.confirm_password') }} *</label>
                                <input
                                    id="confirm-password"
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    class="portal-input"
                                    required
                                    autocomplete="new-password"
                                >
                            </div>

                            <div class="portal-profile-form__actions">
                                <button type="submit" class="thm-btn" :disabled="passwordForm.processing">
                                    {{ t('profile.update_password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('profile.two_factor') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <p class="portal-profile-2fa__text">{{ t('profile.two_factor_description') }}</p>

                        <div v-if="twoFactorEnabled" class="portal-profile-2fa__status">
                            <span class="portal-badge portal-badge--paid">{{ t('profile.two_factor_enabled') }}</span>
                        </div>
                        <div v-else-if="twoFactorPending" class="portal-profile-2fa__status">
                            <span class="portal-badge portal-badge--invoice">{{ t('profile.two_factor_pending') }}</span>
                        </div>

                        <div v-if="twoFactorEnabled || twoFactorPending" class="portal-profile-2fa__tools">
                            <button type="button" class="portal-panel__action" :disabled="twoFactorLoading" @click="loadQrCode">
                                <i class="fas fa-qrcode"></i>
                                {{ t('profile.show_qr') }}
                            </button>
                            <button
                                v-if="twoFactorEnabled"
                                type="button"
                                class="portal-panel__action"
                                :disabled="twoFactorLoading"
                                @click="loadRecoveryCodes"
                            >
                                <i class="fas fa-key"></i>
                                {{ t('profile.show_recovery_codes') }}
                            </button>
                        </div>

                        <div v-if="qrSvg" class="portal-profile-2fa__qr" v-html="qrSvg"></div>

                        <div v-if="recoveryCodes.length" class="portal-profile-2fa__codes">
                            <p class="portal-form-hint">{{ t('profile.recovery_codes_hint') }}</p>
                            <ul>
                                <li v-for="code in recoveryCodes" :key="code"><code>{{ code }}</code></li>
                            </ul>
                        </div>

                        <form
                            v-if="twoFactorPending"
                            class="portal-profile-2fa__confirm"
                            @submit.prevent="confirmTwoFactor"
                        >
                            <div class="portal-form-group">
                                <label for="two-factor-code">{{ t('profile.confirm_code') }} *</label>
                                <input
                                    id="two-factor-code"
                                    v-model="twoFactorCode"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="6"
                                    class="portal-input"
                                    :placeholder="t('profile.confirm_code_placeholder')"
                                    required
                                >
                            </div>
                            <button type="submit" class="thm-btn thm-btn--sm" :disabled="twoFactorLoading">
                                {{ t('profile.confirm_two_factor') }}
                            </button>
                        </form>

                        <p v-if="twoFactorError" class="portal-form-error">{{ twoFactorError }}</p>

                        <div class="portal-profile-2fa__actions">
                            <button
                                v-if="twoFactorEnabled || twoFactorPending"
                                type="button"
                                class="portal-profile-2fa__disable"
                                :disabled="twoFactorLoading"
                                @click="disableTwoFactor"
                            >
                                {{ t('profile.disable_two_factor') }}
                            </button>
                            <button
                                v-else
                                type="button"
                                class="thm-btn"
                                :disabled="twoFactorLoading"
                                @click="enableTwoFactor"
                            >
                                {{ t('profile.enable_two_factor') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </portal-shell>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    user: { type: Object, required: true },
    twoFactorEnabled: { type: Boolean, default: false },
    twoFactorPending: { type: Boolean, default: false },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t } = usePortalTranslations();
const flash = computed(() => page.props.flash || {});
const metaTitle = computed(() => props.meta?.title || t('pages.profile_title'));
const metaDescription = computed(() => props.meta?.description || t('pages.profile_description'));

const avatarPreview = ref(null);
const twoFactorLoading = ref(false);
const twoFactorError = ref('');
const qrSvg = ref('');
const recoveryCodes = ref([]);
const twoFactorCode = ref('');

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    mobile: props.user.mobile || '',
    avatar: null,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const onAvatarChange = (event) => {
    const file = event.target.files[0] || null;
    profileForm.avatar = file;

    if (file) {
        avatarPreview.value = URL.createObjectURL(file);
    }
};

const clearAvatar = () => {
    profileForm.avatar = null;
    avatarPreview.value = null;
};

const submitProfile = () => {
    profileForm.post(route('portal.profile.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};

const submitPassword = () => {
    passwordForm.put(route('portal.profile.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

const fortifyHeaders = () => ({
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': page.props.csrf,
});

const loadQrCode = async () => {
    twoFactorLoading.value = true;
    twoFactorError.value = '';

    try {
        const response = await fetch(route('two-factor.qr-code'), {
            headers: fortifyHeaders(),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error();
        }

        const data = await response.json();
        qrSvg.value = data.svg || '';
    } catch (error) {
        twoFactorError.value = t('profile.two_factor_error');
    } finally {
        twoFactorLoading.value = false;
    }
};

const loadRecoveryCodes = async () => {
    twoFactorLoading.value = true;
    twoFactorError.value = '';

    try {
        const response = await fetch(route('two-factor.recovery-codes'), {
            headers: fortifyHeaders(),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error();
        }

        const data = await response.json();
        recoveryCodes.value = data.recoveryCodes || [];
    } catch (error) {
        twoFactorError.value = t('profile.two_factor_error');
    } finally {
        twoFactorLoading.value = false;
    }
};

const enableTwoFactor = () => {
    twoFactorLoading.value = true;
    twoFactorError.value = '';

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onFinish: () => {
            twoFactorLoading.value = false;
        },
        onError: () => {
            twoFactorError.value = t('profile.two_factor_error');
        },
    });
};

const disableTwoFactor = () => {
    if (!window.confirm(t('profile.disable_confirm'))) {
        return;
    }

    twoFactorLoading.value = true;
    twoFactorError.value = '';

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onFinish: () => {
            twoFactorLoading.value = false;
            qrSvg.value = '';
            recoveryCodes.value = [];
            twoFactorCode.value = '';
        },
        onError: () => {
            twoFactorError.value = t('profile.two_factor_error');
        },
    });
};

const confirmTwoFactor = () => {
    twoFactorLoading.value = true;
    twoFactorError.value = '';

    router.post(route('two-factor.confirm'), { code: twoFactorCode.value }, {
        preserveScroll: true,
        onSuccess: () => {
            twoFactorCode.value = '';
            loadQrCode();
        },
        onFinish: () => {
            twoFactorLoading.value = false;
        },
        onError: () => {
            twoFactorError.value = t('profile.confirm_code_error');
        },
    });
};

if (props.twoFactorPending) {
    loadQrCode();
}
</script>
