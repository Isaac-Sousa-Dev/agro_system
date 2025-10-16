export interface Herd {
  id: number;
  species: string;
  quantity: string;
  purpose: string;
  update_date?: string | null;
  property_id?: number | null;
  created_at: string;
  updated_at: string;
}

export interface HerdForm {
  species: string;
  quantity: string;
  purpose: string;
  update_date?: string | null;
  property_id?: number | null;
  open?: boolean;
}

