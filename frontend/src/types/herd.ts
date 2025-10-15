export interface Herd {
  id: number;
  propriedade_id: number; // FK -> propriedades.id
  especie: string;
  quantidade: number;
  finalidade?: string | null;
  data_atualizacao?: string | null; // date (YYYY-MM-DD)
  created_at: string;
  updated_at: string;
}


