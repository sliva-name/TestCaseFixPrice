<template>
  <div>
    <!-- Заголовок и фильтры -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-4">Список вакансий</h2>
      
      <!-- Фильтры сортировки -->
      <div class="flex flex-wrap gap-4 items-center">
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700">Сортировка по:</label>
          <select
            v-model="sortBy"
            @change="loadVacancies"
            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="created_at">Дате создания</option>
            <option value="salary">Зарплате</option>
            <option value="title">Названию</option>
          </select>
        </div>
        
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700">Порядок:</label>
          <select
            v-model="sortOrder"
            @change="loadVacancies"
            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="desc">Убывание</option>
            <option value="asc">Возрастание</option>
          </select>
        </div>
      </div>
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

    <!-- Список вакансий -->
    <div v-else-if="vacancies.length > 0" class="space-y-4">
      <div
        v-for="(vacancy, index) in vacancies"
        :key="index"
        class="bg-white shadow rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer"
        @click="viewVacancy(index + 1)"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              {{ vacancy.title }}
            </h3>
            <p class="text-gray-600 text-sm line-clamp-3">
              {{ vacancy.description }}
            </p>
          </div>
          <div class="ml-4 text-right">
            <div class="text-lg font-bold text-green-600">
              {{ formatSalary(vacancy.salary) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Пустое состояние -->
    <div v-else class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Вакансии не найдены</h3>
      <p class="mt-1 text-sm text-gray-500">Попробуйте изменить параметры поиска или создайте новую вакансию.</p>
    </div>

    <!-- Пагинация -->
    <div v-if="pagination && pagination.pages > 1" class="mt-8 flex justify-center">
      <nav class="flex items-center space-x-2">
        <button
          @click="changePage(pagination.page - 1)"
          :disabled="pagination.page <= 1"
          class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Назад
        </button>
        
        <span class="px-3 py-2 text-sm text-gray-700">
          Страница {{ pagination.page }} из {{ pagination.pages }}
        </span>
        
        <button
          @click="changePage(pagination.page + 1)"
          :disabled="pagination.page >= pagination.pages"
          class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Вперед
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Vacancy, VacancyListResponse } from '~/types/vacancy'

const { getVacancies } = useApi()

// Состояние
const loading = ref(false)
const error = ref<string | null>(null)
const vacancies = ref<Vacancy[]>([])
const pagination = ref<VacancyListResponse['data']['pagination'] | null>(null)

// Параметры сортировки
const sortBy = ref('created_at')
const sortOrder = ref<'asc' | 'desc'>('desc')

// Загрузка вакансий
const loadVacancies = async (page = 1) => {
  loading.value = true
  error.value = null
  
  try {
    const response = await getVacancies({
      page,
      per_page: 10,
      sort_by: sortBy.value,
      sort_order: sortOrder.value,
    }) as VacancyListResponse
    console.log(response)
    vacancies.value = response.data.items || []
    pagination.value = response.data.pagination
  } catch (err: any) {
    error.value = err.message || 'Произошла ошибка при загрузке вакансий'
  } finally {
    loading.value = false
  }
}

// Смена страницы
const changePage = (page: number) => {
  if (page >= 1 && pagination.value && page <= pagination.value.pages) {
    loadVacancies(page)
  }
}

// Просмотр вакансии
const viewVacancy = (id: number) => {
  navigateTo(`/vacancy/${id}`)
}

// Форматирование зарплаты
const formatSalary = (salary: number) => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
  }).format(salary)
}

// Загрузка при монтировании
onMounted(() => {
  loadVacancies()
})
</script>
