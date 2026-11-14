export type TableColumn<T> = {
	key: keyof T & string;
	label: string;
	// clases extra para encabezado y celda, por ejemplo alineacion
	class?: string;
};
