<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold text-gray-900">
              Сервис вакансий
            </h1>
          </div>
          <nav class="flex space-x-8">
            <NuxtLink
              to="/"
              class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              active-class="text-blue-600 hover:text-blue-700"
            >
              Список вакансий
            </NuxtLink>
            <NuxtLink
              to="/create"
              class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              active-class="text-blue-600 hover:text-blue-700"
            >
              Создать вакансию
            </NuxtLink>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <NuxtPage />
    </main>
  </div>
</template>

<script setup lang="ts">
// Глобальные стили
useHead({
  link: [
    {
      rel: 'stylesheet',
      href: 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'
    }
  ]
})
</script>
