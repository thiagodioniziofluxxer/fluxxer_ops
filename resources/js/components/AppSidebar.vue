<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link , usePage} from '@inertiajs/vue3';
import { Users, BookOpen, Building, Server, LucideCloudy, LucideGitCommitHorizontal, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

import admin from '@/routes/admin';
import { computed } from 'vue';


const page = usePage();
const auth = computed(() => page.props.auth);


const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        show: true,
    },

    ...(auth.value?.can?.clientViewAny ? [
        {
            title: 'Client',
            href: admin.clients.index(),
            icon: Building,
            show: true,
        },
    ] : []),
    ...(auth.value?.can?.hostViewAny ? [
        {
            title: 'Host',
            href: admin.hosts.index(),
            icon: Server,
            show: true,
        },
    ] : []),
    ...(auth.value?.can?.versionViewAny ? [
        {
            title: 'Version',
            href: admin.versions.index(),
            icon: LucideGitCommitHorizontal,
            show: true,
        },
    ] : []),
    ...(auth.value?.can?.deployViewAny ? [
        {
            title: 'Deploy',
            href: admin.deploys.index(),
            icon: LucideCloudy,
            show: true,
        },
    ] : []),



];

const footerNavItems: NavItem[] = [
    ...(auth.value?.can?.usersViewAny ? [
        {
            title: 'Users',
            href: admin.users.index(),
            icon: Users,
            show: true,
        },
    ] : []),
    {
        title: 'Documentation',
        href: 'https://docs.example.com',
        icon: BookOpen,
        isExternal: true,
    }

];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
