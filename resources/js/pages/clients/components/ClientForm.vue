<!-- resources/js/pages/clients/components/ClientForm.vue -->
<script setup lang="ts">
import { reactive, ref, watch, defineExpose } from 'vue'
import { Button } from '@/components/ui/button'

interface Client {
    id: string | number
    name: string
    status?: string | boolean | number
    active?: boolean | number
}

const props = defineProps<{
    client: Client
    isEdit?: boolean
    initialStatus?: string
}>()

const emit = defineEmits<{
    submit: [payload: { id: any; name: string; status: string }]
}>()

/* Opções do enum ClientStatusEnum */
const statusOptions = [
    { value: 'active', label: 'Ativo' },
    { value: 'inactive', label: 'Inativo' },
    { value: 'suspended', label: 'Suspenso' },
]

function normalizeInitialStatus(v?: string) {
    const allowed = statusOptions.map(o => o.value)
    if (v && allowed.includes(v)) return v
    return 'active'
}

const form = reactive({
    id: props.client.id,
    name: props.client.name || '',
    status: normalizeInitialStatus(props.initialStatus),
})

const loading = ref(false)
const errors = ref<Record<string, string[]>>({})

function setErrors(e: Record<string, string[]>) {
    errors.value = e
}
function setLoading(v: boolean) {
    loading.value = v
}

function submit() {
    errors.value = {}
    // normaliza caso algo externo altere
    form.status = normalizeInitialStatus(form.status)
    emit('submit', {
        id: form.id,
        name: form.name.trim(),
        status: form.status,
    })
}

watch(
    () => props.client,
    c => {
        form.id = c.id
        form.name = c.name || ''
    },
    { deep: true }
)

defineExpose({ setErrors, setLoading })
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6 max-w-xl">
        <div class="space-y-2">
            <label for="name" class="text-sm font-medium">Nome</label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full rounded-md border px-3 py-2 text-sm"
                :class="errors.name ? 'border-destructive' : 'border-input'"
                placeholder="Nome do cliente"
            />
            <p v-if="errors.name" class="text-xs text-destructive">
                {{ errors.name[0] }}
            </p>
        </div>

        <div class="space-y-2">
            <label for="status" class="text-sm font-medium">Status</label>
            <select
                id="status"
                v-model="form.status"
                class="w-full rounded-md border px-3 py-2 text-sm bg-white"
                :class="errors.status ? 'border-destructive' : 'border-input'"
                name="status"
            >
                <option
                    v-for="opt in statusOptions"
                    :key="opt.value"
                    :value="opt.value"
                >
                    {{ opt.label }}
                </option>
            </select>
            <p v-if="errors.status" class="text-xs text-destructive">
                {{ errors.status[0] }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <Button type="submit" :disabled="loading">
                <span v-if="!loading">{{ props.isEdit ? 'Salvar Alterações' : 'Criar Cliente' }}</span>
                <span v-else>Salvando...</span>
            </Button>
            <span v-if="loading" class="text-xs text-muted-foreground">Processando...</span>
        </div>
    </form>
</template>

<style scoped>
input[disabled] {
    opacity: .6;
}
</style>
