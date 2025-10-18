import api from '@/services/api'

export interface CepResponse {
  cep: string
  logradouro: string
  complemento: string
  bairro: string
  localidade: string
  uf: string
  ibge: string
  gia: string
  ddd: string
  siafi: string
  erro?: boolean
}

export interface CepError {
  erro: boolean
  message: string
}

export interface CepResult {
  success: boolean
  data?: CepResponse
  error?: CepError
}

/**
 * Busca informações de endereço pelo CEP usando a API ViaCEP
 * @param cep - CEP no formato 00000-000 ou 00000000
 * @returns Promise com os dados do endereço ou erro
 */
export async function getAddressByCep(cep: string): Promise<CepResult> {
  try {
    // Remove caracteres não numéricos do CEP
    const cleanCep = cep.replace(/\D/g, '')

    // Valida se o CEP tem 8 dígitos
    if (cleanCep.length !== 8) {
      return {
        success: false,
        error: {
          erro: true,
          message: 'CEP deve ter 8 dígitos'
        }
      }
    }

    const response = await api.get(`https://viacep.com.br/ws/${cleanCep}/json/`)

    if (response.data.erro) {
      return {
        success: false,
        error: {
          erro: true,
          message: 'CEP não encontrado'
        }
      }
    }

    return {
      success: true,
      data: response.data
    }
  } catch (error) {
    console.error('Erro ao buscar CEP:', error)
    return {
      success: false,
      error: {
        erro: true,
        message: 'Erro ao consultar CEP. Tente novamente.'
      }
    }
  }
}

/**
 * Formata CEP para exibição (00000-000)
 * @param cep - CEP com ou sem formatação
 * @returns CEP formatado
 */
export function formatCep(cep: string): string {
  const cleanCep = cep.replace(/\D/g, '')
  return cleanCep.replace(/(\d{5})(\d{3})/, '$1-$2')
}

/**
 * Valida se o CEP está no formato correto
 * @param cep - CEP para validar
 * @returns true se o CEP é válido
 */
export function isValidCep(cep: string): boolean {
  const cleanCep = cep.replace(/\D/g, '')
  return cleanCep.length === 8 && /^\d{8}$/.test(cleanCep)
}
