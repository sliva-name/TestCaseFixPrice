export interface Vacancy {
  title: string
  description: string
  salary: number
}

export interface VacancyListResponse {
  success: boolean
  data: {
    items: Vacancy[]
    pagination: {
      page: number
      per_page: number
      total: number
      pages: number
    }
    sorting: {
      sort_by: string
      sort_order: string
    }
  }
}

export interface VacancyResponse {
  success: boolean
  data: Vacancy
}

export interface CreateVacancyRequest {
  title: string
  description: string
  salary: number
}

export interface CreateVacancyResponse {
  success: boolean
  data: {
    id: number
    success: boolean
  }
  message?: string
  errors?: Record<string, string>
}
