import { getMaskFn, type DecimalOptions, type MaskPreset } from '@/utils/masks'

type BindingValue = MaskPreset | { preset: MaskPreset, options?: DecimalOptions } | ((v: unknown) => string)

type MaskEl = HTMLElement & { __masking?: boolean; __lastMasked?: string }
type MaskInput = HTMLInputElement & { __masking?: boolean; __lastMasked?: string }

function resolveMaxLength(preset: MaskPreset | ((v: unknown) => string) | undefined): number | undefined {
  if (typeof preset === 'function') return undefined
  switch (preset) {
    case 'cpf_cnpj': return 18 // atende CPF (14) e CNPJ (18)
    case 'phone': return 15 // (99) 99999-9999
    case 'state': return 2
    case 'state_registration': return 20
    case 'date': return 10 // DD/MM/YYYY
    case 'datetime': return 16 // DD/MM/YYYY HH:MM
    case 'cep': return 9 // 99999-999
    default: return undefined
  }
}

function apply(el: MaskEl, formatter: (v: unknown) => string) {
  const isInput = (el as HTMLInputElement).value !== undefined
  if (isInput) {
    const input = el as MaskInput
    if (input.__masking) return // evita reentrância
    const raw = input.value
    const masked = formatter(raw)
    if (masked === input.__lastMasked) return // evita loop quando nada mudou
    input.__masking = true
    input.value = masked
    input.__lastMasked = masked
    // manter cursor no fim (simples e estável)
    input.setSelectionRange(masked.length, masked.length)
    const evt = new Event('input', { bubbles: true })
    input.dispatchEvent(evt)
    input.__masking = false
  } else {
    const current = el.textContent || ''
    const masked = formatter(current)
    if (masked !== current) el.textContent = masked
  }
}

export default {
  mounted(el: HTMLElement, binding: { value: BindingValue }) {
    const { value } = binding
    const preset = typeof value === 'object' && 'preset' in value ? value.preset : value
    const options = typeof value === 'object' && 'preset' in value ? value.options : undefined
    const formatter = getMaskFn(preset as MaskPreset | ((v: unknown) => string), options)

    if ((el as HTMLInputElement).value !== undefined) {
      // Input: escuta eventos
      el.addEventListener('input', () => apply(el as MaskEl, formatter))
      // Define maxLength quando aplicável
      const maxLen = resolveMaxLength(preset as MaskPreset | ((v: unknown) => string))
      if (typeof maxLen === 'number') (el as HTMLInputElement).maxLength = maxLen
      // aplica inicial
      apply(el as MaskEl, formatter)
    } else {
      // Elemento de texto
      apply(el as MaskEl, formatter)
    }
  },
  updated(el: HTMLElement, binding: { value: BindingValue }) {
    const { value } = binding
    const preset = typeof value === 'object' && 'preset' in value ? value.preset : value
    const options = typeof value === 'object' && 'preset' in value ? value.options : undefined
    const formatter = getMaskFn(preset as MaskPreset | ((v: unknown) => string), options)
    if ((el as HTMLInputElement).value !== undefined) {
      const maxLen = resolveMaxLength(preset as MaskPreset | ((v: unknown) => string))
      if (typeof maxLen === 'number') (el as HTMLInputElement).maxLength = maxLen
    }
    apply(el as MaskEl, formatter)
  }
}


