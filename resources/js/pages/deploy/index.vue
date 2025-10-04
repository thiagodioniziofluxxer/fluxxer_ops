<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import admin from '@/routes/admin';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Deploys',
        href: admin.users.index().url,
    },
];

</script>

<template>
    <Head title="Deploys" />

    <AppLayout :breadcrumbs="breadcrumbs">

    </AppLayout>
</template>
