<template>
    <portal-shell
        :title="t('projects.title')"
        :subtitle="t('projects.subtitle')"
        active="projects"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('projects.title') },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div v-if="projects.data.length === 0" class="portal-panel">
            <div class="portal-empty">
                <i class="fas fa-folder-open"></i>
                {{ t('projects.no_projects') }}
            </div>
        </div>

        <div v-else class="row g-4">
            <div v-for="project in projects.data" :key="project.id" class="col-md-6 col-xl-4">
                <article class="portal-project-card">
                    <div class="portal-project-card__top">
                        <h3 class="portal-project-card__title">{{ project.title }}</h3>
                        <span
                            class="portal-badge"
                            :style="{ backgroundColor: (project.status?.color_code || '#6c757d') + '33', color: project.status?.color_code || '#C5C8CD' }"
                        >
                            {{ project.status?.name }}
                        </span>
                    </div>

                    <div v-if="project.company?.name" class="portal-project-card__company">
                        <i class="fas fa-building me-1"></i>{{ project.company.name }}
                    </div>

                    <div class="portal-project-card__meta">
                        <span>{{ t('fields.payment_status') }}</span>
                        <strong>{{ paymentStatusLabel(project.payment_status) }}</strong>
                    </div>
                    <div class="portal-project-card__meta">
                        <span>{{ t('fields.remaining') }}</span>
                        <strong>{{ formatMoney(project.collection.remaining, project.collection.currency) }}</strong>
                    </div>

                    <div class="mb-3">
                        <div class="portal-progress">
                            <div
                                class="portal-progress__bar"
                                :style="{ width: `${project.collection.collection_rate}%` }"
                            ></div>
                        </div>
                    </div>

                    <div class="portal-project-card__footer">
                        <Link :href="route('portal.projects.show', project.id)" class="thm-btn w-100 text-center">
                            {{ t('projects.view_details') }}
                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                        </Link>
                    </div>
                </article>
            </div>
        </div>

        <nav v-if="projects.links?.length > 3" class="portal-pagination" aria-label="Pagination">
            <Link
                v-for="link in projects.links"
                :key="link.label"
                :href="link.url || '#'"
                class="portal-pagination__link"
                :class="{ 'portal-pagination__link--active': link.active }"
                v-html="link.label"
            />
        </nav>
    </portal-shell>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    projects: { type: Object, required: true },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t, paymentStatusLabel } = usePortalTranslations();
const locale = computed(() => page.props.locale);
const metaTitle = computed(() => props.meta?.title || t('pages.projects_title'));
const metaDescription = computed(() => props.meta?.description || t('pages.projects_description'));

const formatMoney = (amount, currency) => `${Number(amount).toFixed(2)} ${currency || ''}`.trim();
</script>
