export interface ProductionUnit {
  id: number;
  crop_name: string
  total_area_ha: string
  geographic_coordinates: string
  property_id?: number | null
  created_at?: string
  updated_at?: string
  property?: {
    id: number
    name: string
    municipality: string
    state: string
    state_registration?: string | null
    total_area: string
    farmer_id: number
    created_at: string
    updated_at: string
  }
}

export interface ProductionUnitForm {
  crop_name: string
  total_area_ha: string
  geographic_coordinates: string
  property_id?: number | null
  open?: boolean
}



