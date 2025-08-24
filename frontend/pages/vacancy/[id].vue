<template>
  <div>
    <!-- Кнопка назад -->
    <div class="mb-6">
      <button
        @click="goBack"
        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Назад к списку
      </button>
    </div>

    <!-- Состояние загрузки -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Ошибка -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Ошибка загрузки</h3>
          <div class="mt-2 text-sm text-red-700">{{ error }}</div>
        </div>
      </div>
    </div>

    <!-- Детали вакансии -->
    <div v-else-if="vacancy" class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Заголовок -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
              {{ vacancy.title }}
            </h1>
          </div>
          <div class="ml-6 text-right">
            <div class="text-3xl font-bold text-green-600">
              {{ formatSalary(vacancy.salary) }}
            </div>
            <div class="text-sm text-gray-500 mt-1">в месяц</div>
          </div>
        </div>
      </div>

      <!-- Описание -->
      <div class="px-6 py-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Описание вакансии</h2>
        <div class="prose max-w-none">
          <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">
            {{ vacancy.description }}
          </p>
        </div>
      </div>

      <!-- Действия -->
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex justify-end items-center">
          <div class="flex space-x-3">
            <button
              @click="editVacancy"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Редактировать
            </button>
            <button
              @click="deleteVacancy"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Удалить
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Vacancy, VacancyResponse } from '~/types/vacancy'

const route = useRoute()
const { getVacancy } = useApi()

// Состояние
const loading = ref(false)
const error = ref<string | null>(null)
const vacancy = ref<Vacancy | null>(null)

// Получение ID из маршрута
const vacancyId = computed(() => parseInt(route.params.id as string))

// Загрузка вакансии
const loadVacancy = async () => {
  if (!vacancyId.value) {
    error.value = 'Неверный ID вакансии'
    return
  }

  loading.value = true
  error.value = null
  
  try {
    const response = await getVacancy(vacancyId.value) as VacancyResponse
    vacancy.value = response.data
  } catch (err: any) {
    error.value = err.message || 'Произошла ошибка при загрузке вакансии'
  } finally {
    loading.value = false
  }
}

// Навигация назад
const goBack = () => {
  navigateTo('/')
}

// Редактирование вакансии
const editVacancy = () => {
  // TODO: Реализовать редактирование
  alert('Функция редактирования будет реализована позже')
}

// Удаление вакансии
const deleteVacancy = () => {
  if (confirm('Вы уверены, что хотите удалить эту вакансию?')) {
    // TODO: Реализовать удаление
    alert('Функция удаления будет реализована позже')
  }
}

// Форматирование зарплаты
const formatSalary = (salary: number) => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
  }).format(salary)
}

// Форматирование даты удалено, так как API больше не возвращает created_at

// Загрузка при монтировании
onMounted(() => {
  loadVacancy()
})

// Обновление при изменении ID
watch(() => route.params.id, () => {
  loadVacancy()
})
</script>
