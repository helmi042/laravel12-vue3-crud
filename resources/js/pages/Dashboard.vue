<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const summaryCards = [
    {
        title: 'Kas Masuk',
        amount: 'Rp 0',
        description: 'Total pemasukan bulan ini.',
    },
    {
        title: 'Kas Keluar',
        amount: 'Rp 0',
        description: 'Total pengeluaran bulan ini.',
    },
    {
        title: 'Saldo',
        amount: 'Rp 0',
        description: 'Saldo kas terakhir.',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-3">
                <Card v-for="card in summaryCards" :key="card.title">
                    <CardHeader>
                        <CardTitle class="text-base">{{ card.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div class="text-2xl font-semibold text-foreground">{{ card.amount }}</div>
                        <p class="text-sm text-muted-foreground">{{ card.description }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
