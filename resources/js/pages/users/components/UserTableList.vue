<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    ColumnDef,
    ColumnFiltersState,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    SortingState,
    useVueTable,
} from '@tanstack/vue-table';

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    ChevronDownIcon,
    ChevronUpIcon,
    MoreHorizontalIcon,
    PencilIcon,
    TrashIcon,
} from 'lucide-vue-next';

const props = defineProps<{
    users: any;
    loading?: boolean;
}>();

// Estados reativos
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const showDeleteDialog = ref(false);
const userToDelete = ref<any>(null);

// Emits para as ações
const emit = defineEmits<{
    edit: [user: any];
    delete: [user: any];
    pageChange: [page: number];
}>();

const columns: ColumnDef<any>[] = [
    {
        accessorKey: 'avatar',
        header: '',
        cell: ({ getValue, row }) => ({
            avatar: getValue(),
            name: row.original.name,
        }),
        enableSorting: false,
        size: 60,
    },
    {
        accessorKey: 'name',
        header: 'Nome',
        enableSorting: true,
    },
    {
        accessorKey: 'email',
        header: 'E-mail',
        enableSorting: true,
    },
    {
        accessorKey: 'client',
        header: 'Cliente',
        cell: ({ row }) => row.original.client,
        enableSorting: false,
    },
    {
        accessorKey: 'role',
        header: 'Papel',
        cell: ({ row }) => row.original.role,
        enableSorting: false,
    },
    {
        accessorKey: 'email_verified_at',
        header: 'Status',
        cell: ({ getValue }) => getValue(),
        enableSorting: false,
    },
    {
        accessorKey: 'created_at',
        header: 'Criado em',
        cell: ({ getValue }) => getValue(),
        enableSorting: true,
    },
    {
        id: 'actions',
        header: '',
        cell: ({ row }) => row.original,
        enableSorting: false,
        size: 60,
    },
];

const data = computed(() => props.users?.data ?? []);

const table = useVueTable({
    get data() {
        return data.value;
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: (updaterOrValue) => {
        sorting.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(sorting.value)
                : updaterOrValue;
    },
    onColumnFiltersChange: (updaterOrValue) => {
        columnFilters.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(columnFilters.value)
                : updaterOrValue;
    },
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
    },
});


function confirmDelete(user: any) {
    userToDelete.value = user;
    showDeleteDialog.value = true;
}

function handleDelete() {
    if (userToDelete.value) {
        emit('delete', userToDelete.value);
        showDeleteDialog.value = false;
        userToDelete.value = null;
    }
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

function getInitials(name: string) {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function handlePageChange(page: number) {
    emit('pageChange', page);
}

function getRoleVariant(roleName: string) {
    const variants: Record<string, string> = {
        Admin: 'destructive',
        Manager: 'default',
        User: 'secondary',
    };
    return variants[roleName] || 'outline';
}
</script>

<template>
    <div class="space-y-4">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                            :class="[
                                'select-none',
                                header.column.getCanSort()
                                    ? 'cursor-pointer hover:bg-muted/50'
                                    : '',
                                header.column.id === 'actions'
                                    ? 'w-[70px]'
                                    : '',
                                header.column.id === 'avatar' ? 'w-[60px]' : '',
                            ]"
                            @click="
                                header.column.getCanSort()
                                    ? header.column.getToggleSortingHandler()?.(
                                          $event,
                                      )
                                    : undefined
                            "
                        >
                            <div class="flex items-center space-x-2">
                                <span>{{
                                    header.isPlaceholder
                                        ? ''
                                        : header.column.columnDef.header
                                }}</span>
                                <div
                                    v-if="header.column.getCanSort()"
                                    class="flex flex-col"
                                >
                                    <ChevronUpIcon
                                        :class="[
                                            'h-3 w-3',
                                            header.column.getIsSorted() ===
                                            'asc'
                                                ? 'text-foreground'
                                                : 'text-muted-foreground',
                                        ]"
                                    />
                                    <ChevronDownIcon
                                        :class="[
                                            '-mt-1 h-3 w-3',
                                            header.column.getIsSorted() ===
                                            'desc'
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
                    <TableRow
                        v-if="loading"
                        v-for="n in 5"
                        :key="`loading-${n}`"
                        class="animate-pulse"
                    >
                        <TableCell
                            v-for="i in columns.length"
                            :key="i"
                            class="py-4"
                        >
                            <div class="h-4 w-full rounded bg-muted"></div>
                        </TableCell>
                    </TableRow>

                    <TableRow
                        v-else-if="!data.length"
                        class="hover:bg-transparent"
                    >
                        <TableCell
                            :colspan="columns.length"
                            class="py-8 text-center text-muted-foreground"
                        >
                            Nenhum usuário encontrado
                        </TableCell>
                    </TableRow>

                    <TableRow
                        v-else
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                        class="hover:bg-muted/50"
                    >
                        <TableCell
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                            class="py-4"
                        >
                            <!-- Avatar -->
                            <Avatar
                                v-if="cell.column.id === 'avatar'"
                                class="h-10 w-10"
                            >
                                <AvatarImage
                                    :src="cell.getValue() as any"
                                    :alt="(cell.getValue() as any)?.name"
                                />
                                <AvatarFallback>
                                    {{
                                        getInitials(
                                            (cell.getValue() as any)?.name ||
                                                'NN',
                                        )
                                    }}
                                </AvatarFallback>
                            </Avatar>

                            <!-- Cliente -->
                            <div v-else-if="cell.column.id === 'client'">
                                <Badge
                                    v-if="cell.getValue() as any"
                                    variant="outline"
                                >
                                    {{ (cell.getValue() as any).name }}
                                </Badge>
                                <span
                                    v-else
                                    class="text-sm text-muted-foreground"
                                    >Sem cliente</span
                                >

                            </div>

                            <!-- Role/Papel -->
                            <Badge
                                v-else-if="cell.column.id === 'role'"
                                :variant="
                                    getRoleVariant(
                                        (cell.getValue() as any)?.name,
                                    )
                                "
                            >
                                {{
                                    (cell.getValue() as any)?.name ||
                                    'Sem papel'
                                }}
                            </Badge>

                            <!-- Status -->
                            <Badge
                                v-else-if="
                                    cell.column.id === 'email_verified_at'
                                "
                                :variant="
                                    cell.getValue() ? 'default' : 'secondary'
                                "
                            >
                                {{
                                    cell.getValue() ? 'Verificado' : 'Pendente'
                                }}
                            </Badge>

                            <!-- Data formatada -->
                            <span
                                v-else-if="cell.column.id === 'created_at'"
                                class="text-sm text-muted-foreground"
                            >
                                {{ formatDate(cell.getValue() as string) }}
                            </span>

                            <!-- Ações -->
                            <DropdownMenu
                                v-else-if="cell.column.id === 'actions'"
                            >
                                <DropdownMenuTrigger as-child>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 w-8 p-0"
                                    >
                                        <MoreHorizontalIcon class="h-4 w-4" />
                                        <span class="sr-only">Abrir menu</span>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                 <DropdownMenuItem
                                        :as="'a'"
                                        :href="`/admin/users/${row.original.id}/edit`"
                                    >
                                        <PencilIcon class="mr-2 h-4 w-4" />
                                        Editar
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click="confirmDelete(cell.getValue())"
                                        class="text-destructive focus:text-destructive"
                                    >
                                        <TrashIcon class="mr-2 h-4 w-4" />
                                        Deletar
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>

                            <!-- Outros campos -->
                            <span v-else class="font-medium">{{
                                cell.getValue()
                            }}</span>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Paginação melhorada -->
        <div class="mx-2 flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Mostrando {{ props.users?.from || 0 }} a
                {{ props.users?.to || 0 }} de
                {{ props.users?.total || 0 }} resultado(s)
            </div>

            <div class="flex items-center space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!props.users?.prev_page_url || loading"
                    @click="handlePageChange(props.users.current_page - 1)"
                >
                    Anterior
                </Button>

                <div class="flex items-center space-x-1">
                    <span class="text-sm">
                        Página {{ props.users?.current_page || 1 }} de
                        {{ props.users?.last_page || 1 }}
                    </span>
                </div>

                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!props.users?.next_page_url || loading"
                    @click="handlePageChange(props.users.current_page + 1)"
                >
                    Próxima
                </Button>
            </div>
        </div>

        <!-- Dialog simples para confirmação -->
        <div
            v-if="showDeleteDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
        >
            <div class="mx-4 w-full max-w-lg rounded-lg bg-white p-6">
                <h2 class="mb-2 text-lg font-semibold">Confirmar exclusão</h2>
                <p class="mb-4 text-gray-600">
                    Tem certeza que deseja deletar o usuário "{{
                        userToDelete?.name
                    }}"? Esta ação não pode ser desfeita.
                </p>
                <div class="flex justify-end space-x-2">
                    <Button variant="outline" @click="showDeleteDialog = false">
                        Cancelar
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        Deletar
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
