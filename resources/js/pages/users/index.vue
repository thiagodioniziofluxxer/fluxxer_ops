<script setup lang="ts">
import { computed, ref } from "vue"
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import admin from '@/routes/admin';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardTitle, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { UserIcon, UserCheckIcon, UserXIcon, BuildingIcon, Plus, Download } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

import UserTableList from '@/pages/users/components/UserTableList.vue';
import AlertMessage from '@/components/AlertMessage.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Users',
        href: admin.users.index().url,
    },
];

const props = defineProps<{
    users: any;
}>();

// Computed para estatísticas
const totalUsers = computed(() => props.users?.total || 0)
const verifiedUsers = computed(() =>
    props.users?.data?.filter((user: any) => user.email_verified_at)?.length || 0
)
const unverifiedUsers = computed(() => totalUsers.value - verifiedUsers.value)
const usersWithClients = computed(() =>
    props.users?.data?.filter((user: any) => user.client)?.length || 0
)
const adminUsers = computed(() =>
    props.users?.data?.filter((user: any) => user.role?.slug === 'admin')?.length || 0
)

const currentPage = computed(() => props.users?.current_page || 1)
const totalPages = computed(() => props.users?.last_page || 1)

// Funções de ação
const handleCreateUser = () => {
    router.visit(admin.users.create().url)
}

const handleExportCSV = () => {
    // Implementar exportação CSV
    console.log('Exportando CSV...')
}

// Filtros ativos
const activeFilter = ref('all')

const handleFilterChange = (filter: string) => {
    activeFilter.value = filter

    const params: Record<string, string> = {}

    switch (filter) {
        case 'verified':
            params.filter = 'verified'
            break
        case 'unverified':
            params.filter = 'unverified'
            break
        case 'admin':
            params.filter = 'admin'
            break
        default:
            // 'all' - sem filtros
            break
    }

    router.get(admin.users.index().url, params, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-1">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <!-- Alertas de sessão -->
                <AlertMessage />

                <!-- Resumo de estatísticas -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Total de Usuários</CardTitle>
                            <UserIcon class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ totalUsers }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ currentPage }}/{{ totalPages }} páginas
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">E-mails Verificados</CardTitle>
                            <UserCheckIcon class="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-green-600">{{ verifiedUsers }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ totalUsers > 0 ? Math.round((verifiedUsers / totalUsers) * 100) : 0 }}% do total
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Pendentes</CardTitle>
                            <UserXIcon class="h-4 w-4 text-orange-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-orange-600">{{ unverifiedUsers }}</div>
                            <p class="text-xs text-muted-foreground">
                                Aguardando verificação
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Com Clientes</CardTitle>
                            <BuildingIcon class="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-blue-600">{{ usersWithClients }}</div>
                            <p class="text-xs text-muted-foreground">
                                Vinculados a empresas
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Filtros rápidos e ações -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            :variant="activeFilter === 'all' ? 'default' : 'outline'"
                            class="cursor-pointer hover:bg-muted"
                            @click="handleFilterChange('all')"
                        >
                            Todos ({{ totalUsers }})
                        </Badge>
                        <Badge
                            :variant="activeFilter === 'verified' ? 'default' : 'outline'"
                            class="cursor-pointer hover:bg-muted"
                            @click="handleFilterChange('verified')"
                        >
                            Verificados ({{ verifiedUsers }})
                        </Badge>
                        <Badge
                            :variant="activeFilter === 'unverified' ? 'default' : 'outline'"
                            class="cursor-pointer hover:bg-muted"
                            @click="handleFilterChange('unverified')"
                        >
                            Pendentes ({{ unverifiedUsers }})
                        </Badge>
                        <Badge
                            :variant="activeFilter === 'admin' ? 'default' : 'outline'"
                            class="cursor-pointer hover:bg-muted"
                            @click="handleFilterChange('admin')"
                        >
                            Administradores ({{ adminUsers }})
                        </Badge>
                    </div>

                    <div class="flex gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            @click="handleExportCSV"
                        >
                            <Download class="h-4 w-4 mr-2" />
                            Exportar CSV
                        </Button>
                        <Button @click="handleCreateUser">
                            <Plus class="h-4 w-4 mr-2" />
                            Adicionar Usuário
                        </Button>
                    </div>
                </div>

                <!-- Tabela de usuários -->
                <div class="flex flex-col gap-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-xl">Lista de Usuários</CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <UserTableList :users="props.users" />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
