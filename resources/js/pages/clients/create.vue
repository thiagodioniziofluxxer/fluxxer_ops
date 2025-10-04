<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import admin from '@/routes/admin'
import { type BreadcrumbItem } from '@/types'
import ClientForm from './components/ClientForm.vue'

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Clientes', href: admin.clients.index().url },
    { title: 'Criar Cliente', href: '#' },
]

const clientFormRef = ref<InstanceType<typeof ClientForm>>()

// Objeto inicial para criação
const newClient = {
    id: '',
    name: '',
    status: 'active',
}

const handleSubmit = (formData: { id: any; name: string; status: string }) => {
    router.post('/admin/clients', {
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
                :client="newClient"
                :is-edit="false"
                :initial-status="newClient.status"
                @submit="handleSubmit"
            />
        </div>
    </AppLayout>
</template>
