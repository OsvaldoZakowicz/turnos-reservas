<script setup lang="ts">
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import DataTable from "@/components/DataTable.vue";
import { Button } from "@/components/ui/button";
import { index } from "@/routes/services";
import ServiceFormDialog from "./ServiceFormDialog.vue";
import type { Service, TableColumn } from "@/types";

defineProps<{ services: Service[] }>();

const dialogOpen = ref(false);
const editing = ref<Service | null>(null);

const openCreate = () => {
	editing.value = null;
	dialogOpen.value = true;
};

const openEdit = (service: Service) => {
	editing.value = service;
	dialogOpen.value = true;
};

// creamos un mapeo de atributos a encabezados de tabla
// para componente tabla
const columns: TableColumn<Service>[] = [
	{ key: "name", label: "Nombre" },
	{ key: "duration_minutes", label: "Duracion (min)" },
	{ key: "price", label: "Precio" },
	{ key: "is_active", label: "Estado" },
];

defineOptions({
	layout: {
		breadcrumbs: [
			{
				title: "Mis Servicios",
				href: index(),
			},
		],
	},
});
</script>

<template>
	<Head title="Servicios" />

	<div class="flex justify-end items-center gap-4 p-4 rounded-xl">
		<Button variant="default" size="sm" @click="openCreate"
			>Nuevo servicio</Button
		>
	</div>

	<div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
		<DataTable :columns="columns" :rows="services" row-key="id">
			<template #cell-is_active="{ row }">
				<Badge :variant="row.is_active ? 'default' : 'secondary'">
					{{ row.is_active ? "activo" : "inactivo" }}
				</Badge>
			</template>

			<template #actions="{ row }">
				<Button variant="outline" size="sm" @click="openEdit(row)">
					Editar
				</Button>
				<Button variant="destructive" size="sm">Eliminar</Button>
			</template>
		</DataTable>
	</div>

	<ServiceFormDialog v-model:open="dialogOpen" :service="editing" />
</template>
