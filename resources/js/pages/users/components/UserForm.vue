<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import {
    UserIcon,
    BuildingIcon,
    ShieldIcon
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import admin from '@/routes/admin'

interface User {
    id?: number
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

const props = defineProps<{
    user?: User | null
    isEdit?: boolean
}>()

const emit = defineEmits<{
    submit: [data: any]
    'fetch-clients': []
    'fetch-roles': []
}>()

// Estados do formulário
const form = ref({
    name: props.user?.name || '',
    client_id: props.user?.client_id || null,
    role_id: props.user?.role_id || null
})

const clients = ref<Client[]>([])
const roles = ref<Role[]>([])
const loading = ref(false)
const errors = ref<Record<string, string[]>>({})

// Computed
const isEditMode = computed(() => props.isEdit && props.user?.id)
const formTitle = computed(() => isEditMode.value ? 'Editar Usuário' : 'Criar Usuário')
const submitText = computed(() => isEditMode.value ? 'Atualizar' : 'Criar')

const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const getRoleVariant = (roleSlug: string): "default" | "destructive" | "secondary" | "outline" => {
    const variants: Record<string, "default" | "destructive" | "secondary" | "outline"> = {
        'admin': 'destructive',
        'manager': 'default',
        'user': 'secondary',
    }
    return variants[roleSlug] || 'outline'
}

// Funções
const fetchClients = async () => {
    emit('fetch-clients')
}

const fetchRoles = async () => {
    emit('fetch-roles')
}

const handleSubmit = () => {
    errors.value = {}
    loading.value = true

    const formData = {
        ...form.value,
        id: props.user?.id
    }

    emit('submit', formData)
}

const handleCancel = () => {
    router.visit(admin.users.index().url)
}

const setClients = (clientList: Client[]) => {
    clients.value = clientList
}

const setRoles = (roleList: Role[]) => {
    roles.value = roleList
}

const setErrors = (errorList: Record<string, string[]>) => {
    errors.value = errorList
    loading.value = false
}

const setLoading = (loadingState: boolean) => {
    loading.value = loadingState
}

// Expose functions para o componente pai
defineExpose({
    setClients,
    setRoles,
    setErrors,
    setLoading
})

onMounted(() => {
    fetchClients()
    fetchRoles()
})
</script>

<template>
    <Card class="w-full max-w-2xl mx-auto">
        <CardHeader>
            <CardTitle class="flex items-center gap-2">
                <UserIcon class="h-5 w-5" />
                {{ formTitle }}
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Avatar Section (somente visualização) -->
                <div v-if="isEditMode && user" class="flex flex-col items-center space-y-4">
                    <Avatar class="h-24 w-24">
                        <AvatarImage :src="user.avatar" :alt="user.name" />
                        <AvatarFallback class="text-lg">
                            {{ getInitials(user.name || 'NN') }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="text-center">
                        <p class="text-sm text-muted-foreground">Avatar gerenciado pelo WorkOS</p>
                    </div>
                </div>

                <!-- Informações do WorkOS (somente visualização) -->
                <div v-if="isEditMode && user" class="space-y-4 p-4 bg-muted/50 rounded-lg">
                    <h3 class="text-sm font-medium text-muted-foreground">Informações do WorkOS (somente leitura)</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label class="text-muted-foreground">E-mail</Label>
                            <Input :value="user.email" disabled class="bg-muted" />
                        </div>
                        <div class="space-y-2">
                            <Label class="text-muted-foreground">Status do E-mail</Label>
                            <Badge :variant="user.email_verified_at ? 'default' : 'secondary'">
                                {{ user.email_verified_at ? 'Verificado' : 'Pendente de verificação' }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Informações Editáveis -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium">Informações Editáveis</h3>

                    <div class="space-y-2">
                        <Label for="name" class="flex items-center gap-1">
                            <UserIcon class="h-4 w-4" />
                            Nome completo *
                        </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="Digite o nome completo"
                            required
                            :disabled="loading"
                            :class="errors.name ? 'border-red-500' : ''"
                        />
                        <span v-if="errors.name" class="text-sm text-red-500">
                            {{ errors.name[0] }}
                        </span>
                    </div>
                </div>

                <!-- Cliente e Role -->
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label class="flex items-center gap-1">
                            <BuildingIcon class="h-4 w-4" />
                            Cliente
                        </Label>
                        <select
                            v-model="form.client_id"
                            :disabled="loading"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            :class="errors.client_id ? 'border-red-500' : ''"
                        >
                            <option :value="null">Sem cliente</option>
                            <option
                                v-for="client in clients"
                                :key="client.id"
                                :value="client.id"
                            >
                                {{ client.name }}
                            </option>
                        </select>
                        <span v-if="errors.client_id" class="text-sm text-red-500">
                            {{ errors.client_id[0] }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <Label class="flex items-center gap-1">
                            <ShieldIcon class="h-4 w-4" />
                            Papel *
                        </Label>
                        <select
                            v-model="form.role_id"
                            required
                            :disabled="loading"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            :class="errors.role_id ? 'border-red-500' : ''"
                        >
                            <option value="">Selecione um papel</option>
                            <option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.id"
                            >
                                {{ role.name }}
                            </option>
                        </select>
                        <span v-if="errors.role_id" class="text-sm text-red-500">
                            {{ errors.role_id[0] }}
                        </span>
                    </div>
                </div>

                <!-- Informação sobre WorkOS -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <div class="text-blue-600 mt-0.5">ℹ️</div>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Integração com WorkOS</p>
                            <p>E-mail, senha e avatar são gerenciados pelo WorkOS e não podem ser editados aqui.</p>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="flex gap-3 pt-6">
                    <Button
                        type="submit"
                        class="flex-1"
                        :disabled="loading"
                    >
                        {{ loading ? 'Salvando...' : submitText }}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="loading"
                        @click="handleCancel"
                    >
                        Cancelar
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
