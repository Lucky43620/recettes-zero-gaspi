<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();
const { formatRelativeTime } = useDateFormat();

defineProps({
    reports: Array,
});

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
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.recent_reports') }}</h2>
                <Link href="/admin/reports" class="text-sm text-green-600 hover:text-green-700">
                    {{ t('admin.view_all') }}
                </Link>
            </div>
        </div>
        <div class="divide-y">
            <div
                v-for="report in reports.slice(0, 5)"
                :key="report.id"
                class="p-4 hover:bg-gray-50"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            {{ report.reporter.name }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ getReasonLabel(report.reason) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ formatRelativeTime(report.created_at) }}
                        </p>
                    </div>
                    <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(report.status)]">
                        {{ getStatusLabel(report.status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
