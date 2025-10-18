// Utilitários de máscara e formatação reutilizáveis

export function toDigits(value: unknown): string {
  return String(value ?? '').replace(/\D+/g, '')
}

export function formatCpf(value: string): string {
  const v = toDigits(value).slice(0, 11)
  let out = ''
  if (v.length > 0) out = v.slice(0, 3)
  if (v.length > 3) out = `${v.slice(0, 3)}.${v.slice(3, 6)}`
  if (v.length > 6) out = `${out}.${v.slice(6, 9)}`
  if (v.length > 9) out = `${out}-${v.slice(9, 11)}`
  return out
}

export function formatCnpj(value: string): string {
  const v = toDigits(value).slice(0, 14)
  let out = ''
  if (v.length > 0) out = v.slice(0, 2)
  if (v.length > 2) out = `${v.slice(0, 2)}.${v.slice(2, 5)}`
  if (v.length > 5) out = `${out}.${v.slice(5, 8)}`
  if (v.length > 8) out = `${out}/${v.slice(8, 12)}`
  if (v.length > 12) out = `${out}-${v.slice(12, 14)}`
  return out
}

export function formatCpfCnpj(value: unknown): string {
  const digits = toDigits(value)
  if (digits.length <= 11) return formatCpf(digits)
  return formatCnpj(digits)
}

export function formatPhone(value: unknown): string {
  const v = toDigits(value).slice(0, 11)
  if (v.length <= 10) {
    // (99) 9999-9999
    const p1 = v.slice(0, 2)
    const p2 = v.slice(2, 6)
    const p3 = v.slice(6, 10)
    if (!p1) return ''
    if (!p2) return `(${p1}`
    if (!p3) return `(${p1}) ${p2}`
    return `(${p1}) ${p2}-${p3}`
  }
  // (99) 99999-9999
  const p1 = v.slice(0, 2)
  const p2 = v.slice(2, 7)
  const p3 = v.slice(7, 11)
  return `(${p1}) ${p2}-${p3}`
}

export function formatState(value: unknown): string {
  return String(value ?? '').replace(/[^A-Za-z]/g, '').toUpperCase().slice(0, 2)
}

export function formatStateRegistration(value: unknown): string {
  // Mantém letras/números, pontos, barras e hífens, tudo MAIÚSCULO
  return String(value ?? '').replace(/[^A-Za-z0-9./-]/g, '').toUpperCase().slice(0, 20)
}

export interface DecimalOptions {
  decimals?: number
  decimalSeparator?: string
  thousandSeparator?: string | false
}

export function formatDecimal(value: unknown, opts: DecimalOptions = {}): string {
  const { decimals = 2, decimalSeparator = '.', thousandSeparator = false } = opts
  const raw = String(value ?? '')
  // mantém apenas dígitos e um separador decimal
  const cleaned = raw.replace(/[^0-9.,]/g, '').replace(/,/g, '.')
  const [intPartRaw, decRaw = ''] = cleaned.split('.')
  const intPart = (intPartRaw || '').replace(/^0+(?=\d)/, '')
  const decPart = decRaw.slice(0, decimals)
  let intFormatted = intPart
  if (thousandSeparator) {
    intFormatted = (intPart || '0').replace(/\B(?=(\d{3})+(?!\d))/g, String(thousandSeparator))
  }
  if (decimals === 0) return intFormatted || '0'
  return `${intFormatted || '0'}${decimalSeparator}${decPart.padEnd(decimals, '0')}`
}

export function formatDate(value: unknown): string {
  const v = toDigits(value).slice(0, 8)
  if (v.length <= 2) return v
  if (v.length <= 4) return `${v.slice(0, 2)}/${v.slice(2, 4)}`
  return `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4, 8)}`
}

export function formatDateTime(value: unknown): string {
  if (!value) return ''
  const date = new Date(String(value))
  if (isNaN(date.getTime())) return String(value)

  return date.toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

export function formatCep(value: unknown): string {
  return toDigits(value).slice(0, 9).replace(/(\d{5})(\d{3})/, '$1-$2')
}

// Diretiva utilitária: aceita um preset string ou uma função (value)=>string
export type MaskPreset = 'cpf_cnpj' | 'phone' | 'state' | 'state_registration' | 'decimal' | 'date' | 'datetime' | 'cep'

export function getMaskFn(preset: MaskPreset | ((v: unknown) => string), options?: DecimalOptions) {
  if (typeof preset === 'function') return preset
  switch (preset) {
    case 'cpf_cnpj': return formatCpfCnpj
    case 'phone': return formatPhone
    case 'state': return formatState
    case 'state_registration': return formatStateRegistration
    case 'decimal': return (v: unknown) => formatDecimal(v, options)
    case 'date': return formatDate
    case 'datetime': return formatDateTime
    case 'cep': return formatCep
    default: return (v: unknown) => String(v ?? '')
  }
}

