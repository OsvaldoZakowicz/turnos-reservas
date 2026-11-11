<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import {
	Card,
	CardContent,
	CardDescription,
	CardHeader,
	CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { index } from "@/routes/services";
import type { Service } from "@/types";

defineProps<{ services: Service[] }>();

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

// el precio llega como string por el cast decimal
const priceFormatter = new Intl.NumberFormat("es-AR", {
	style: "currency",
	currency: "ARS",
});

const formatPrice = (price: string): string =>
	priceFormatter.format(Number(price));
</script>

<template>
	<Head title="Servicios" />

	<div class="flex justify-end items-center gap-4 p-4 rounded-xl">
		<Button variant="default" size="lg">Nuevo Servicio</Button>
	</div>

	<div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
		<p v-if="services.length === 0" class="text-muted-foreground">
			todavia no hay servicios cargados
		</p>

		<div v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
			<Card v-for="service in services" :key="service.id">
				<CardHeader>
					<div class="flex items-start justify-between gap-2">
						<CardTitle>{{ service.name }}</CardTitle>
						<Badge :variant="service.is_active ? 'default' : 'secondary'">
							{{ service.is_active ? "activo" : "inactivo" }}
						</Badge>
					</div>
					<CardDescription>{{ service.description }}</CardDescription>
				</CardHeader>
				<CardContent class="flex justify-between text-sm">
					<span>{{ service.duration_minutes }} min</span>
					<span class="font-medium">{{ formatPrice(service.price) }}</span>
				</CardContent>
			</Card>
		</div>
	</div>
</template>
