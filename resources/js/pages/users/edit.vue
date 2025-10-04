<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import admin from '@/routes/admin'
import { type BreadcrumbItem } from '@/types'
import UserForm from './components/UserForm.vue'

interface User {
    id: number
    name: string
    email: string
    client_id: number | null
    role_id: number
    avatar?: string
    email_verified_at?: string | null
}

interface Client {
    id: number
    name: string
}

interface Role {
    id: number
    name: string
    slug: string
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Users',
        href: admin.users.index().url,
    },
    {
        title: 'Edição de Usuário',
        href: '#',
    },
]

const props = defineProps<{
    user: User
    clients: Client[]
    roles: Role[]
}>()

const userFormRef = ref<InstanceType<typeof UserForm>>()

// Funções para buscar dados
const handleFetchClients = () => {
    if (props.clients && userFormRef.value) {
        userFormRef.value.setClients(props.clients)
    }
}

const handleFetchRoles = () => {
    if (props.roles && userFormRef.value) {
        userFormRef.value.setRoles(props.roles)
    }
}

const handleSubmit = (formData: any) => {
    // Usar Inertia.put para atualizar o usuário
    router.put(`/admin/users/${formData.id}`, {
        name: formData.name,
        client_id: formData.client_id,
        role_id: formData.role_id
    }, {
        // onSuccess: () => {
        //     // Redirecionar para a lista de usuários após sucesso
        //     router.visit(admin.users.index().url)
        // },
        onError: (errors: Record<string, string | string[]>) => {
            // Converter strings simples em arrays para compatibilidade
            const formattedErrors: Record<string, string[]> = {}

            Object.keys(errors).forEach(key => {
                const error = errors[key]
                formattedErrors[key] = Array.isArray(error) ? error : [error as string]
            })

            // Passar os erros formatados para o formulário
            if (userFormRef.value) {
                userFormRef.value.setErrors(formattedErrors)
            }
        },
        onStart: () => {
            // Limpar erros anteriores e ativar loading
            if (userFormRef.value) {
                userFormRef.value.setErrors({})
                userFormRef.value.setLoading(true)
            }
        },
        onFinish: () => {
            // Garantir que o loading seja desativado
            if (userFormRef.value) {
                userFormRef.value.setLoading(false)
            }
        }
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <UserForm
                ref="userFormRef"
                :user="props.user"
                :is-edit="true"
                @submit="handleSubmit"
                @fetch-clients="handleFetchClients"
                @fetch-roles="handleFetchRoles"
            />
        </div>
    </AppLayout>
</template>
