export interface ProductionUnit {
  id: number;
  propriedade_id: number; // FK -> propriedades.id
  nome_cultura: string;
  area_total_ha: number; // decimal(10,2)
  coordenadas_geograficas?: string | null;
  created_at: string;
  updated_at: string;
}


