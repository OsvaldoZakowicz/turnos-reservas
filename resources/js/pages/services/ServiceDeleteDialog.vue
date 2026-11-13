<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import ServiceController from "@/actions/App/Http/Controllers/ServiceController";
import { Service } from "@/types";
import Button from "@/components/ui/button/Button.vue";
import {
	AlertDialog,
	AlertDialogCancel,
	AlertDialogContent,
	AlertDialogDescription,
	AlertDialogFooter,
	AlertDialogHeader,
	AlertDialogTitle,
} from "@/components/ui/alert-dialog";

const props = defineProps<{
	service: Service | null;
}>();

const open = defineModel<boolean>("open", { default: false });

const processing = ref(false);

const confirm = () => {
	if (!props.service) {
		return;
	}

	router.delete(ServiceController.destroy(props.service.id).url, {
		preserveScroll: true,
		onStart: () => (processing.value = true),
		onFinish: () => (processing.value = false),
		// el dialogo se cierra solo si el borrado fue exitoso
		onSuccess: () => (open.value = false),
	});
};
</script>
<template>
	<AlertDialog v-model:open="open">
		<AlertDialogContent>
			<AlertDialogHeader>
				<AlertDialogTitle> Eliminar servicio </AlertDialogTitle>
				<AlertDialogDescription
					>Eliminar el servicio {{ service?.name }}?
				</AlertDialogDescription>
			</AlertDialogHeader>
			<AlertDialogFooter>
				<AlertDialogCancel :disabled="processing">Cancelar</AlertDialogCancel>
				<Button variant="destructive" size="sm" @click="confirm"
					>Eliminar
				</Button>
			</AlertDialogFooter>
		</AlertDialogContent>
	</AlertDialog>
</template>
