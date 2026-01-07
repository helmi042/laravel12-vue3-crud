<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button, buttonVariants } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Transactions',
        href: '/transactions',
    },
];

const transactions = [
    {
        id: 1,
        date: '2024-08-01',
        description: 'Penjualan tunai',
        type: 'Masuk',
        amount: 'Rp 1.250.000',
    },
    {
        id: 2,
        date: '2024-08-02',
        description: 'Pembelian bahan baku',
        type: 'Keluar',
        amount: 'Rp 450.000',
    },
    {
        id: 3,
        date: '2024-08-03',
        description: 'Pembayaran listrik',
        type: 'Keluar',
        amount: 'Rp 120.000',
    },
];
</script>

<template>
    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">Daftar transaksi terakhir.</p>
                <Link :href="route('transactions.create')" :class="buttonVariants({ variant: 'outline' })">
                    Tambah transaksi
                </Link>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Tanggal</TableHead>
                        <TableHead>Deskripsi</TableHead>
                        <TableHead>Jenis</TableHead>
                        <TableHead>Nominal</TableHead>
                        <TableHead class="w-[120px]">Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="transaction in transactions" :key="transaction.id">
                        <TableCell>{{ transaction.date }}</TableCell>
                        <TableCell>{{ transaction.description }}</TableCell>
                        <TableCell>{{ transaction.type }}</TableCell>
                        <TableCell>{{ transaction.amount }}</TableCell>
                        <TableCell>
                            <Button variant="secondary" size="sm">Detail</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
