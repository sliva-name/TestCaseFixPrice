<template>
  <div>
    <!-- Заголовок -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Создать новую вакансию</h2>
      <p class="mt-2 text-sm text-gray-600">
        Заполните форму для создания новой вакансии
      </p>
    </div>

    <!-- Форма создания вакансии -->
    <div class="bg-white shadow rounded-lg p-6">
      <form @submit.prevent="submitForm">
        <!-- Название вакансии -->
        <div class="mb-6">
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Название вакансии *
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.title }"
            placeholder="Например: Senior PHP Developer"
          />
          <div v-if="errors.title" class="mt-1 text-sm text-red-600">
            {{ errors.title }}
          </div>
        </div>

        <!-- Описание вакансии -->
        <div class="mb-6">
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Описание вакансии *
          </label>
          <textarea
            id="description"
            v-model="form.description"
            required
            rows="6"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.description }"
            placeholder="Подробное описание требований, обязанностей и условий работы..."
          ></textarea>
          <div v-if="errors.description" class="mt-1 text-sm text-red-600">
            {{ errors.description }}
          </div>
        </div>

        <!-- Зарплата -->
        <div class="mb-6">
          <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">
            Зарплата (руб/мес) *
          </label>
          <input
            id="salary"
            v-model.number="form.salary"
            type="number"
            required
            min="0"
            step="1000"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.salary }"
            placeholder="Например: 150000"
          />
          <div v-if="errors.salary" class="mt-1 text-sm text-red-600">
            {{ errors.salary }}
          </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="goBack"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Отмена
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="inline-flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Создание...
            </span>
            <span v-else>Создать вакансию</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Успешное создание -->
    <div v-if="success" class="mt-6 bg-green-50 border border-green-200 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-green-800">Вакансия успешно создана!</h3>
          <div class="mt-2 text-sm text-green-700">
            ID вакансии: {{ createdVacancyId }}
          </div>
          <div class="mt-4">
            <button
              @click="viewCreatedVacancy"
              class="text-sm font-medium text-green-800 hover:text-green-600"
            >
              Просмотреть вакансию →
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { CreateVacancyRequest, CreateVacancyResponse } from '~/types/vacancy'

const { createVacancy } = useApi()

// Состояние формы
const form = ref<CreateVacancyRequest>({
  title: '',
  description: '',
  salary: 0,
})

// Состояние UI
const loading = ref(false)
const success = ref(false)
const createdVacancyId = ref<number | null>(null)
const errors = ref<Record<string, string>>({})

// Валидация формы
const validateForm = (): boolean => {
  errors.value = {}
  
  if (!form.value.title.trim()) {
    errors.value.title = 'Название вакансии обязательно'
  }
  
  if (!form.value.description.trim()) {
    errors.value.description = 'Описание вакансии обязательно'
  }
  
  if (form.value.salary <= 0) {
    errors.value.salary = 'Зарплата должна быть больше 0'
  }
  
  return Object.keys(errors.value).length === 0
}

// Отправка формы
const submitForm = async () => {
  if (!validateForm()) {
    return
  }
  
  loading.value = true
  errors.value = {}
  
  try {
    const response = await createVacancy(form.value) as CreateVacancyResponse
    
    if (response.success) {
      success.value = true
      createdVacancyId.value = response.data.id
      
      // Очищаем форму
      form.value = {
        title: '',
        description: '',
        salary: 0,
      }
    } else {
      // Обрабатываем ошибки валидации с сервера
      if (response.errors) {
        errors.value = response.errors
      }
    }
  } catch (err: any) {
    errors.value.general = err.message || 'Произошла ошибка при создании вакансии'
  } finally {
    loading.value = false
  }
}

// Навигация назад
const goBack = () => {
  navigateTo('/')
}

// Просмотр созданной вакансии
const viewCreatedVacancy = () => {
  if (createdVacancyId.value) {
    navigateTo(`/vacancy/${createdVacancyId.value}`)
  }
}
</script>
