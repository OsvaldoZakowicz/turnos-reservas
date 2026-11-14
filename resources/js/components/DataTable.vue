<script setup lang="ts" generic="T extends Object">
import { computed, useSlots } from 'vue';
import { TableColumn } from '@/types';

const props = withDefaults(
	defineProps<{
		columns: TableColumn<T>[];
		rows: T[];
		rowKey: keyof T & string;
		actionsLabel?: string;
		emptyText?: string;
	}>(),
	{
		actionsLabel: 'Acciones',
		emptyText: 'sin datos para mostrar',
	},
);

const slots = useSlots();

// la columna de acciones solo existe si se usa el slot actions
const hasActions = computed(() => Boolean(slots.actions));
const colspan = computed(
	() => props.columns.length + (hasActions.value ? 1 : 0),
);
</script>
<template>
	<div
		class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
	>
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="bg-muted/50 text-left text-muted-foreground">
					<tr>
						<th
							v-for="column in columns"
							:key="column.key"
							class="px-4 py-3 font-medium"
							:class="column.class"
						>
							{{ column.label }}
						</th>
						<th
							v-if="hasActions"
							class="px-4 py-3 text-right font-medium"
						>
							{{ actionsLabel }}
						</th>
					</tr>
				</thead>

				<tbody
					class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border"
				>
					<tr v-if="rows.length === 0">
						<td
							:colspan="colspan"
							class="px-4 py-8 text-center text-muted-foreground"
						>
							<slot name="empty">{{ emptyText }}</slot>
						</td>
					</tr>

					<tr
						v-for="row in rows"
						:key="String(row[rowKey])"
						class="hover:bg-muted/30"
					>
						<td
							v-for="column in columns"
							:key="column.key"
							class="px-4 py-3"
							:class="column.class"
						>
							<slot :name="`cell-${column.key}`" :row="row">
								{{ row[column.key] }}
							</slot>
						</td>

						<td v-if="hasActions" class="px-4 py-3">
							<div class="flex justify-end gap-2">
								<slot name="actions" :row="row" />
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div
			class="border-t border-sidebar-border/70 px-4 py-3 text-sm text-muted-foreground dark:border-sidebar-border"
		>
			<slot name="footer">{{ rows.length }} registros</slot>
		</div>
	</div>
</template>
