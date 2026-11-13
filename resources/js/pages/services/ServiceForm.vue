<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import ServiceController from "@/actions/App/Http/Controllers/ServiceController";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Service } from "@/types";

// un servicio opcionalmente
const props = defineProps<{
	service?: Service | null;
}>();

const emit = defineEmits<{
	saved: [];
	close: [];
}>();

// para cada campo o es un valor a editar o un default
// depende de si creamos o editamos
const form = useForm({
	name: props.service?.name ?? "",
	description: props.service?.description ?? "",
	duration_minutes: props.service?.duration_minutes ?? 60,
	// el decimal llega como string desde laravel
	price: Number(props.service?.price ?? 0),
	is_active: props.service?.is_active ?? true,
});

const cancel = () => {
	// descarta lo escrito y los errores antes de cerrar
	form.reset();
	form.clearErrors();
	emit("close");
};

// submit para crear o editar segun
// si existe un servicio en el prop
const submit = () => {
	const options = {
		preserveScroll: true,
		onSuccess: () => {
			form.reset();
			emit("saved");
		},
	};

	if (props.service) {
		form.put(ServiceController.update(props.service.id).url, options);
		return;
	}

	form.post(ServiceController.store().url, options);
};
</script>

<template>
	<form class="grid gap-4" @submit.prevent="submit">
		<div class="grid gap-2">
			<Label for="name">Nombre</Label>
			<Input id="name" v-model="form.name" required />
			<InputError :message="form.errors.name" />
		</div>

		<div class="grid gap-2">
			<Label for="description">Descripción</Label>
			<Textarea id="description" v-model="form.description" />
			<InputError :message="form.errors.description" />
		</div>

		<div class="grid grid-cols-2 gap-4">
			<div class="grid gap-2">
				<Label for="duration_minutes">Duración (minutos)</Label>
				<Input
					id="duration_minutes"
					v-model.number="form.duration_minutes"
					type="number"
					min="5"
					required
				/>
				<InputError :message="form.errors.duration_minutes" />
			</div>

			<div class="grid gap-2">
				<Label for="price">Precio</Label>
				<Input
					id="price"
					v-model.number="form.price"
					type="number"
					step="0.01"
					min="0"
					required
				/>
				<InputError :message="form.errors.price" />
			</div>
		</div>

		<div class="flex items-center gap-2">
			<Checkbox id="is_active" v-model="form.is_active" />
			<Label for="is_active">Servicio activo</Label>
		</div>

		<div class="flex justify-end gap-2">
			<Button type="button" variant="outline" @click="cancel">
				Cancelar
			</Button>
			<Button type="submit" :disabled="form.processing">{{
				service ? "Editar servicio" : "Guardar"
			}}</Button>
		</div>
	</form>
</template>
