<template>
  <PublicLayout :title="t('auth.privacy_policy')">
    <div class="py-12 bg-gray-50 min-h-screen">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <BackButton :href="route('home')" class="mb-6" />

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
          <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
            Politique de confidentialité
          </h1>

          <div class="prose prose-green max-w-none">
            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                1. Introduction
              </h2>
              <p class="text-gray-700">
                Chez Recettes Zéro Gaspi, nous accordons une grande importance à la protection de vos données personnelles. Cette politique de confidentialité explique comment nous collectons, utilisons et protégeons vos informations.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                2. Données collectées
              </h2>
              <p class="text-gray-700 mb-4">Nous collectons les données suivantes :</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li><strong>Informations personnelles :</strong> Nom, prénom, adresse email, photo de profil (optionnelle)</li>
                <li><strong>Contenu créé :</strong> Recettes, commentaires, photos, notes, listes de courses, planning de repas</li>
                <li><strong>Données d'utilisation :</strong> Pages visitées, fonctionnalités utilisées, temps passé sur l'application</li>
                <li><strong>Données techniques :</strong> Adresse IP, type de navigateur, système d'exploitation, appareil utilisé</li>
                <li><strong>Cookies :</strong> Cookies de session, préférences linguistiques, authentification</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                3. Utilisation des données
              </h2>
              <p class="text-gray-700 mb-4">Nous utilisons vos données pour :</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>Fournir et améliorer nos services</li>
                <li>Personnaliser votre expérience utilisateur</li>
                <li>Communiquer avec vous (emails de notification, support client)</li>
                <li>Assurer la sécurité et prévenir les fraudes</li>
                <li>Respecter nos obligations légales</li>
                <li>Analyser l'utilisation pour améliorer l'application</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                4. Partage des données
              </h2>
              <p class="text-gray-700 mb-4">
                Nous ne vendons jamais vos données personnelles. Nous les partageons uniquement avec :
              </p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li><strong>Stripe :</strong> Pour traiter les paiements de manière sécurisée (abonnement premium)</li>
                <li><strong>OpenFoodFacts :</strong> Pour récupérer les informations nutritionnelles des produits</li>
                <li><strong>Autorités légales :</strong> Si requis par la loi ou pour protéger nos droits</li>
              </ul>
              <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mt-4">
                <p class="text-gray-700">
                  <strong>Note :</strong> Les recettes que vous marquez comme "publiques" sont visibles par tous les utilisateurs de la plateforme. Vos recettes privées restent confidentielles.
                </p>
              </div>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                5. Cookies et technologies similaires
              </h2>
              <p class="text-gray-700 mb-4">Nous utilisons les cookies suivants :</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li><strong>Cookies essentiels :</strong> Nécessaires au fonctionnement de l'application (session, authentification)</li>
                <li><strong>Cookies fonctionnels :</strong> Mémorisation de vos préférences (langue, thème)</li>
              </ul>
              <p class="text-gray-700 mt-4">
                Nous n'utilisons PAS de cookies publicitaires ou de tracking tiers.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                6. Sécurité des données
              </h2>
              <p class="text-gray-700 mb-4">
                Nous mettons en œuvre des mesures de sécurité pour protéger vos données :
              </p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>Chiffrement SSL/TLS pour toutes les communications</li>
                <li>Mots de passe hashés avec bcrypt</li>
                <li>Serveurs sécurisés et sauvegardés régulièrement</li>
                <li>Accès restreint aux données personnelles (équipe autorisée uniquement)</li>
                <li>Authentification à deux facteurs disponible</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                7. Vos droits (RGPD)
              </h2>
              <p class="text-gray-700 mb-4">Conformément au RGPD, vous disposez des droits suivants :</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li><strong>Droit d'accès :</strong> Consulter les données que nous détenons sur vous</li>
                <li><strong>Droit de rectification :</strong> Corriger vos informations personnelles</li>
                <li><strong>Droit à l'effacement :</strong> Supprimer votre compte et vos données</li>
                <li><strong>Droit à la portabilité :</strong> Exporter vos données dans un format lisible</li>
                <li><strong>Droit d'opposition :</strong> Vous opposer au traitement de certaines données</li>
                <li><strong>Droit à la limitation :</strong> Limiter l'utilisation de vos données</li>
              </ul>
              <div class="bg-green-50 border-l-4 border-green-600 p-4 mt-4">
                <p class="font-semibold text-gray-900 mb-2">Pour exercer vos droits :</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                  <li>Exportez vos données depuis votre profil</li>
                  <li>Supprimez votre compte depuis les paramètres</li>
                  <li>Contactez-nous : contact@recettes-zero-gaspi.fr</li>
                </ul>
              </div>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                8. Conservation des données
              </h2>
              <p class="text-gray-700">
                Vos données sont conservées tant que votre compte est actif. Après suppression de votre compte, vos données personnelles sont définitivement effacées sous 30 jours. Les contenus publics (recettes partagées) peuvent être conservés de manière anonymisée.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                9. Mineurs
              </h2>
              <p class="text-gray-700">
                Notre service est destiné aux personnes âgées de 13 ans et plus. Si vous avez moins de 13 ans, veuillez obtenir le consentement de vos parents avant de créer un compte. Si nous découvrons qu'un mineur de moins de 13 ans a créé un compte sans autorisation parentale, nous le supprimerons immédiatement.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                10. Modifications de cette politique
              </h2>
              <p class="text-gray-700">
                Nous pouvons modifier cette politique de confidentialité à tout moment. Les modifications importantes seront notifiées par email. Nous vous encourageons à consulter régulièrement cette page pour rester informé de nos pratiques.
              </p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                11. Contact
              </h2>
              <p class="text-gray-700">
                Pour toute question concernant cette politique de confidentialité ou vos données personnelles, contactez-nous à :
              </p>
              <p class="text-gray-700 mt-2">
                <strong>Email :</strong> contact@recettes-zero-gaspi.fr
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
import BackButton from '@/Components/Common/BackButton.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
