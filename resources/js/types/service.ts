// servicio ofrecido y reservable
export type Service = {
	id: number;
	name: string;
	description: string | null;
	duration_minutes: number;
	// llega como string por el cast decimal del modelo
	price: string;
	is_active: boolean;
	created_at: string;
	updated_at: string;
};
