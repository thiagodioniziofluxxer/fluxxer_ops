<!-- resources/js/pages/clients/components/ClientTableList.vue -->
<script setup lang="ts">
import { computed, ref } from 'vue'
import {
    ColumnDef,
    getCoreRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    getFilteredRowModel,
    useVueTable,
    SortingState,
    ColumnFiltersState,
} from '@tanstack/vue-table'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
    ChevronDownIcon,
    ChevronUpIcon,
    MoreHorizontalIcon,
    PencilIcon,
    TrashIcon,
} from 'lucide-vue-next'

interface Client {
    id: number | string
    name: string
    status?: string | number | boolean
    active?: boolean | number // fallback legado
    users_count?: number
    users?: any[]
    created_at?: string
}

const props = defineProps<{
    clients: {
        data: Client[]
        from?: number
        to?: number
        total?: number
        current_page?: number
        last_page?: number
        prev_page_url?: string | null
        next_page_url?: string | null
    } | null
    loading?: boolean
}>()

const emit = defineEmits<{
    delete: [client: Client]
    edit: [client: Client]
    pageChange: [page: number]
}>()

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const showDeleteDialog = ref(false)
const clientToDelete = ref<Client | null>(null)

function isActive(client: Client): boolean {
    if (client.status !== undefined) {
        const v = client.status
        return v === 'active' || v === 'ativo' || v === 1 || v === true || v === '1'
    }
    return client.active === 1 || client.active === true || client.active === '1'
}

const columns: ColumnDef<Client>[] = [
    { accessorKey: 'name', header: 'Nome', enableSorting: true },
    { accessorKey: 'status', header: 'Status', enableSorting: true, cell: ({ row }) => row.original },
    { accessorKey: 'users_count', header: 'Usuários Vinculados', enableSorting: false, cell: ({ row }) => row.original },
    { accessorKey: 'created_at', header: 'Criado em', enableSorting: true, cell: ({ getValue }) => getValue() },
    { id: 'actions', header: '', enableSorting: false, cell: ({ row }) => row.original, size: 60 },
]

const data = computed<Client[]>(() => props.clients?.data ?? [])

const table = useVueTable({
    get data() { return data.value },
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: (updater) => {
        sorting.value = typeof updater === 'function' ? updater(sorting.value) : updater
    },
    onColumnFiltersChange: (updater) => {
        columnFilters.value = typeof updater === 'function' ? updater(columnFilters.value) : updater
    },
    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
    },
})

type BadgeVariant = 'default' | 'destructive' | 'outline' | 'secondary'
function getStatusVariant(client: Client): BadgeVariant {
    return isActive(client) ? 'default' : 'secondary'
}

function formatDate(dateString?: string) {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

function confirmDelete(client: Client) {
    clientToDelete.value = client
    showDeleteDialog.value = true
}

function handleDelete() {
    if (clientToDelete.value) {
        emit('delete', clientToDelete.value)
        showDeleteDialog.value = false
        clientToDelete.value = null
    }
}

function handlePageChange(page: number) {
    emit('pageChange', page)
}
</script>

<template>
    <div class="space-y-4">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                            :class="[
                                'select-none',
                                header.column.getCanSort() ? 'cursor-pointer hover:bg-muted/50' : '',
                                header.column.id === 'actions' ? 'w-[60px]' : '',
                              ]"
                            @click="header.column.getCanSort() ? header.column.getToggleSortingHandler()?.($event) : undefined"
                        >
                            <div class="flex items-center space-x-2">
                                <span>{{ header.isPlaceholder ? '' : header.column.columnDef.header }}</span>
                                <div v-if="header.column.getCanSort()" class="flex flex-col">
                                    <ChevronUpIcon
                                        :class="[
                                          'h-3 w-3',
                                          header.column.getIsSorted() === 'asc'
                                            ? 'text-foreground'
                                            : 'text-muted-foreground',
                                        ]"
                                    />
                                    <ChevronDownIcon
                                        :class="[
                                          '-mt-1 h-3 w-3',
                                          header.column.getIsSorted() === 'desc'
                                            ? 'text-foreground'
                                            : 'text-muted-foreground',
                                        ]"
                                    />
                                </div>
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="loading" v-for="n in 5" :key="`loading-${n}`" class="animate-pulse">
                        <TableCell v-for="i in columns.length" :key="i" class="py-4">
                            <div class="h-4 w-full rounded bg-muted"></div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-else-if="!data.length" class="hover:bg-transparent">
                        <TableCell :colspan="columns.length" class="py-8 text-center text-muted-foreground">
                            Nenhum cliente encontrado
                        </TableCell>
                    </TableRow>

                    <TableRow v-else v-for="row in table.getRowModel().rows" :key="row.id" class="hover:bg-muted/50">
                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="py-4">
                              <span v-if="cell.column.id === 'name'" class="font-medium">
                                {{ cell.getValue() }}
                              </span>

                            <Badge
                                v-else-if="cell.column.id === 'status'"
                                :variant="getStatusVariant(row.original)"
                            >
                                {{ isActive(row.original) ? 'Ativo' : 'Inativo' }}
                            </Badge>

                            <span v-else-if="cell.column.id === 'users_count'" class="text-sm">
                                {{
                                    row.original.users_count !== undefined
                                        ? row.original.users_count
                                        : (row.original.users?.length || 0)
                                }}
                            </span>

                            <span v-else-if="cell.column.id === 'created_at'" class="text-sm text-muted-foreground">
                                {{ formatDate(cell.getValue() as string) }}
                              </span>

                            <DropdownMenu v-else-if="cell.column.id === 'actions'">
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                                        <MoreHorizontalIcon class="h-4 w-4" />
                                        <span class="sr-only">Abrir menu</span>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem :as="'a'" :href="`/admin/clients/${row.original.id}/edit`">
                                        <PencilIcon class="mr-2 h-4 w-4" /> Editar
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click="confirmDelete(row.original)"
                                        class="text-destructive focus:text-destructive"
                                    >
                                        <TrashIcon class="mr-2 h-4 w-4" /> Deletar
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>

                            <span v-else class="text-sm">
                {{ cell.getValue() || '-' }}
              </span>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="mx-2 flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Mostrando {{ props.clients?.from || 0 }} a {{ props.clients?.to || 0 }} de {{ props.clients?.total || 0 }} resultado(s)
            </div>
            <div class="flex items-center space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!props.clients?.prev_page_url || loading"
                    @click="handlePageChange((props.clients?.current_page || 1) - 1)"
                >
                    Anterior
                </Button>
                <span class="text-sm">
          Página {{ props.clients?.current_page || 1 }} de {{ props.clients?.last_page || 1 }}
        </span>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!props.clients?.next_page_url || loading"
                    @click="handlePageChange((props.clients?.current_page || 1) + 1)"
                >
                    Próxima
                </Button>
            </div>
        </div>

        <div v-if="showDeleteDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80">
            <div class="mx-4 w-full max-w-lg rounded-lg bg-white p-6">
                <h2 class="mb-2 text-lg font-semibold">Confirmar exclusão</h2>
                <p class="mb-4 text-gray-600">
                    Tem certeza que deseja deletar o cliente "{{ clientToDelete?.name }}"? Esta ação não pode ser desfeita.
                </p>
                <div class="flex justify-end space-x-2">
                    <Button variant="outline" @click="showDeleteDialog = false">Cancelar</Button>
                    <Button variant="destructive" @click="handleDelete">Deletar</Button>
                </div>
            </div>
        </div>
    </div>
</template>
