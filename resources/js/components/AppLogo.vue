<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';

import logoLight from '@/assets/logo.png';
import logoDark from '@/assets/logo-branca-fluxxer.png';

interface Props {
    size?: number | string;
    alt?: string;
    rounded?: boolean;
    clickable?: boolean;
    withShadow?: boolean;
    background?: boolean;          // Exibe fundo para contraste
    padding?: number | string;     // Espaço interno
    fit?: 'contain' | 'cover';     // Estratégia de ajuste
    contrast?: 'normal' | 'high';  // Intensidade de filtros
    themeMode?: 'auto' | 'light' | 'dark'; // Força tema
    showBorder?: boolean;
    lazy?: boolean;
    skeleton?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    size: 56,
    alt: 'Logo',
    rounded: true,
    clickable: false,
    withShadow: false,
    background: false,
    padding: '0.35rem',
    fit: 'contain',
    contrast: 'normal',
    themeMode: 'auto',
    showBorder: false,
    lazy: true,
    skeleton: true
});

const loaded = ref(false);
const errored = ref(false);

const boxSize = computed(() =>
    typeof props.size === 'number' ? props.size + 'px' : props.size
);

const paddingValue = computed(() =>
    typeof props.padding === 'number' ? props.padding + 'px' : props.padding
);

const classes = computed(() => [
    'relative inline-flex select-none items-center justify-center shrink-0',
    'transition-all duration-300',
    props.rounded ? 'rounded-2xl' : 'rounded-lg',
    props.clickable ? 'cursor-pointer hover:scale-[1.035] active:scale-[0.97]' : '',
    props.withShadow ? 'shadow-sm hover:shadow-md' : '',
    props.background
        ? 'bg-white/80 dark:bg-neutral-900/70 backdrop-blur supports-[backdrop-filter]:backdrop-blur-md'
        : '',
    props.showBorder
        ? 'border border-neutral-200/70 dark:border-neutral-800/70'
        : '',
]);

const imgFilter = computed(() => {
    if (errored.value) return 'opacity:0;';
    const base = props.contrast === 'high'
        ? 'filter: drop-shadow(0 1px 1px rgba(0,0,0,0.25)) brightness(1.02) contrast(1.05);'
        : 'filter: drop-shadow(0 1px 1px rgba(0,0,0,0.18));';
    return base + (loaded.value ? 'opacity:1;' : 'opacity:0;');
});

const fitClass = computed(() =>
    props.fit === 'cover' ? 'object-cover' : 'object-contain'
);

const finalDark = computed(() =>
    props.themeMode === 'light' ? null : logoDark
);
const finalLight = computed(() =>
    props.themeMode === 'dark' ? logoDark : logoLight
);

const loadingAttr = computed(() => (props.lazy ? 'lazy' : 'eager'));

onMounted(() => {
    // Caso queira forçar pré-carregamento, pode-se adicionar aqui.
});

// Estilos inline do container
const wrapperStyle = computed(() => ({
    width: boxSize.value,
    height: boxSize.value,
    padding: props.background ? paddingValue.value : '0',
}));

// Placeholder skeleton
const showSkeleton = computed(
    () => props.skeleton && !loaded.value && !errored.value
);
</script>

<template>
    <div
        :class="classes"
        :style="wrapperStyle"
        role="img"
        :aria-label="alt"
    >
        <div
            v-if="showSkeleton"
            class="absolute inset-0 animate-pulse rounded-inherit bg-neutral-200/60 dark:bg-neutral-800/40"
        />
        <picture
            class="w-full h-full flex items-center justify-center"
            :data-theme-forced="props.themeMode !== 'auto'"
        >
            <source
                v-if="finalDark && props.themeMode !== 'light'"
                :srcset="finalDark"
                media="(prefers-color-scheme: dark)"
            />
            <img
                :src="finalLight"
                :alt="alt"
                :width="boxSize"
                :height="boxSize"
                decoding="async"
                :loading="loadingAttr"
                draggable="false"
                :class="[
          'w-full h-full',
          fitClass,
          'transition-opacity duration-500 ease-out',
          'pointer-events-none select-none'
        ]"
                :style="imgFilter"
                @load="loaded = true"
                @error="errored = true"
            />
        </picture>

        <!-- Overlay leve para contraste opcional -->
        <div
            v-if="props.background && props.contrast === 'high'"
            class="pointer-events-none absolute inset-0 rounded-inherit ring-1 ring-black/5 dark:ring-white/5"
        />
    </div>
</template>

<style scoped>
.rounded-inherit {
    border-radius: inherit;
}
</style>
