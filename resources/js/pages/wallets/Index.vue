<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button, buttonVariants } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { deleteWallet } from '@/composables/useWallet';

defineProps({
    wallets: {
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
        title: 'Dompet',
        href: '/wallets',
    },
];

const typeLabels = {
    cash: 'Tunai',
    bank: 'Bank',
    ewallet: 'E-Wallet',
};

const formatCurrency = (value) => {
    const amount = Number(value ?? 0);

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number.isNaN(amount) ? 0 : amount);
};

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <Head title="Dompet" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mt-3 flex items-center justify-between gap-3">
                <p class="text-sm text-muted-foreground">Kelola saldo dompet keuangan.</p>
                <Link :href="route('wallets.create')" :class="buttonVariants({ variant: 'outline' })">
                    Tambah dompet
                </Link>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nama</TableHead>
                        <TableHead>Jenis</TableHead>
                        <TableHead>Saldo</TableHead>
                        <TableHead>Update</TableHead>
                        <TableHead class="w-[140px]">Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="wallet in wallets" :key="wallet.id">
                        <TableCell class="font-medium">{{ wallet.name }}</TableCell>
                        <TableCell>{{ typeLabels[wallet.type] ?? wallet.type }}</TableCell>
                        <TableCell>{{ formatCurrency(wallet.balance) }}</TableCell>
                        <TableCell>{{ formatDate(wallet.updated_at) }}</TableCell>
                        <TableCell class="space-x-2">
                            <Link :href="route('wallets.show', wallet)" :class="buttonVariants({ variant: 'secondary' })">
                                Detail
                            </Link>
                            <Link :href="route('wallets.edit', wallet)" :class="buttonVariants({ variant: 'default' })">
                                Edit
                            </Link>
                            <Button variant="destructive" @click="deleteWallet(wallet.id)">Hapus</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
