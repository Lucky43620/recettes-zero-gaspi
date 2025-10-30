<script setup>
const props = defineProps({
    form: Object,
});
</script>

<template>
    <div class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Titre *
            </label>
            <input
                v-model="form.title"
                type="text"
                required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
            />
            <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                {{ form.errors.title }}
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Résumé
            </label>
            <textarea
                v-model="form.summary"
                rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
            />
            <div v-if="form.errors.summary" class="text-red-600 text-sm mt-1">
                {{ form.errors.summary }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Portions *
                </label>
                <input
                    v-model.number="form.servings"
                    type="number"
                    min="1"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Préparation (min)
                </label>
                <input
                    v-model.number="form.prep_minutes"
                    type="number"
                    min="0"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cuisson (min)
                </label>
                <input
                    v-model.number="form.cook_minutes"
                    type="number"
                    min="0"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Difficulté
                </label>
                <select
                    v-model="form.difficulty"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                >
                    <option value="">Sélectionner</option>
                    <option value="easy">Facile</option>
                    <option value="medium">Moyen</option>
                    <option value="hard">Difficile</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cuisine
                </label>
                <input
                    v-model="form.cuisine"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                />
            </div>
        </div>
    </div>
</template>