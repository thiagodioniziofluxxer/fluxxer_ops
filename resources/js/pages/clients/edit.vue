<!-- resources/js/pages/clients/edit.vue -->
<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import admin from '@/routes/admin'
import { type BreadcrumbItem } from '@/types'
import ClientForm from './components/ClientForm.vue'

interface Client {
    id: string | number
    name: string
    status?: string | boolean | number
    active?: boolean | number
    created_at?: string
}

const props = defineProps<{
    client: Client
}>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Clientes', href: admin.clients.index().url },
    { title: 'Edição de Cliente', href: '#' },
]

const clientFormRef = ref<InstanceType<typeof ClientForm>>()

function normalizeStatus(c: Client): string {
    if (c.status !== undefined && c.status !== null) {
        const v = c.status
        return (v === 'active' || v === 'ativo' || v === 1 || v === true || v === '1') ? 'active' : 'inactive'
    }
    const a = c.active
    return (a === 1 || a === true || a === '1') ? 'active' : 'inactive'
}

const initialStatus = normalizeStatus(props.client)

const handleSubmit = (formData: { id: any; name: string; status: string }) => {
    router.put(`/admin/clients/${formData.id}`, {
        name: formData.name,
        status: formData.status,
    }, {
        onStart: () => clientFormRef.value?.setLoading(true),
        onError: (errors) => {
            const formatted: Record<string, string[]> = {}
            Object.entries(errors).forEach(([k, v]) => {
                formatted[k] = Array.isArray(v) ? v : [v as string]
            })
            clientFormRef.value?.setErrors(formatted)
        },
        onFinish: () => clientFormRef.value?.setLoading(false),
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <ClientForm
                ref="clientFormRef"
                :client="props.client"
                :initial-status="initialStatus"
                :is-edit="true"
                @submit="handleSubmit"
            />
        </div>
    </AppLayout>
</template>
