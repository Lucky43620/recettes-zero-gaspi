<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none"
    >
      <BellIcon class="w-6 h-6" />
      <span
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
    >
      <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">{{ t('notifications.title') }}</h3>
        <Link
          v-if="unreadCount > 0"
          :href="route('notifications.read-all')"
          method="post"
          as="button"
          class="text-xs text-blue-600 hover:text-blue-800"
        >
          {{ t('notifications.mark_all_as_read') }}
        </Link>
      </div>

      <div class="max-h-96 overflow-y-auto">
        <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500">
          {{ t('notifications.no_notifications') }}
        </div>

        <Link
          v-for="notification in notifications"
          :key="notification.id"
          :href="notification.data.action_url"
          @click="markAsRead(notification.id)"
          class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition"
          :class="{ 'bg-blue-50': !notification.read_at }"
        >
          <div class="flex items-start gap-3">
            <div
              class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
              :class="getNotificationColor(notification.data.type)"
            >
              <component :is="getNotificationIcon(notification.data.type)" class="w-5 h-5 text-white" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900">
                {{ notification.data.title }}
              </p>
              <p class="text-xs text-gray-600 mt-1">
                {{ notification.data.message }}
              </p>
              <p class="text-xs text-gray-400 mt-1">
                {{ formatDate(notification.created_at) }}
              </p>
            </div>
          </div>
        </Link>
      </div>

      <div class="p-3 border-t border-gray-200 text-center">
        <Link
          :href="route('notifications.index')"
          class="text-sm text-blue-600 hover:text-blue-800 font-medium"
        >
          {{ t('notifications.see_all') }}
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { BellIcon, ChatBubbleLeftIcon, UserPlusIcon, ClockIcon } from '@heroicons/vue/24/outline'

const { t } = useI18n()
const page = usePage()
const isOpen = ref(false)

const notifications = computed(() => page.props.auth.user?.notifications?.slice(0, 5) || [])
const unreadCount = computed(() => page.props.auth.user?.unread_notifications_count || 0)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const markAsRead = (notificationId) => {
  router.post(route('notifications.read', notificationId), {}, {
    preserveScroll: true,
    only: ['auth']
  })
  isOpen.value = false
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
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (minutes < 1) return t('notifications.just_now')
  if (minutes < 60) return t('notifications.minutes_ago', { count: minutes })
  if (hours < 24) return t('notifications.hours_ago', { count: hours })
  if (days < 7) return t('notifications.days_ago', { count: days })
  return date.toLocaleDateString('fr-FR')
}
</script>
