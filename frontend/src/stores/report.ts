import { defineStore } from "pinia"
import { ref } from "vue"
import api from '@/services/api'

export const useReportStore = defineStore('report', () => {
  const loading = ref(false)
  const error = ref<string | null>(null)
  const reports = ref<[]>([])

  const getDashboardData = async () => {
    const response = await api.get('/reports/dashboard')
    return response.data
  }

  return {
    getDashboardData,
    loading,
    error,
    reports
  }
})
