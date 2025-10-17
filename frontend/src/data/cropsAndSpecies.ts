// Lista de culturas disponíveis
export const CROPS = [
  { id: 1, name: 'Laranja Pera' },
  { id: 2, name: 'Melancia Crimson Sweet' },
  { id: 3, name: 'Goiaba Paluma' }
]

// Lista de espécies de animais disponíveis
export const SPECIES = [
  { id: 1, name: 'Suíno' },
  { id: 2, name: 'Caprino' },
  { id: 3, name: 'Bovino' }
]

export interface Crop {
  id: number
  name: string
}

export interface Species {
  id: number
  name: string
}
