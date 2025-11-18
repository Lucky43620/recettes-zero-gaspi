<template>
  <PublicLayout :title="t('auth.terms_of_service')">
    <div class="py-12 bg-gray-50 min-h-screen">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <Link :href="route('home')" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 transition">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour
        </Link>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
          <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
            Conditions d'utilisation
          </h1>

          <div class="prose prose-green max-w-none">
            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                1. Acceptation des conditions
              </h2>
              <p class="text-gray-700">
                En accédant et en utilisant l'application Recettes Zéro Gaspi, vous acceptez d'être lié par les présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre service.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                2. Description du service
              </h2>
              <p class="text-gray-700 mb-4">Recettes Zéro Gaspi est une plateforme communautaire permettant de :</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>Créer, partager et découvrir des recettes de cuisine anti-gaspillage</li>
                <li>Planifier vos repas et générer des listes de courses</li>
                <li>Gérer votre garde-manger et suivre vos ingrédients</li>
                <li>Participer à une communauté engagée contre le gaspillage alimentaire</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                3. Compte utilisateur
              </h2>
              <p class="text-gray-700 mb-4">Pour utiliser certaines fonctionnalités, vous devez créer un compte. Vous vous engagez à :</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>Fournir des informations exactes et à jour</li>
                <li>Maintenir la sécurité de votre mot de passe</li>
                <li>Être responsable de toute activité effectuée depuis votre compte</li>
                <li>Nous informer immédiatement de toute utilisation non autorisée</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                4. Contenu utilisateur
              </h2>
              <p class="text-gray-700 mb-4">
                Vous conservez tous les droits sur le contenu que vous publiez (recettes, photos, commentaires). En publiant du contenu, vous nous accordez une licence mondiale, non exclusive, pour utiliser, reproduire et afficher ce contenu dans le cadre de notre service.
              </p>
              <div class="bg-yellow-50 border-l-4 border-yellow-600 p-4 mb-4">
                <p class="text-gray-700 font-semibold mb-2">Contenu interdit :</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                  <li>Contenu illégal, diffamatoire ou haineux</li>
                  <li>Contenu offensant, violent ou inapproprié</li>
                  <li>Spam ou publicité non sollicitée</li>
                  <li>Violation de droits d'auteur ou de propriété intellectuelle</li>
                  <li>Informations personnelles d'autres personnes sans leur consentement</li>
                </ul>
              </div>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                5. Propriété intellectuelle
              </h2>
              <p class="text-gray-700">
                L'application, son design, ses fonctionnalités et son contenu original sont protégés par les lois sur la propriété intellectuelle. Vous ne pouvez pas copier, modifier, distribuer ou vendre tout ou partie de notre service sans autorisation écrite.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                6. Services Premium
              </h2>
              <p class="text-gray-700 mb-4">
                Nous proposons des fonctionnalités premium payantes via Stripe. En souscrivant :
              </p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>Les paiements sont traités de manière sécurisée par Stripe</li>
                <li>L'abonnement se renouvelle automatiquement sauf annulation</li>
                <li>Vous pouvez annuler à tout moment depuis votre compte</li>
                <li>Aucun remboursement pour les périodes déjà facturées</li>
                <li>L'accès premium reste actif jusqu'à la fin de la période payée</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                7. Limitation de responsabilité
              </h2>
              <p class="text-gray-700 mb-4">
                Recettes Zéro Gaspi est fourni "tel quel". Nous ne garantissons pas :
              </p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>L'exactitude des recettes ou informations nutritionnelles</li>
                <li>La disponibilité continue du service sans interruption</li>
                <li>L'absence d'erreurs ou de bugs</li>
              </ul>
              <p class="text-gray-700 mt-4">
                Vous utilisez les recettes à vos risques. Nous ne sommes pas responsables des allergies, intoxications ou problèmes de santé liés à l'utilisation des recettes partagées.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                8. Modifications des conditions
              </h2>
              <p class="text-gray-700">
                Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications importantes seront notifiées par email. Votre utilisation continue du service après les modifications constitue votre acceptation des nouvelles conditions.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                9. Résiliation
              </h2>
              <p class="text-gray-700">
                Nous pouvons suspendre ou supprimer votre compte si vous violez ces conditions. Vous pouvez supprimer votre compte à tout moment depuis les paramètres.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                10. Contact
              </h2>
              <p class="text-gray-700">
                Pour toute question concernant ces conditions, contactez-nous à : contact@recettes-zero-gaspi.fr
              </p>
            </section>

            <div class="mt-12 pt-6 border-t border-gray-200 text-sm text-gray-500 text-center">
              Dernière mise à jour : 18 novembre 2025
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
