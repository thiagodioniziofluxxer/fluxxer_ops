<!-- resources/js/pages/dashboard/components/MetricCard.vue -->
<script lang="ts" setup>
import { computed } from 'vue';
import {
    Package,
    Layers,
    Users,
    CloudUpload,
    UserX
} from 'lucide-vue-next';

interface Props {
    icon: string;
    label: string;
    value: string | number;
    subtitle?: string;
    tone?: string;
}

const props = withDefaults(defineProps<Props>(), {
    subtitle: '',
    tone: 'indigo'
});

const iconMap: Record<string, any> = {
    package: Package,
    layers: Layers,
    users: Users,
    'cloud-upload': CloudUpload,
    'user-x': UserX
};

const toneMap: Record<string, string> = {
    indigo: 'from-indigo-50/80 to-white dark:from-indigo-950/40 dark:to-neutral-900 border-indigo-200/60 dark:border-indigo-800/50 text-indigo-600 dark:text-indigo-300',
    violet: 'from-violet-50/80 to-white dark:from-violet-950/40 dark:to-neutral-900 border-violet-200/60 dark:border-violet-800/50 text-violet-600 dark:text-violet-300',
    emerald: 'from-emerald-50/80 to-white dark:from-emerald-950/40 dark:to-neutral-900 border-emerald-200/60 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-300',
    sky: 'from-sky-50/80 to-white dark:from-sky-950/40 dark:to-neutral-900 border-sky-200/60 dark:border-sky-800/50 text-sky-600 dark:text-sky-300',
    rose: 'from-rose-50/80 to-white dark:from-rose-950/40 dark:to-neutral-900 border-rose-200/60 dark:border-rose-800/50 text-rose-600 dark:text-rose-300',
    amber: 'from-amber-50/80 to-white dark:from-amber-950/40 dark:to-neutral-900 border-amber-200/60 dark:border-amber-800/50 text-amber-600 dark:text-amber-300'
};

const Icon = computed(() => iconMap[props.icon] ?? Package);
const tone = computed(() => toneMap[props.tone] ?? toneMap.indigo);
</script>

<template>
    <div
        class="relative overflow-hidden rounded-xl border shadow-sm bg-gradient-to-br p-4 flex flex-col justify-between h-full group"
        :class="tone"
    >
        <div class="flex items-start justify-between">
            <div class="flex flex-col gap-1">
                <span class="text-[11px] uppercase tracking-wide font-medium opacity-70">{{ label }}</span>
                <span class="text-2xl font-semibold tabular-nums">{{ value }}</span>
            </div>
            <div class="p-2 rounded-md bg-white/60 dark:bg-white/5 backdrop-blur-sm border shadow-sm">
                <Icon class="w-5 h-5" />
            </div>
        </div>
        <p v-if="subtitle" class="text-[11px] mt-3 text-muted-foreground">
            {{ subtitle }}
        </p>
    </div>
</template>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: rgba(120,120,120,0.25);
    border-radius: 3px;
}
</style>
