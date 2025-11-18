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
        'created' => ':resource créé(e) avec succès.',
        'updated' => ':resource mis(e) à jour avec succès.',
        'deleted' => ':resource supprimé(e) avec succès.',
        'saved' => ':resource enregistré(e) avec succès.',
        'restored' => ':resource restauré(e) avec succès.',

        // Specific resources
        'recipe_created' => 'Recette créée avec succès.',
        'recipe_updated' => 'Recette mise à jour avec succès.',
        'recipe_deleted' => 'Recette supprimée avec succès.',
        'recipe_published' => 'Recette publiée avec succès.',

        'comment_created' => 'Commentaire ajouté avec succès.',
        'comment_updated' => 'Commentaire modifié avec succès.',
        'comment_deleted' => 'Commentaire supprimé avec succès.',

        'rating_created' => 'Note ajoutée avec succès.',
        'rating_updated' => 'Note modifiée avec succès.',

        'favorite_added' => 'Ajouté aux favoris avec succès.',
        'favorite_removed' => 'Retiré des favoris avec succès.',

        'collection_created' => 'Collection créée avec succès.',
        'collection_updated' => 'Collection mise à jour avec succès.',
        'collection_deleted' => 'Collection supprimée avec succès.',

        'report_created' => 'Signalement envoyé avec succès.',
        'report_updated' => 'Signalement mis à jour avec succès.',
        'report_resolved' => 'Signalement résolu avec succès.',
        'report_dismissed' => 'Signalement rejeté avec succès.',

        'notification_read' => 'Notification marquée comme lue.',
        'notification_deleted' => 'Notification supprimée avec succès.',
        'notifications_read_all' => 'Toutes les notifications ont été marquées comme lues.',
        'notifications_deleted_all' => 'Toutes les notifications ont été supprimées.',

        'event_created' => 'Événement créé avec succès.',
        'event_updated' => 'Événement mis à jour avec succès.',
        'event_deleted' => 'Événement supprimé avec succès.',
        'event_joined' => 'Vous avez rejoint l\'événement avec succès.',
        'event_left' => 'Vous avez quitté l\'événement avec succès.',

        'profile_updated' => 'Profil mis à jour avec succès.',
        'password_changed' => 'Mot de passe modifié avec succès.',
        'email_verified' => 'Adresse email vérifiée avec succès.',

        'follow_user' => 'Vous suivez maintenant cet utilisateur.',
        'unfollow_user' => 'Vous ne suivez plus cet utilisateur.',

        'settings_updated' => 'Paramètres mis à jour avec succès.',
        'preferences_saved' => 'Préférences enregistrées avec succès.',

        'subscription_created' => 'Abonnement créé avec succès.',
        'subscription_updated' => 'Abonnement mis à jour avec succès.',
        'subscription_canceled' => 'Abonnement annulé. Vous pourrez continuer à utiliser les fonctionnalités premium jusqu\'à :date.',
        'subscription_resumed' => 'Abonnement réactivé avec succès.',
        'subscription_swapped' => 'Votre abonnement a été modifié avec succès.',

        'meal_plan_created' => 'Planning de repas créé avec succès.',
        'meal_plan_updated' => 'Planning de repas mis à jour avec succès.',
        'meal_plan_deleted' => 'Planning de repas supprimé avec succès.',

        'shopping_list_created' => 'Liste de courses créée avec succès.',
        'shopping_list_updated' => 'Liste de courses mise à jour avec succès.',
        'shopping_list_deleted' => 'Liste de courses supprimée avec succès.',

        'pantry_item_added' => 'Ingrédient ajouté au garde-manger avec succès.',
        'pantry_item_updated' => 'Ingrédient du garde-manger mis à jour avec succès.',
        'pantry_item_deleted' => 'Ingrédient retiré du garde-manger avec succès.',
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
        'generic' => 'Une erreur est survenue. Veuillez réessayer.',
        'not_found' => ':resource non trouvé(e).',
        'unauthorized' => 'Vous n\'êtes pas autorisé(e) à effectuer cette action.',
        'forbidden' => 'Accès refusé.',
        'validation_failed' => 'Les données fournies sont invalides.',
        'server_error' => 'Erreur serveur. Veuillez réessayer plus tard.',

        'create_failed' => 'Échec de la création de :resource.',
        'update_failed' => 'Échec de la mise à jour de :resource.',
        'delete_failed' => 'Échec de la suppression de :resource.',
        'save_failed' => 'Échec de l\'enregistrement de :resource.',

        'recipe_not_found' => 'Recette non trouvée.',
        'recipe_create_failed' => 'Échec de la création de la recette.',
        'recipe_limit_reached' => 'Vous avez atteint la limite de recettes pour votre plan.',

        'comment_not_found' => 'Commentaire non trouvé.',
        'comment_unauthorized' => 'Vous ne pouvez pas modifier ce commentaire.',

        'collection_not_found' => 'Collection non trouvée.',
        'collection_limit_reached' => 'Vous avez atteint la limite de collections.',

        'report_duplicate' => 'Vous avez déjà signalé cet élément.',
        'report_not_found' => 'Signalement non trouvé.',

        'event_not_found' => 'Événement non trouvé.',
        'event_ended' => 'Cet événement est terminé.',
        'event_not_started' => 'Cet événement n\'a pas encore commencé.',
        'event_already_joined' => 'Vous participez déjà à cet événement.',

        'notification_not_found' => 'Notification non trouvée.',

        'subscription_not_found' => 'Abonnement non trouvé.',
        'subscription_already_exists' => 'Vous avez déjà un abonnement actif.',
        'subscription_payment_failed' => 'Le paiement a échoué. Veuillez vérifier vos informations de paiement.',
        'subscription_incomplete' => 'Le paiement n\'a pas été finalisé.',

        'meal_plan_not_found' => 'Planning de repas non trouvé.',
        'meal_plan_limit_reached' => 'Vous avez atteint la limite de plannings de repas.',

        'shopping_list_not_found' => 'Liste de courses non trouvée.',
        'shopping_list_limit_reached' => 'Vous avez atteint la limite de listes de courses.',

        'pantry_item_not_found' => 'Ingrédient non trouvé dans le garde-manger.',

        'user_not_found' => 'Utilisateur non trouvé.',
        'already_following' => 'Vous suivez déjà cet utilisateur.',
        'not_following' => 'Vous ne suivez pas cet utilisateur.',
        'cannot_follow_self' => 'Vous ne pouvez pas vous suivre vous-même.',

        'file_upload_failed' => 'Échec du téléchargement du fichier.',
        'file_too_large' => 'Le fichier est trop volumineux.',
        'invalid_file_type' => 'Type de fichier invalide.',

        'email_not_verified' => 'Veuillez vérifier votre adresse email.',
        'email_already_exists' => 'Cette adresse email est déjà utilisée.',

        'invalid_credentials' => 'Identifiants invalides.',
        'account_disabled' => 'Votre compte a été désactivé.',

        'rate_limit_exceeded' => 'Trop de tentatives. Veuillez réessayer plus tard.',

        'config_not_available' => 'Configuration non disponible.',
        'feature_not_available' => 'Cette fonctionnalité n\'est pas disponible.',
        'premium_required' => 'Cette fonctionnalité nécessite un abonnement premium.',
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
        'no_results' => 'Aucun résultat trouvé.',
        'no_recipes' => 'Aucune recette trouvée.',
        'no_comments' => 'Aucun commentaire pour le moment.',
        'no_notifications' => 'Aucune notification.',
        'no_favorites' => 'Aucun favori pour le moment.',
        'no_collections' => 'Aucune collection pour le moment.',
        'no_events' => 'Aucun événement pour le moment.',
        'no_meal_plans' => 'Aucun planning de repas pour le moment.',
        'no_shopping_lists' => 'Aucune liste de courses pour le moment.',
        'no_pantry_items' => 'Votre garde-manger est vide.',
        'no_followers' => 'Aucun abonné pour le moment.',
        'no_following' => 'Vous ne suivez personne pour le moment.',

        'email_sent' => 'Email envoyé avec succès.',
        'processing' => 'Traitement en cours...',
        'loading' => 'Chargement...',

        'subscription_inactive' => 'Vous n\'avez pas d\'abonnement actif.',
        'subscription_trial' => 'Vous êtes en période d\'essai jusqu\'au :date.',
        'subscription_expiring' => 'Votre abonnement expire le :date.',

        'feature_coming_soon' => 'Cette fonctionnalité sera bientôt disponible.',
        'under_maintenance' => 'Cette section est en maintenance.',
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
        'delete' => 'Êtes-vous sûr(e) de vouloir supprimer :resource ?',
        'delete_permanent' => 'Cette action est irréversible. Êtes-vous sûr(e) ?',
        'cancel_subscription' => 'Êtes-vous sûr(e) de vouloir annuler votre abonnement ?',
        'leave_event' => 'Êtes-vous sûr(e) de vouloir quitter cet événement ?',
        'delete_account' => 'Êtes-vous sûr(e) de vouloir supprimer votre compte ? Cette action est irréversible.',
        'unsaved_changes' => 'Vous avez des modifications non enregistrées. Voulez-vous continuer ?',
    ],
];
