// Lista de culturas disponíveis
export const CROPS = [
  { id: 'Laranja Pera', name: 'Laranja Pera' },
  { id: 'Melancia Crimson Sweet', name: 'Melancia Crimson Sweet' },
  { id: 'Goiaba Paluma', name: 'Goiaba Paluma' }
]

// Lista de espécies de animais disponíveis
export const SPECIES = [
  { id: 'Suíno', name: 'Suíno' },
  { id: 'Caprino', name: 'Caprino' },
  { id: 'Bovino', name: 'Bovino' }
]

export interface Crop {
  id: string
  name: string
}

export interface Species {
  id: string
  name: string
}
