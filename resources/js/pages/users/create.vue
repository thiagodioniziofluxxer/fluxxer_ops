<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import admin from '@/routes/admin'
import { type BreadcrumbItem } from '@/types'
import UserForm from './components/UserForm.vue'

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
        title: 'Criar Usuário',
        href: '#',
    },
]

const props = defineProps({
    clients: Array,
    roles: Array
})

const userFormRef = ref()

const handleFetchClients = () => {
    if (props.clients) {
        userFormRef.value?.setClients(props.clients)
    }
}

const handleFetchRoles = () => {
    if (props.roles) {
        userFormRef.value?.setRoles(props.roles)
    }
}

const handleSubmit = (formData: any) => {
    // Usar Inertia.post para criar o usuário
    // Inertia.post('/admin/users', formData, {
    //     onSuccess: () => {
    //         // Redirecionar para a lista ou mostrar mensagem
    //     },
    //     onError: (errors) => {
    //         userFormRef.value?.setErrors(errors)
    //     }
    // })

    console.log('Dados do formulário para criação:', formData)
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <UserForm
                ref="userFormRef"
                :is-edit="false"
                @submit="handleSubmit"
                @fetch-clients="handleFetchClients"
                @fetch-roles="handleFetchRoles"
            />
        </div>
    </AppLayout>
</template>
