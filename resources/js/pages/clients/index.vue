<!-- resources/js/pages/clients/index.vue -->
<script setup lang="ts">
import { computed, ref } from 'vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import admin from '@/routes/admin'
import { type BreadcrumbItem } from '@/types'
import { Card, CardContent, CardTitle, CardHeader } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { BuildingIcon, CheckCircleIcon, XCircleIcon, UsersIcon, Plus, Download } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import ClientTableList from '@/pages/clients/components/ClientTableList.vue'
import AlertMessage from '@/components/AlertMessage.vue'

const props = defineProps<{ clients: any }>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Clientes', href: admin.clients.index().url },
]

function isClientActive(client: any): boolean {
    if ('status' in client && client.status !== null && client.status !== undefined) {
        const v = client.status
        return v === 'active' || v === 'ativo' || v === 1 || v === true || v === '1'
    }
    // fallback legado
    const a = client.active
    return a === 1 || a === true || a === '1'
}

const totalClients = computed(() => props.clients?.total || 0)
const activeClients = computed(() =>
    props.clients?.data?.filter((c: any) => isClientActive(c))?.length || 0
)
const inactiveClients = computed(() => totalClients.value - activeClients.value)
const clientsWithUsers = computed(() =>
    props.clients?.data?.filter((c: any) => Array.isArray(c.users) && c.users.length > 0)?.length || 0
)
const currentPage = computed(() => props.clients?.current_page || 1)
const totalPages = computed(() => props.clients?.last_page || 1)

const handleCreateClient = () => {
    router.visit(admin.clients.create().url)
}

const handleExportCSV = () => {
    console.log('Exportando clientes para CSV...')
}

const activeFilter = ref('all')

const handleFilterChange = (filter: string) => {
    activeFilter.value = filter
    const params: Record<string, string> = {}
    switch (filter) {
        case 'active':
            params.filter = 'active'
            break
        case 'inactive':
            params.filter = 'inactive'
            break
        case 'with_users':
            params.filter = 'with_users'
            break
    }
    router.get(admin.clients.index().url, params, {
        preserveState: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-1">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <AlertMessage />

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Total de Clientes</CardTitle>
                            <BuildingIcon class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ totalClients }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ currentPage }}/{{ totalPages }} páginas
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Ativos</CardTitle>
                            <CheckCircleIcon class="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-green-600">{{ activeClients }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ totalClients > 0 ? Math.round((activeClients / totalClients) * 100) : 0 }}% do total
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Inativos</CardTitle>
                            <XCircleIcon class="h-4 w-4 text-orange-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-orange-600">{{ inactiveClients }}</div>
                            <p class="text-xs text-muted-foreground">
                                Necessitam atenção
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Com Usuários Vinculados</CardTitle>
                            <UsersIcon class="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-blue-600">{{ clientsWithUsers }}</div>
                            <p class="text-xs text-muted-foreground">
                                Com equipe relacionada
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap gap-2">
                        <Badge :variant="activeFilter === 'all' ? 'default' : 'outline'" class="cursor-pointer hover:bg-muted"
                               @click="handleFilterChange('all')">
                            Todos ({{ totalClients }})
                        </Badge>
                        <Badge :variant="activeFilter === 'active' ? 'default' : 'outline'" class="cursor-pointer hover:bg-muted"
                               @click="handleFilterChange('active')">
                            Ativos ({{ activeClients }})
                        </Badge>
                        <Badge :variant="activeFilter === 'inactive' ? 'default' : 'outline'" class="cursor-pointer hover:bg-muted"
                               @click="handleFilterChange('inactive')">
                            Inativos ({{ inactiveClients }})
                        </Badge>
                        <Badge :variant="activeFilter === 'with_users' ? 'default' : 'outline'" class="cursor-pointer hover:bg-muted"
                               @click="handleFilterChange('with_users')">
                            Com Usuários ({{ clientsWithUsers }})
                        </Badge>
                    </div>

                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" @click="handleExportCSV">
                            <Download class="h-4 w-4 mr-2" />
                            Exportar CSV
                        </Button>
                        <Button @click="handleCreateClient">
                            <Plus class="h-4 w-4 mr-2" />
                            Adicionar Cliente
                        </Button>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-xl">Lista de Clientes</CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <ClientTableList :clients="props.clients" />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
