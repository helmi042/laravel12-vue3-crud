<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button, buttonVariants } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { deleteTransactionCategory } from '@/composables/useTransactionCategory';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

const typeLabels = {
    income: 'Pendapatan',
    expense: 'Pengeluaran',
};

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Jenis Transaksi',
        href: '/transaction-categories',
    },
    {
        title: 'Detail jenis',
        href: '/transaction-categories',
    },
];
</script>

<template>
    <Head title="Detail jenis transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Detail jenis transaksi</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Nama</div>
                            <div>{{ props.category.name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Tipe</div>
                            <div>{{ typeLabels[props.category.type] ?? props.category.type }}</div>
                        </div>
                        <div class="flex space-x-4">
                            <div class="w-32 font-semibold">Catatan</div>
                            <div class="flex-1">{{ props.category.description ?? '-' }}</div>
                        </div>
                        <div class="flex justify-between items-center space-x-4 mt-6">
                            <Link :href="route('transaction-categories.index')" :class="buttonVariants({ variant: 'outline' })">
                                Kembali
                            </Link>
                            <div>
                                <Link
                                    :href="route('transaction-categories.edit', props.category)"
                                    :class="buttonVariants({ variant: 'default' })"
                                >
                                    Edit
                                </Link>
                                <Button @click="deleteTransactionCategory(props.category.id)" class="ml-2" variant="destructive">
                                    Hapus
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
