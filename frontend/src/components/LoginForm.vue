<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { login, loading, error } = useAuth()

// Form data
const form = reactive({
  email: '',
  password: ''
})

const showPassword = ref(false)

// Validation
const errors = ref<Record<string, string>>({})

const validateForm = () => {
  errors.value = {}

  if (!form.email) {
    errors.value.email = 'Email é obrigatório'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.value.email = 'Email inválido'
  }

  if (!form.password) {
    errors.value.password = 'Senha é obrigatória'
  } else if (form.password.length < 6) {
    errors.value.password = 'Senha deve ter pelo menos 6 caracteres'
  }

  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) return

  await login({
    email: form.email,
    password: form.password
  })
}

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>🌱 Agro System</h1>
        <p>Faça login para acessar o sistema</p>
      </div>

      <form @submit.prevent="handleSubmit" class="login-form">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            :class="{ 'error': errors.email }"
            placeholder="Digite seu email"
            autocomplete="email"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>

        <div class="form-group">
          <label for="password">Senha</label>
          <div class="password-input-wrapper">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-input"
              :class="{ 'error': errors.password }"
              placeholder="Digite sua senha"
              autocomplete="current-password"
            />
            <button
              type="button"
              @click="togglePasswordVisibility"
              class="password-toggle"
            >
              {{ showPassword ? '👁️' : '👁️‍🗨️' }}
            </button>
          </div>
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <div v-if="error" class="error-banner">
          {{ error }}
        </div>

        <button
          type="submit"
          class="login-button"
          :disabled="loading"
        >
          <span v-if="loading">Entrando...</span>
          <span v-else>Entrar</span>
        </button>
      </form>

      <div class="login-footer">
        <p>Demo: admin@agro.com / password</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  /* background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); */
  padding: 2rem;
}

.login-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  padding: 3rem;
  width: 100%;
  max-width: 400px;
  border: 1px solid #e5e7eb;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-header h1 {
  font-size: 2rem;
  color: #065f46;
  margin: 0 0 0.5rem 0;
  font-weight: 700;
}

.login-header p {
  color: #6b7280;
  margin: 0;
  font-size: 0.95rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
}

.form-input {
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: white;
}

.form-input:focus {
  outline: none;
  border-color: #059669;
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.form-input.error {
  border-color: #dc2626;
}

.form-input.error:focus {
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.password-input-wrapper {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  padding: 0.25rem;
  color: #6b7280;
  transition: color 0.2s;
}

.password-toggle:hover {
  color: #374151;
}

.error-message {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.error-banner {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  text-align: center;
}

.login-button {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
  border: none;
  padding: 0.875rem 1.5rem;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  margin-top: 1rem;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.login-footer {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.login-footer p {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
}

@media (max-width: 480px) {
  .login-container {
    padding: 1rem;
  }

  .login-card {
    padding: 2rem;
  }

  .login-header h1 {
    font-size: 1.75rem;
  }
}
</style>
