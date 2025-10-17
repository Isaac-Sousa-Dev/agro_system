export interface Herd {
  id: number;
  species: string;
  quantity: string;
  purpose: string;
  update_date?: string | null;
  property_id?: number | null;
  created_at: string;
  updated_at: string;
  property?: {
    id: number;
    name: string;
    municipality: string;
    state: string;
    state_registration?: string | null;
  }
}

export interface HerdForm {
  species: string;
  quantity: string;
  purpose: string;
  update_date?: string | null;
  property_id?: number | null;
  open?: boolean;
}

