// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  ssr: false, // Отключаем SSR для SPA
  app: {
    head: {
      title: 'Сервис вакансий',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Сервис для хранения и подачи вакансий' }
      ]
    }
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NODE_ENV === 'development' ? 'http://localhost/api' : '/api'
    }
  }
})
