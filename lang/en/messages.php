<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Success Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for success messages throughout
    | the application.
    |
    */

    'success' => [
        'created' => ':resource created successfully.',
        'updated' => ':resource updated successfully.',
        'deleted' => ':resource deleted successfully.',
        'saved' => ':resource saved successfully.',
        'restored' => ':resource restored successfully.',

        // Specific resources
        'recipe_created' => 'Recipe created successfully.',
        'recipe_updated' => 'Recipe updated successfully.',
        'recipe_deleted' => 'Recipe deleted successfully.',
        'recipe_published' => 'Recipe published successfully.',

        'comment_created' => 'Comment added successfully.',
        'comment_updated' => 'Comment updated successfully.',
        'comment_deleted' => 'Comment deleted successfully.',

        'rating_created' => 'Rating added successfully.',
        'rating_updated' => 'Rating updated successfully.',

        'favorite_added' => 'Added to favorites successfully.',
        'favorite_removed' => 'Removed from favorites successfully.',

        'collection_created' => 'Collection created successfully.',
        'collection_updated' => 'Collection updated successfully.',
        'collection_deleted' => 'Collection deleted successfully.',

        'report_created' => 'Report submitted successfully.',
        'report_updated' => 'Report updated successfully.',
        'report_resolved' => 'Report resolved successfully.',
        'report_dismissed' => 'Report dismissed successfully.',

        'notification_read' => 'Notification marked as read.',
        'notification_deleted' => 'Notification deleted successfully.',
        'notifications_read_all' => 'All notifications marked as read.',
        'notifications_deleted_all' => 'All notifications deleted successfully.',

        'event_created' => 'Event created successfully.',
        'event_updated' => 'Event updated successfully.',
        'event_deleted' => 'Event deleted successfully.',
        'event_joined' => 'You have joined the event successfully.',
        'event_left' => 'You have left the event successfully.',

        'profile_updated' => 'Profile updated successfully.',
        'password_changed' => 'Password changed successfully.',
        'email_verified' => 'Email verified successfully.',

        'follow_user' => 'You are now following this user.',
        'unfollow_user' => 'You are no longer following this user.',

        'settings_updated' => 'Settings updated successfully.',
        'preferences_saved' => 'Preferences saved successfully.',

        'subscription_created' => 'Subscription created successfully.',
        'subscription_updated' => 'Subscription updated successfully.',
        'subscription_canceled' => 'Subscription canceled. You can continue using premium features until :date.',
        'subscription_resumed' => 'Subscription resumed successfully.',
        'subscription_swapped' => 'Your subscription has been changed successfully.',

        'meal_plan_created' => 'Meal plan created successfully.',
        'meal_plan_updated' => 'Meal plan updated successfully.',
        'meal_plan_deleted' => 'Meal plan deleted successfully.',

        'shopping_list_created' => 'Shopping list created successfully.',
        'shopping_list_updated' => 'Shopping list updated successfully.',
        'shopping_list_deleted' => 'Shopping list deleted successfully.',

        'pantry_item_added' => 'Item added to pantry successfully.',
        'pantry_item_updated' => 'Pantry item updated successfully.',
        'pantry_item_deleted' => 'Item removed from pantry successfully.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for error messages throughout
    | the application.
    |
    */

    'error' => [
        'generic' => 'An error occurred. Please try again.',
        'not_found' => ':resource not found.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'forbidden' => 'Access denied.',
        'validation_failed' => 'The provided data is invalid.',
        'server_error' => 'Server error. Please try again later.',

        'create_failed' => 'Failed to create :resource.',
        'update_failed' => 'Failed to update :resource.',
        'delete_failed' => 'Failed to delete :resource.',
        'save_failed' => 'Failed to save :resource.',

        'recipe_not_found' => 'Recipe not found.',
        'recipe_create_failed' => 'Failed to create recipe.',
        'recipe_limit_reached' => 'You have reached the recipe limit for your plan.',

        'comment_not_found' => 'Comment not found.',
        'comment_unauthorized' => 'You cannot edit this comment.',

        'collection_not_found' => 'Collection not found.',
        'collection_limit_reached' => 'You have reached the collection limit.',

        'report_duplicate' => 'You have already reported this item.',
        'report_not_found' => 'Report not found.',

        'event_not_found' => 'Event not found.',
        'event_ended' => 'This event has ended.',
        'event_not_started' => 'This event has not started yet.',
        'event_already_joined' => 'You are already participating in this event.',

        'notification_not_found' => 'Notification not found.',

        'subscription_not_found' => 'Subscription not found.',
        'subscription_already_exists' => 'You already have an active subscription.',
        'subscription_payment_failed' => 'Payment failed. Please check your payment information.',
        'subscription_incomplete' => 'Payment was not completed.',

        'meal_plan_not_found' => 'Meal plan not found.',
        'meal_plan_limit_reached' => 'You have reached the meal plan limit.',

        'shopping_list_not_found' => 'Shopping list not found.',
        'shopping_list_limit_reached' => 'You have reached the shopping list limit.',

        'pantry_item_not_found' => 'Item not found in pantry.',

        'user_not_found' => 'User not found.',
        'already_following' => 'You are already following this user.',
        'not_following' => 'You are not following this user.',
        'cannot_follow_self' => 'You cannot follow yourself.',

        'file_upload_failed' => 'File upload failed.',
        'file_too_large' => 'File is too large.',
        'invalid_file_type' => 'Invalid file type.',

        'email_not_verified' => 'Please verify your email address.',
        'email_already_exists' => 'This email address is already in use.',

        'invalid_credentials' => 'Invalid credentials.',
        'account_disabled' => 'Your account has been disabled.',

        'rate_limit_exceeded' => 'Too many attempts. Please try again later.',

        'config_not_available' => 'Configuration not available.',
        'feature_not_available' => 'This feature is not available.',
        'premium_required' => 'This feature requires a premium subscription.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Info Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for informational messages.
    |
    */

    'info' => [
        'no_results' => 'No results found.',
        'no_recipes' => 'No recipes found.',
        'no_comments' => 'No comments yet.',
        'no_notifications' => 'No notifications.',
        'no_favorites' => 'No favorites yet.',
        'no_collections' => 'No collections yet.',
        'no_events' => 'No events yet.',
        'no_meal_plans' => 'No meal plans yet.',
        'no_shopping_lists' => 'No shopping lists yet.',
        'no_pantry_items' => 'Your pantry is empty.',
        'no_followers' => 'No followers yet.',
        'no_following' => 'You are not following anyone yet.',

        'email_sent' => 'Email sent successfully.',
        'processing' => 'Processing...',
        'loading' => 'Loading...',

        'subscription_inactive' => 'You do not have an active subscription.',
        'subscription_trial' => 'You are on trial until :date.',
        'subscription_expiring' => 'Your subscription expires on :date.',

        'feature_coming_soon' => 'This feature will be available soon.',
        'under_maintenance' => 'This section is under maintenance.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Confirmation Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for confirmation dialogs.
    |
    */

    'confirm' => [
        'delete' => 'Are you sure you want to delete :resource?',
        'delete_permanent' => 'This action is irreversible. Are you sure?',
        'cancel_subscription' => 'Are you sure you want to cancel your subscription?',
        'leave_event' => 'Are you sure you want to leave this event?',
        'delete_account' => 'Are you sure you want to delete your account? This action is irreversible.',
        'unsaved_changes' => 'You have unsaved changes. Do you want to continue?',
    ],
];
