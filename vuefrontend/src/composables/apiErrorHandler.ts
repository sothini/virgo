// composables/useApiError.ts
import { ref } from 'vue'

export function useApiError() {
  const error = ref<string | null>(null)
  const errors = ref<Record<string, string[]>>({})

  function reset() {
    error.value = null
    errors.value = {}
  }

  function handle(err: any) {
    const response = err.response

    if (!response) {
      error.value = 'Network error'
      return
    }

    if (response.status === 422) {
      errors.value = response.data.errors || {}
      return
    }

    error.value = response.data.message || response.message || 'Something went wrong'
    console.log(error.value)
  }

  return { error, errors, reset, handle }
}
