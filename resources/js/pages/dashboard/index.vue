<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

import {
    Activity,
    Users,
    Layers,
    Package,
    Clock,
    CloudUpload,
    AlertCircle,
    UserX
} from 'lucide-vue-next';

import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardDescription
} from '@/components/ui/card';
import {
    Alert,
    AlertTitle,
    AlertDescription
} from '@/components/ui/alert';
import MetricCard from '@/pages/dashboard/components/MetricCard.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url }
];

const page = usePage();
const user = computed<any>(() => (page.props as any).auth?.user || null);
const roleSlug = computed<string>(() => user.value?.role?.slug || '');

const hasRole = computed(() => !!roleSlug.value);
const isClient = computed(() => roleSlug.value === 'client');
const isDeveloper = computed(() => ['develop', 'developer'].includes(roleSlug.value));
const isAdmin = computed(() => roleSlug.value === 'admin');
const isDevOrAdmin = computed(() => isDeveloper.value || isAdmin.value);

// Data
const latestVersion = computed<any>(() => (page.props as any).latestVersion || null);
const versions = computed<any[]>(() => (page.props as any).versions || []);
const clientsCount = computed<number>(() => (page.props as any).clientsCount || 0);
const pendingDeploys = computed<any[]>(() => (page.props as any).pendingDeploys || []);
const usersCount = computed<number>(() => (page.props as any).usersCount || 0);
const pendingUserLinks = computed<any[]>(() => (page.props as any).pendingUserLinks || []);

// Derived
const totalReleases = computed(() => versions.value.length);
const latestVersionLabel = computed(
    () => latestVersion.value ? 'v' + latestVersion.value.version : '—'
);

// Helpers
const short = (text: string, max = 28) =>
    text?.length > max ? text.slice(0, max) + '…' : text;
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Sem role -->
        <div v-if="!hasRole" class="p-6">
            <Alert class="border-amber-300/60 bg-amber-50 dark:bg-amber-900/20">
                <AlertCircle class="w-5 h-5" />
                <AlertTitle class="font-semibold">Aguardando aprovação</AlertTitle>
                <AlertDescription class="text-sm leading-relaxed">
                    Sua conta ainda não possui role atribuída. Assim que aprovada você terá acesso ao conteúdo.
                </AlertDescription>
            </Alert>
        </div>

        <!-- Cliente -->
        <div v-else-if="isClient" class="p-6 space-y-8">
            <!-- Summary mini bar (cliente) -->
            <div class="grid gap-4 sm:grid-cols-2">
                <Card class="border border-indigo-200/60 dark:border-indigo-800/50 bg-indigo-50/60 dark:bg-indigo-950/30">
                    <CardHeader class="pb-2">
                        <CardTitle class="flex items-center gap-2 text-sm font-medium">
                            <Package class="w-4 h-4 text-indigo-600 dark:text-indigo-300" />
                            Última versão
                        </CardTitle>
                        <CardDescription>Status atual</CardDescription>
                    </CardHeader>
                    <CardContent class="pt-0">
                        <p class="text-xl font-semibold">
                            {{ latestVersionLabel }}
                        </p>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ latestVersion ? latestVersion.released_at : 'Sem data' }}
                        </p>
                    </CardContent>
                </Card>

                <Card class="border border-violet-200/60 dark:border-violet-800/50 bg-violet-50/60 dark:bg-violet-950/30">
                    <CardHeader class="pb-2">
                        <CardTitle class="flex items-center gap-2 text-sm font-medium">
                            <Layers class="w-4 h-4 text-violet-600 dark:text-violet-300" />
                            Total releases
                        </CardTitle>
                        <CardDescription>Histórico</CardDescription>
                    </CardHeader>
                    <CardContent class="pt-0">
                        <p class="text-xl font-semibold">
                            {{ totalReleases }}
                        </p>
                        <p class="text-xs text-muted-foreground mt-1">
                            Contagem acumulada
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card class="border border-indigo-200/60 dark:border-indigo-800/50 shadow-sm bg-gradient-to-br from-indigo-50/70 to-white dark:from-indigo-950/40 dark:to-neutral-900">
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-sm font-medium">
                        <Activity class="w-4 h-4 text-indigo-500" />
                        Detalhes da release atual
                    </CardTitle>
                    <CardDescription>Distribuição</CardDescription>
                </CardHeader>
                <CardContent v-if="latestVersion" class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 border border-indigo-500/20 text-xs font-medium">
              v{{ latestVersion.version }}
            </span>
                        <span class="text-xs text-muted-foreground">
              Lançada em {{ latestVersion.released_at }}
            </span>
                    </div>
                </CardContent>
                <CardContent v-else>
                    <p class="text-sm text-muted-foreground">Nenhuma versão disponível.</p>
                </CardContent>
            </Card>
        </div>

        <!-- Developer ou Admin -->
        <div v-else-if="isDevOrAdmin" class="p-6 space-y-10">
            <!-- Summary Bar (nova) -->
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <MetricCard
                    icon="package"
                    label="Última versão"
                    :value="latestVersionLabel"
                    :subtitle="latestVersion ? latestVersion.released_at : 'Sem data'"
                    tone="indigo"
                />
                <MetricCard
                    icon="layers"
                    label="Total releases"
                    :value="totalReleases"
                    subtitle="Acumulado"
                    tone="violet"
                />
                <MetricCard
                    icon="users"
                    label="Clientes"
                    :value="clientsCount"
                    subtitle="Ativos"
                    tone="emerald"
                />
                <MetricCard
                    v-if="isAdmin"
                    icon="users"
                    label="Usuários"
                    :value="usersCount"
                    subtitle="Cadastrados"
                    tone="sky"
                />
                <MetricCard
                    v-if="isAdmin"
                    icon="user-x"
                    label="Pendentes vínculo"
                    :value="pendingUserLinks.length"
                    subtitle="Sem cliente"
                    tone="rose"
                />
                <MetricCard
                    v-if="!isAdmin"
                    icon="cloud-upload"
                    label="Deploys pendentes"
                    :value="pendingDeploys.length"
                    subtitle="Aprovação"
                    tone="amber"
                />
            </div>

            <!-- Se admin, card de deploys fica abaixo junto dos outros (já existia) -->
            <section class="space-y-4">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight flex items-center gap-2">
                        <Activity class="w-5 h-5 text-sky-500" />
                        Visão geral detalhada
                    </h2>
                    <p class="text-xs text-muted-foreground mt-1">
                        Status consolidado do ecossistema
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
                    <!-- Mantidos (evita duplicar com Summary: pode omitir se quiser só Summary) -->
                    <MetricCard
                        icon="cloud-upload"
                        label="Deploys pendentes"
                        :value="pendingDeploys.length"
                        subtitle="Aprovação"
                        tone="amber"
                    />
                </div>
            </section>

            <!-- Atividades -->
            <section class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Deploys -->
                    <Card class="lg:col-span-1 h-full flex flex-col">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-sm font-medium flex items-center gap-2">
                                <CloudUpload class="w-4 h-4 text-amber-500" />
                                Deploys pendentes
                            </CardTitle>
                            <CardDescription>Aguardando aprovação</CardDescription>
                        </CardHeader>
                        <CardContent class="flex-1">
                            <ul v-if="pendingDeploys.length" class="space-y-3">
                                <li
                                    v-for="d in pendingDeploys.slice(0,10)"
                                    :key="d.id"
                                    class="group relative rounded-md border border-amber-300/30 dark:border-amber-900/40 p-3 hover:bg-amber-50/40 dark:hover:bg-amber-900/10 transition-colors"
                                >
                                    <div class="flex items-center justify-between">
                    <span class="text-sm font-medium flex items-center gap-2">
                      {{ short(d.name, 22) }}
                    </span>
                                        <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-500/20">
                      {{ d.environment }}
                    </span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground mt-1 flex items-center gap-1">
                                        <Clock class="w-3 h-3" /> {{ d.created_at }}
                                    </p>
                                </li>
                            </ul>
                            <p v-else class="text-sm text-muted-foreground">
                                Nenhum deploy pendente.
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Histórico versões -->
                    <Card class="lg:col-span-1 h-full flex flex-col">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-sm font-medium flex items-center gap-2">
                                <Layers class="w-4 h-4 text-violet-500" />
                                Histórico de versões
                            </CardTitle>
                            <CardDescription>Últimas releases</CardDescription>
                        </CardHeader>
                        <CardContent class="flex-1">
                            <ul v-if="versions.length" class="space-y-2">
                                <li
                                    v-for="ver in versions.slice(0,10)"
                                    :key="ver.version"
                                    class="flex items-center justify-between rounded-md border border-violet-300/30 dark:border-violet-900/40 px-3 py-2 text-sm hover:bg-violet-50/40 dark:hover:bg-violet-900/10 transition-colors"
                                >
                                    <span class="font-medium">v{{ ver.version }}</span>
                                    <span class="text-[11px] text-muted-foreground">
                    {{ ver.released_at }}
                  </span>
                                </li>
                            </ul>
                            <p v-else class="text-sm text-muted-foreground">Sem versões registradas.</p>
                        </CardContent>
                    </Card>

                    <!-- Pendentes de vínculo (admin) -->
                    <Card
                        v-if="isAdmin"
                        class="lg:col-span-1 h-full flex flex-col"
                    >
                        <CardHeader class="pb-3">
                            <CardTitle class="text-sm font-medium flex items-center gap-2">
                                <UserX class="w-4 h-4 text-rose-500" />
                                Usuários sem cliente
                            </CardTitle>
                            <CardDescription>Até 10 mais recentes</CardDescription>
                        </CardHeader>
                        <CardContent class="flex-1">
                            <ul v-if="pendingUserLinks.length" class="space-y-3">
                                <li
                                    v-for="u in pendingUserLinks.slice(0,10)"
                                    :key="u.id"
                                    class="group relative border border-rose-300/30 dark:border-rose-900/40 rounded-md p-3 hover:bg-rose-50/40 dark:hover:bg-rose-900/10 transition-colors"
                                >
                                    <span class="text-sm font-medium">{{ short(u.name, 24) }}</span>
                                    <p class="text-[11px] text-muted-foreground mt-1">
                                        {{ u.email }}
                                    </p>
                                    <p class="text-[10px] text-muted-foreground mt-1">
                                        Criado: {{ u.created_at }}
                                    </p>
                                </li>
                            </ul>
                            <p v-else class="text-sm text-muted-foreground">
                                Nenhum usuário pendente.
                            </p>
                        </CardContent>
                    </Card>

                    <div
                        v-else
                        class="lg:col-span-1 rounded-lg border border-dashed text-muted-foreground text-sm flex items-center justify-center p-6"
                    >
                        Nenhum painel adicional.
                    </div>
                </div>
            </section>
        </div>

        <!-- Fallback -->
        <div v-else class="p-6">
            <Alert>
                <AlertTitle>Perfil não configurado</AlertTitle>
                <AlertDescription>
                    Nenhum layout definido para esta role ({{ roleSlug }}).
                </AlertDescription>
            </Alert>
        </div>
    </AppLayout>
</template>
