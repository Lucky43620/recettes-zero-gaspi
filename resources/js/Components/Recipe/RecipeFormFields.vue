<script setup>
import { UserGroupIcon, ClockIcon, FireIcon, GlobeAltIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    form: Object,
});
</script>

<template>
    <div class="space-y-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Titre de la recette *
            </label>
            <input
                v-model="form.title"
                type="text"
                required
                placeholder="Ex: Tarte aux pommes maison"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-lg"
            />
            <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                {{ form.errors.title }}
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Description courte
            </label>
            <textarea
                v-model="form.summary"
                rows="4"
                placeholder="Décrivez votre recette en quelques mots..."
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 resize-none"
            />
            <div v-if="form.errors.summary" class="text-red-600 text-sm mt-1">
                {{ form.errors.summary }}
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">Informations pratiques</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <UserGroupIcon class="h-4 w-4 text-gray-500" />
                        Portions *
                    </label>
                    <input
                        v-model.number="form.servings"
                        type="number"
                        min="1"
                        required
                        placeholder="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <ClockIcon class="h-4 w-4 text-gray-500" />
                        Préparation (min)
                    </label>
                    <input
                        v-model.number="form.prep_minutes"
                        type="number"
                        min="0"
                        placeholder="15"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <FireIcon class="h-4 w-4 text-gray-500" />
                        Cuisson (min)
                    </label>
                    <input
                        v-model.number="form.cook_minutes"
                        type="number"
                        min="0"
                        placeholder="30"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Niveau de difficulté
                </label>
                <select
                    v-model="form.difficulty"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                >
                    <option value="">Sélectionner un niveau</option>
                    <option value="easy">😊 Facile</option>
                    <option value="medium">👨‍🍳 Moyen</option>
                    <option value="hard">⭐ Difficile</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <GlobeAltIcon class="h-4 w-4 text-gray-500" />
                    Type de cuisine
                </label>
                <input
                    v-model="form.cuisine"
                    type="text"
                    placeholder="Ex: Française, Italienne, Asiatique..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                />
            </div>
        </div>
    </div>
</template>