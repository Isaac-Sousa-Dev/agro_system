export interface User {
  id: number
  name: string
  email: string
  email_verified_at?: string
  created_at: string
  updated_at: string,
  avatar?: string,
  user?: User | null
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface LoginResponse {
  token: string
  user: User
  message?: string
}

export interface ApiResponse<T = unknown> {
  data: T
  message?: string
  success: boolean
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

