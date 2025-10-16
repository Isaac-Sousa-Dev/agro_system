import type { HerdForm } from "./herd";
import type { ProductionUnitForm } from "./productionUnit";

export interface Property {
  id: number;
  farmer_id?: number | null;
  name: string;
  municipality: string;
  state: string;
  state_registration?: string | null;
  total_area: number | string;
  created_at: string;
  updated_at: string;
  productionUnits: ProductionUnitForm[];
  herds?: HerdForm[];
}

export interface PropertyForm {
  name: string
  municipality: string
  state: string
  state_registration?: string | null
  total_area: string,
  open?: boolean,
  farmer_id?: number | null,
  productionUnits?: ProductionUnitForm[],
  herds?: HerdForm[]
}
