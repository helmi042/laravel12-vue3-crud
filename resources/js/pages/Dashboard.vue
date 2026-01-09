<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { buttonVariants } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type Summary = {
    income: number;
    expense: number;
    net: number;
};

type TransactionSummary = {
    income: { total: number; count: number };
    expense: { total: number; count: number };
    transfer: { total: number; count: number };
};

type WalletBalance = {
    id: number;
    name: string;
    type: string;
    balance: number;
    updatedAt: string | null;
};

type RecentTransaction = {
    id: number;
    date: string;
    type: 'income' | 'expense' | 'transfer';
    category: string;
    amount: number;
    walletName?: string | null;
    walletFromName?: string | null;
    walletToName?: string | null;
};

const props = defineProps<{
    summary: Summary;
    transactionSummary: TransactionSummary;
    wallets: WalletBalance[];
    recentTransactions: RecentTransaction[];
}>();

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDate = (value: string | null) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const summaryCards = computed(() => [
    {
        title: 'Pendapatan',
        amount: formatCurrency(props.summary?.income ?? 0),
        description: 'Total pemasukan bulan ini.',
        tone: 'text-emerald-600',
    },
    {
        title: 'Pengeluaran',
        amount: formatCurrency(props.summary?.expense ?? 0),
        description: 'Total pengeluaran bulan ini.',
        tone: 'text-rose-600',
    },
    {
        title: 'Saldo Bersih',
        amount: formatCurrency(props.summary?.net ?? 0),
        description: 'Sisa saldo bulan ini.',
        tone: 'text-sky-600',
    },
]);

const transactionTypes = computed(() => [
    {
        title: 'Pendapatan',
        subtitle: 'Penjualan, jasa, dan lainnya',
        total: formatCurrency(props.transactionSummary?.income?.total ?? 0),
        count: `${props.transactionSummary?.income?.count ?? 0} transaksi`,
        badge: 'bg-emerald-50 text-emerald-700',
    },
    {
        title: 'Pengeluaran',
        subtitle: 'Operasional, tagihan, kebutuhan',
        total: formatCurrency(props.transactionSummary?.expense?.total ?? 0),
        count: `${props.transactionSummary?.expense?.count ?? 0} transaksi`,
        badge: 'bg-rose-50 text-rose-700',
    },
    {
        title: 'Transfer antar dompet',
        subtitle: 'Perpindahan antar dompet',
        total: formatCurrency(props.transactionSummary?.transfer?.total ?? 0),
        count: `${props.transactionSummary?.transfer?.count ?? 0} transaksi`,
        badge: 'bg-amber-50 text-amber-700',
    },
]);

const walletTypeLabel = {
    cash: 'Tunai',
    bank: 'Bank',
    ewallet: 'E-Wallet',
};

const walletBalances = computed(() =>
    (props.wallets ?? []).map((wallet) => ({
        name: wallet.name,
        account: walletTypeLabel[wallet.type as keyof typeof walletTypeLabel] ?? wallet.type,
        balance: formatCurrency(wallet.balance ?? 0),
        activity: wallet.updatedAt ? `Update ${formatDate(wallet.updatedAt)}` : 'Belum ada update',
    })),
);

const recentTransactions = computed(() => props.recentTransactions ?? []);

const getTransactionWallet = (transaction: RecentTransaction) => {
    if (transaction.type === 'transfer') {
        return `${transaction.walletFromName ?? '-'} ke ${transaction.walletToName ?? '-'}`;
    }

    return transaction.walletName ?? '-';
};

const typeLabel = {
    income: 'Pendapatan',
    expense: 'Pengeluaran',
    transfer: 'Transfer antar dompet',
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <section class="space-y-3">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Ringkasan</h2>
                    <p class="text-sm text-muted-foreground">Ikhtisar kondisi keuangan bulan ini.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <Card v-for="card in summaryCards" :key="card.title">
                        <CardHeader class="pb-2">
                            <CardTitle class="text-sm font-medium text-muted-foreground">
                                {{ card.title }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-1">
                            <div class="text-2xl font-semibold" :class="card.tone">{{ card.amount }}</div>
                            <p class="text-xs text-muted-foreground">{{ card.description }}</p>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <section class="grid gap-4 lg:grid-cols-3">
                <Card class="lg:col-span-2">
                    <CardHeader class="space-y-1">
                        <CardTitle>Transaksi</CardTitle>
                        <CardDescription>Ringkasan berdasarkan jenis transaksi.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-3">
                        <div
                            v-for="type in transactionTypes"
                            :key="type.title"
                            class="flex h-full flex-col justify-between gap-4 rounded-lg border border-border/60 p-4"
                        >
                            <div class="space-y-2">
                                <span class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-medium" :class="type.badge">
                                    {{ type.title }}
                                </span>
                                <p class="text-sm font-medium text-foreground">{{ type.subtitle }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-lg font-semibold text-foreground">{{ type.total }}</p>
                                <p class="text-xs text-muted-foreground">{{ type.count }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="space-y-1">
                        <CardTitle>Saldo Dompet</CardTitle>
                        <CardDescription>Saldo di setiap dompet keuangan.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-for="wallet in walletBalances" :key="wallet.name" class="space-y-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-foreground">{{ wallet.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ wallet.account }}</p>
                                </div>
                                <p class="text-sm font-semibold text-foreground">{{ wallet.balance }}</p>
                            </div>
                            <p class="text-xs text-muted-foreground">{{ wallet.activity }}</p>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <section class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h3 class="text-lg font-semibold text-foreground">Riwayat Transaksi</h3>
                        <p class="text-sm text-muted-foreground">Daftar transaksi terbaru yang tercatat.</p>
                    </div>
                    <Link :href="route('transactions.index')" :class="buttonVariants({ variant: 'outline' })">
                        Lihat semua
                    </Link>
                </div>
                <Card>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Tanggal</TableHead>
                                    <TableHead>Kategori</TableHead>
                                    <TableHead>Dompet</TableHead>
                                    <TableHead>Jenis</TableHead>
                                    <TableHead class="text-right">Nominal</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in recentTransactions" :key="item.id">
                                    <TableCell class="whitespace-nowrap">{{ formatDate(item.date) }}</TableCell>
                                    <TableCell>{{ item.category }}</TableCell>
                                    <TableCell>{{ getTransactionWallet(item) }}</TableCell>
                                    <TableCell>{{ typeLabel[item.type] ?? item.type }}</TableCell>
                                    <TableCell class="text-right font-medium">{{ formatCurrency(item.amount) }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
