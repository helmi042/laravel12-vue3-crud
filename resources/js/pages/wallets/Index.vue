<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <section class="space-y-3">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Dompet</h2>
                    <p class="text-sm text-muted-foreground">Kelola saldo dompet keuangan.</p>
                </div>
                <Card>
                    <CardHeader class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <CardTitle>Daftar dompet</CardTitle>
                            <CardDescription>Saldo otomatis tersinkronisasi dari transaksi.</CardDescription>
                        </div>
                        <Link :href="route('wallets.create')" :class="buttonVariants({ variant: 'outline' })">
                            Tambah dompet
                        </Link>
                    </CardHeader>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Jenis</TableHead>
                                    <TableHead>Saldo</TableHead>
                                    <TableHead>Aktivitas</TableHead>
                                    <TableHead class="w-[140px]">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="wallets.length">
                                    <TableRow v-for="wallet in wallets" :key="wallet.id">
                                        <TableCell>
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 overflow-hidden rounded-full border border-border bg-muted/40">
                                                    <img
                                                        v-if="wallet.logo_url"
                                                        :src="wallet.logo_url"
                                                        :alt="`Logo ${wallet.name}`"
                                                        class="h-full w-full object-cover"
                                                    />
                                                    <div
                                                        v-else
                                                        class="flex h-full w-full items-center justify-center text-xs font-semibold text-muted-foreground"
                                                    >
                                                        {{ wallet.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                                    </div>
                                                </div>
                                                <span class="font-medium">{{ wallet.name }}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell>{{ typeLabels[wallet.type] ?? wallet.type }}</TableCell>
                                        <TableCell>
                                            <div class="space-y-1">
                                                <p class="font-semibold text-foreground">
                                                    {{ formatCurrency(wallet.current_balance) }}
                                                </p>
                                                <p class="text-xs text-muted-foreground">
                                                    Saldo awal {{ formatCurrency(wallet.balance) }}
                                                </p>
                                            </div>
                                        </TableCell>
                                        <TableCell>{{ formatDate(wallet.last_activity) }}</TableCell>
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
                                </template>
                                <TableEmpty v-else :colspan="5">
                                    Belum ada dompet. Tambahkan dompet pertama untuk mulai mencatat transaksi.
                                </TableEmpty>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
