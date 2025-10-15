export interface Property {
  id: number;
  farmer_id: number; // FK -> producers.id
  name: string;
  municipality: string;
  state: string; // char(2)
  state_registration?: string | null;
  total_area: number | string; // decimal(10,2)
  created_at: string;
  updated_at: string;
}
