<script setup lang="ts">
import {
	Dialog,
	DialogContent,
	DialogDescription,
	DialogHeader,
	DialogTitle,
} from "@/components/ui/dialog";
import ServiceForm from "./ServiceForm.vue";
import { Service } from "@/types/service.js";

defineProps<{
	service?: Service | null;
}>();

const open = defineModel<boolean>("open", { default: false });
</script>

<template>
	<Dialog v-model:open="open">
		<DialogContent>
			<DialogHeader>
				<DialogTitle>
					{{ service ? "Editar servicio" : "Nuevo servicio" }}
				</DialogTitle>
				<DialogDescription>
					{{
						service
							? "Modifica los datos del servicio"
							: "Completa los datos del servicio que vas a ofrecer"
					}}
				</DialogDescription>
			</DialogHeader>
			<ServiceForm
				:key="service?.id ?? 'new'"
				:service="service"
				@saved="open = false"
				@close="open = false"
			/>
		</DialogContent>
	</Dialog>
</template>
