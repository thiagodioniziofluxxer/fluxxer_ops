<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';
import { AlertTriangle, CheckCircle, Info, X, XCircle } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

interface FlashMessage {
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
}

const page = usePage();
const show = ref(true);

// Computed para pegar as mensagens flash da session
const flashMessages = computed(() => {
    const props = page.props as any;
    const messages: FlashMessage[] = [];
    console.log('props.flash', props.flash);
    // Verificar diferentes tipos de mensagem da session
    if (props.flash?.success) {
        messages.push({ type: 'success', message: props.flash.success });
    }
    if (props.flash?.error) {
        messages.push({ type: 'error', message: props.flash.error });
    }
    if (props.flash?.warning) {
        messages.push({ type: 'warning', message: props.flash.warning });
    }
    if (props.flash?.info) {
        messages.push({ type: 'info', message: props.flash.info });
    }

    return messages;
});

const getIcon = (type: string) => {
    const icons = {
        success: CheckCircle,
        error: XCircle,
        warning: AlertTriangle,
        info: Info,
    };
    return icons[type as keyof typeof icons] || Info;
};

const getVariant = (type: string) => {
    const variants = {
        success: 'default',
        error: 'destructive',
        warning: 'default',
        info: 'default',
    };
    return variants[type as keyof typeof variants] || 'default';
};

const getColorClasses = (type: string) => {
    const classes = {
        success: 'border-green-200 bg-green-50 text-green-800',
        error: 'border-red-200 bg-red-50 text-red-800',
        warning: 'border-yellow-200 bg-yellow-50 text-yellow-800',
        info: 'border-blue-200 bg-blue-50 text-blue-800',
    };
    return classes[type as keyof typeof classes] || classes.info;
};

const dismissAlert = () => {
    show.value = false;
};

// Auto-dismiss para mensagens de sucesso após 5 segundos
onMounted(() => {
    if (flashMessages.value.some((msg) => msg.type === 'success')) {
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
});
</script>

<template>
    <div v-if="flashMessages.length > 0 && show" class="space-y-3">
        <div
            v-for="(message, index) in flashMessages"
            :key="index"
            :class="[
                'relative rounded-lg border p-4 shadow-sm transition-all duration-300',
                getColorClasses(message.type),
            ]"
        >
            <div class="flex items-start gap-3">
                <component
                    :is="getIcon(message.type)"
                    class="mt-0.5 h-5 w-5 flex-shrink-0"
                    :class="{
                        'text-green-600': message.type === 'success',
                        'text-red-600': message.type === 'error',
                        'text-yellow-600': message.type === 'warning',
                        'text-blue-600': message.type === 'info',
                    }"
                />
                <div class="flex-1">
                    <p class="text-sm font-medium">
                        {{ message.message }}
                    </p>
                </div>
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-6 w-6 p-0 hover:bg-black/10"
                    @click="dismissAlert"
                >
                    <X class="h-4 w-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
