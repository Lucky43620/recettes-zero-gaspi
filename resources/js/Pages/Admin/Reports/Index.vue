<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();

const props = defineProps({
    reports: Object,
});

const { formatRelativeTime } = useDateFormat();

const statusForm = useForm({
    status: '',
    resolution_note: '',
});

const selectedReport = ref(null);

const updateStatus = (report, newStatus) => {
    statusForm.status = newStatus;
    statusForm.put(route('reports.update', report.id), {
        preserveScroll: true,
        onSuccess: () => {
            selectedReport.value = null;
            statusForm.reset();
        },
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        reviewing: 'bg-blue-100 text-blue-800',
        resolved: 'bg-green-100 text-green-800',
        dismissed: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || colors.pending;
};

const getStatusLabel = (status) => {
    return t(`admin.report_status.${status}`);
};

const getReasonLabel = (reason) => {
    return t(`admin.report_reason.${reason}`);
};
</script>

<template>
    <Head :title="t('admin.reports_title')" />

    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('admin.reports') }}</h1>
                <p class="mt-2 text-gray-600">{{ t('admin.manage_reports_description') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.reporter_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.type_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.reason_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.description_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('common.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.date_column') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="report in reports.data" :key="report.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div v-if="report.reporter" class="flex items-center">
                                        <img :src="report.reporter.profile_photo_url" :alt="report.reporter.name" class="w-8 h-8 rounded-full" />
                                        <span class="ml-3 text-sm font-medium text-gray-900">{{ report.reporter.name }}</span>
                                    </div>
                                    <div v-else class="text-sm text-gray-400 italic">
                                        {{ t('common.deleted_user') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ report.reportable_type ? report.reportable_type.split('\\').pop() : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span v-if="report.reason" class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                        {{ getReasonLabel(report.reason) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ report.description || '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span v-if="report.status" :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(report.status)]">
                                        {{ getStatusLabel(report.status) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ report.created_at ? formatRelativeTime(report.created_at) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                    <button
                                        v-if="report.status === 'pending'"
                                        @click="updateStatus(report, 'reviewing')"
                                        class="text-blue-600 hover:text-blue-700"
                                    >
                                        {{ t('admin.mark_reviewing') }}
                                    </button>
                                    <button
                                        v-if="report.status !== 'resolved'"
                                        @click="updateStatus(report, 'resolved')"
                                        class="text-green-600 hover:text-green-700"
                                    >
                                        {{ t('admin.resolve') }}
                                    </button>
                                    <button
                                        v-if="report.status !== 'dismissed'"
                                        @click="updateStatus(report, 'dismissed')"
                                        class="text-gray-600 hover:text-gray-700"
                                    >
                                        {{ t('admin.dismiss') }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden divide-y divide-gray-200">
                    <div v-for="report in reports.data" :key="report.id" class="p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center flex-1">
                                <img v-if="report.reporter" :src="report.reporter.profile_photo_url" :alt="report.reporter.name" class="w-10 h-10 rounded-full" />
                                <div class="ml-3 flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ report.reporter?.name || t('common.deleted_user') }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ report.created_at ? formatRelativeTime(report.created_at) : '-' }}</div>
                                </div>
                            </div>
                            <span v-if="report.status" :class="['px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap', getStatusColor(report.status)]">
                                {{ getStatusLabel(report.status) }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ t('admin.type_column') }}:</span>
                                <span class="text-gray-900 font-medium">{{ report.reportable_type ? report.reportable_type.split('\\').pop() : '-' }}</span>
                            </div>
                            <div v-if="report.reason" class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ t('admin.reason_column') }}:</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                    {{ getReasonLabel(report.reason) }}
                                </span>
                            </div>
                            <div v-if="report.description" class="pt-2 border-t">
                                <div class="text-xs text-gray-500 mb-1">{{ t('admin.description_column') }}:</div>
                                <div class="text-sm text-gray-700 line-clamp-2">{{ report.description }}</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-3 border-t">
                            <button
                                v-if="report.status === 'pending'"
                                @click="updateStatus(report, 'reviewing')"
                                class="flex-1 min-w-[100px] px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition"
                            >
                                {{ t('admin.mark_reviewing') }}
                            </button>
                            <button
                                v-if="report.status !== 'resolved'"
                                @click="updateStatus(report, 'resolved')"
                                class="flex-1 min-w-[100px] px-3 py-2 text-sm text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition"
                            >
                                {{ t('admin.resolve') }}
                            </button>
                            <button
                                v-if="report.status !== 'dismissed'"
                                @click="updateStatus(report, 'dismissed')"
                                class="flex-1 min-w-[100px] px-3 py-2 text-sm text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                            >
                                {{ t('admin.dismiss') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="reports.links" class="px-4 md:px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs sm:text-sm text-gray-500">
                        {{ reports.from }} - {{ reports.to }} {{ t('admin.pagination_of') }} {{ reports.total }} {{ t('admin.reports_count') }}
                    </div>
                    <div class="flex flex-wrap gap-1 sm:gap-2 justify-center">
                        <Link
                            v-for="(link, index) in reports.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm transition',
                                link.active
                                    ? 'bg-green-600 text-white'
                                    : link.url
                                    ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                            ]"
                        >
                            <span v-if="link.label === 'pagination.previous'">{{ t('common.previous') }}</span>
                            <span v-else-if="link.label === 'pagination.next'">{{ t('common.next') }}</span>
                            <span v-else v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
