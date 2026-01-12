<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <section class="space-y-3">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Jenis Transaksi</h2>
                    <p class="text-sm text-muted-foreground">Kelola kategori pendapatan dan pengeluaran.</p>
                </div>
                <Card>
                    <CardHeader class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <CardTitle>Daftar kategori</CardTitle>
                            <CardDescription>Gunakan kategori agar laporan lebih rapi.</CardDescription>
                        </div>
                        <Link :href="route('transaction-categories.create')" :class="buttonVariants({ variant: 'outline' })">
                            Tambah jenis
                        </Link>
                    </CardHeader>
                    <CardContent class="p-0">
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
                                <template v-if="categories.length">
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
                                </template>
                                <TableEmpty v-else :colspan="4">
                                    Belum ada kategori. Buat kategori pertama untuk memudahkan transaksi.
                                </TableEmpty>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
