export interface ProductionUnit {
  id: number;
  crop_name: string
  total_area_ha: string
  geographic_coordinates: string
  property_id?: number | null
}

export interface ProductionUnitForm {
  crop_name: string
  total_area_ha: string
  geographic_coordinates: string
  property_id?: number | null
  open?: boolean
}



