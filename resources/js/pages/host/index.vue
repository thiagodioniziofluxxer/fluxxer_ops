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
        title: 'Hosts',
        href: admin.users.index().url,
    },
];

</script>

<template>
    <Head title="Hosts" />

    <AppLayout :breadcrumbs="breadcrumbs">

    </AppLayout>
</template>
