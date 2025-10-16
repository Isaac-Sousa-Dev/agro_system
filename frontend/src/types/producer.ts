import type { Property, PropertyForm } from "./property";

export interface Producer {
  id: number;
  name: string;
  cpf_cnpj: string;
  phone?: string | null;
  email?: string | null;
  address?: string | null;
  registration_date?: string | null; // date (YYYY-MM-DD)
  created_at: string; // timestamp ISO
  updated_at: string; // timestamp ISO
  properties: Property[];
}

export interface ProducerForm {
  name: string
  cpf_cnpj: string
  phone?: string | null
  email?: string | null
  address?: string | null
  registration_date?: string | null
  properties: PropertyForm[]
}
