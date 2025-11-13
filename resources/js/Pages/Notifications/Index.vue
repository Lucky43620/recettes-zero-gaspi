<template>
  <AppLayout :title="t('notifications.title')">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('notifications.title') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">
                {{ t('notifications.all_notifications') }}
              </h3>
              <p class="text-sm text-gray-600 mt-1">
                {{ t('notifications.unread_count', { count: unreadCount }) }}
              </p>
            </div>
            <Link
              v-if="unreadCount > 0"
              :href="route('notifications.read-all')"
              method="post"
              as="button"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
            >
              {{ t('notifications.mark_all_as_read') }}
            </Link>
          </div>

          <div v-if="!notifications || !notifications.data || notifications.data.length === 0" class="p-12 text-center">
            <BellIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <p class="text-gray-500 text-lg">{{ t('notifications.no_notifications') }}</p>
          </div>

          <div v-else class="divide-y divide-gray-200">
            <div
              v-for="notification in notifications.data"
              :key="notification.id"
              class="p-6 hover:bg-gray-50 transition"
              :class="{ 'bg-blue-50': !notification.read_at }"
            >
              <div class="flex items-start justify-between gap-4">
                <Link
                  :href="notification.data.action_url"
                  @click="markAsRead(notification.id)"
                  class="flex-1 flex items-start gap-4"
                >
                  <div class="flex-shrink-0">
                    <div
                      class="w-12 h-12 rounded-full flex items-center justify-center"
                      :class="getNotificationColor(notification.data.type)"
                    >
                      <component :is="getNotificationIcon(notification.data.type)" class="w-6 h-6 text-white" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-base font-medium text-gray-900 mb-1">
                      {{ notification.data.title }}
                    </p>
                    <p class="text-sm text-gray-600 mb-2">
                      {{ notification.data.message }}
                    </p>
                    <p class="text-xs text-gray-400">
                      {{ formatDate(notification.created_at) }}
                    </p>
                  </div>
                </Link>

                <button
                  @click="deleteNotification(notification.id)"
                  class="flex-shrink-0 p-2 text-gray-400 hover:text-red-600 transition"
                  :title="t('common.delete')"
                >
                  <TrashIcon class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>

          <div v-if="notifications.last_page > 1" class="p-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <Link
                v-if="notifications.prev_page_url"
                :href="notifications.prev_page_url"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
              >
                {{ t('common.previous') }}
              </Link>
              <span class="text-sm text-gray-600">
                {{ t('notifications.page_of', { current: notifications.current_page, total: notifications.last_page }) }}
              </span>
              <Link
                v-if="notifications.next_page_url"
                :href="notifications.next_page_url"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
              >
                {{ t('common.next') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { BellIcon, ChatBubbleLeftIcon, UserPlusIcon, ClockIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  notifications: Object,
  unreadCount: Number
})

const markAsRead = (notificationId) => {
  router.post(route('notifications.read', notificationId), {}, {
    preserveScroll: true
  })
}

const deleteNotification = (notificationId) => {
  if (confirm(t('notifications.delete_confirmation'))) {
    router.delete(route('notifications.destroy', notificationId), {
      preserveScroll: true
    })
  }
}

const getNotificationIcon = (type) => {
  const icons = {
    comment_reply: ChatBubbleLeftIcon,
    new_follower: UserPlusIcon,
    meal_plan_reminder: ClockIcon,
    expiring_items: ClockIcon
  }
  return icons[type] || BellIcon
}

const getNotificationColor = (type) => {
  const colors = {
    comment_reply: 'bg-blue-500',
    new_follower: 'bg-green-500',
    meal_plan_reminder: 'bg-orange-500',
    expiring_items: 'bg-red-500'
  }
  return colors[type] || 'bg-gray-500'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
