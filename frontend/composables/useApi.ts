export const useApi = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const api = $fetch.create({
    baseURL: apiBase,
    headers: {
      'Content-Type': 'application/json',
    },
  })

  return {
    // Получить список вакансий
    async getVacancies(params: {
      page?: number
      per_page?: number
      sort_by?: string
      sort_order?: 'asc' | 'desc'
    } = {}) {
      return await api('/vacancies', {
        method: 'GET',
        params,
      })
    },

    // Получить конкретную вакансию
    async getVacancy(id: number, fields?: string[]) {
      const params: any = {}
      if (fields && fields.length > 0) {
        params.fields = fields.join(',')
      }
      
      return await api(`/vacancy/${id}`, {
        method: 'GET',
        params,
      })
    },

    // Создать новую вакансию
    async createVacancy(data: {
      title: string
      description: string
      salary: number
    }) {
      return await api('/vacancy', {
        method: 'POST',
        body: data,
      })
    },
  }
}
