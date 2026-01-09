<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button, buttonVariants } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { deleteTransactionCategory } from '@/composables/useTransactionCategory';

defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Jenis Transaksi',
        href: '/transaction-categories',
    },
];

const typeLabels = {
    income: 'Pendapatan',
    expense: 'Pengeluaran',
};
</script>

<template>
    <Head title="Jenis Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mt-3 flex items-center justify-between gap-3">
                <p class="text-sm text-muted-foreground">Kelola kategori pendapatan dan pengeluaran.</p>
                <Link :href="route('transaction-categories.create')" :class="buttonVariants({ variant: 'outline' })">
                    Tambah jenis
                </Link>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nama</TableHead>
                        <TableHead>Tipe</TableHead>
                        <TableHead>Catatan</TableHead>
                        <TableHead class="w-[140px]">Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="category in categories" :key="category.id">
                        <TableCell class="font-medium">{{ category.name }}</TableCell>
                        <TableCell>{{ typeLabels[category.type] ?? category.type }}</TableCell>
                        <TableCell class="text-sm text-muted-foreground">{{ category.description ?? '-' }}</TableCell>
                        <TableCell class="space-x-2">
                            <Link
                                :href="route('transaction-categories.show', category)"
                                :class="buttonVariants({ variant: 'secondary' })"
                            >
                                Detail
                            </Link>
                            <Link
                                :href="route('transaction-categories.edit', category)"
                                :class="buttonVariants({ variant: 'default' })"
                            >
                                Edit
                            </Link>
                            <Button variant="destructive" @click="deleteTransactionCategory(category.id)">Hapus</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
