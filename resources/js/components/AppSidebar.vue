<script setup lang="ts">
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
    SidebarRail,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Folder, LayoutGrid, Tags, Wallet } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Dompet',
        href: '/wallets',
        icon: Wallet,
    },
    {
        title: 'Transaksi',
        href: '/transactions',
        icon: Folder,
    },
    {
        title: 'Jenis Transaksi',
        href: '/transaction-categories',
        icon: Tags,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border border-sidebar-border/60">
        <SidebarHeader class="pb-1">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarSeparator />

        <SidebarContent class="px-1 pb-3">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarSeparator />

        <SidebarFooter class="pb-2">
            <NavUser />
        </SidebarFooter>

        <SidebarRail />
    </Sidebar>
    <slot />
</template>
