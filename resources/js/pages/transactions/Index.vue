<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Transaksi',
        href: '/transactions',
    },
];

type Transaction = {
    id: number;
    date: string;
    category: string;
    type: 'income' | 'expense' | 'transfer';
    amount: number;
    walletId?: number | null;
    walletFromId?: number | null;
    walletToId?: number | null;
    walletName?: string | null;
    walletFromName?: string | null;
    walletToName?: string | null;
};

type WalletOption = {
    id: number;
    name: string;
};

type TransactionCategory = {
    id: number;
    name: string;
    type: 'income' | 'expense';
};

const props = defineProps<{
    transactions: Transaction[];
    wallets: WalletOption[];
    transactionCategories: TransactionCategory[];
}>();

const transactions = computed(() => props.transactions ?? []);
const walletOptions = computed(() => props.wallets ?? []);

const typeOptions = [
    { label: 'Pendapatan', value: 'income' },
    { label: 'Pengeluaran', value: 'expense' },
    { label: 'Transfer antar dompet', value: 'transfer' },
];

const today = new Date().toISOString().split('T')[0];
const initialWalletId = props.wallets?.[0]?.id ? String(props.wallets[0].id) : '';
const secondaryWalletId = props.wallets?.[1]?.id ? String(props.wallets[1].id) : initialWalletId;
const initialCategoryId = props.transactionCategories?.find((category) => category.type === 'income')?.id;

const form = useForm({
    date: today,
    type: 'income',
    wallet: initialWalletId,
    walletFrom: initialWalletId,
    walletTo: secondaryWalletId,
    categoryId: initialCategoryId ? String(initialCategoryId) : '',
    amount: '',
    notes: '',
});

const isTransfer = computed(() => form.type === 'transfer');
const categoryOptions = computed(() =>
    (props.transactionCategories ?? []).filter((category) => category.type === form.type),
);

watch(
    () => form.type,
    (value) => {
        if (value === 'transfer') {
            form.categoryId = '';
            return;
        }

        const matchedCategory = (props.transactionCategories ?? []).find((category) => category.type === value);
        form.categoryId = matchedCategory ? String(matchedCategory.id) : '';
    },
);

const filters = reactive({
    search: '',
    type: 'all',
    wallet: 'all',
});

const typeMeta = {
    income: { label: 'Pendapatan', class: 'bg-emerald-50 text-emerald-700' },
    expense: { label: 'Pengeluaran', class: 'bg-rose-50 text-rose-700' },
    transfer: { label: 'Transfer antar dompet', class: 'bg-amber-50 text-amber-700' },
};

const getWalletLabel = (transaction: Transaction) => {
    if (transaction.type === 'transfer') {
        return `${transaction.walletFromName ?? '-'} ke ${transaction.walletToName ?? '-'}`;
    }

    return transaction.walletName ?? '-';
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDate = (value: string) => {
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

const amountPrefix = (transaction: Transaction) => {
    if (transaction.type === 'income') {
        return '+';
    }

    if (transaction.type === 'expense') {
        return '-';
    }

    return '';
};

const amountTone = (transaction: Transaction) => {
    if (transaction.type === 'income') {
        return 'text-emerald-600';
    }

    if (transaction.type === 'expense') {
        return 'text-rose-600';
    }

    return 'text-amber-600';
};

const filteredTransactions = computed(() => {
    const search = filters.search.trim().toLowerCase();

    return transactions.value.filter((transaction) => {
        if (filters.type !== 'all' && transaction.type !== filters.type) {
            return false;
        }

        if (filters.wallet !== 'all') {
            const selectedWalletId = Number(filters.wallet);

            if (Number.isNaN(selectedWalletId)) {
                return false;
            }

            if (transaction.type === 'transfer') {
                if (transaction.walletFromId !== selectedWalletId && transaction.walletToId !== selectedWalletId) {
                    return false;
                }
            } else if (transaction.walletId !== selectedWalletId) {
                return false;
            }
        }

        if (search) {
            const haystack = `${transaction.category} ${getWalletLabel(transaction)}`.toLowerCase();
            if (!haystack.includes(search)) {
                return false;
            }
        }

        return true;
    });
});

const emptyTransactionsMessage = computed(() => {
    if (!transactions.value.length) {
        return 'Belum ada transaksi yang tercatat.';
    }

    return 'Tidak ada transaksi yang cocok dengan filter ini.';
});

const resetFilters = () => {
    filters.search = '';
    filters.type = 'all';
    filters.wallet = 'all';
};

const handleSubmit = () => {
    form.transform((data) => {
        const transfer = data.type === 'transfer';

        return {
            date: data.date,
            type: data.type,
            amount: data.amount,
            notes: data.notes,
            category_id: transfer ? null : data.categoryId,
            wallet_id: transfer ? null : data.wallet,
            wallet_from_id: transfer ? data.walletFrom : null,
            wallet_to_id: transfer ? data.walletTo : null,
        };
    }).post(route('transactions.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('amount', 'notes'),
    });
};
</script>

<template>
    <Head title="Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <section class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">Transaksi</h2>
                        <p class="text-sm text-muted-foreground">Kelola pendapatan, pengeluaran, dan transfer.</p>
                    </div>
                </div>
                <Card>
                    <CardHeader>
                        <CardTitle>Tambah transaksi</CardTitle>
                        <CardDescription>Masukkan transaksi baru langsung dari sini.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <form class="space-y-6" @submit.prevent="handleSubmit">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="grid w-full gap-2">
                                    <Label for="date">Tanggal</Label>
                                    <Input id="date" v-model="form.date" type="date" />
                                    <InputError :message="form.errors.date" />
                                </div>
                                <div class="grid w-full gap-2">
                                    <Label for="type">Jenis transaksi</Label>
                                    <Select v-model="form.type">
                                        <SelectTrigger id="type" class="w-full">
                                            <SelectValue placeholder="Pilih jenis" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="option in typeOptions" :key="option.value" :value="option.value">
                                                {{ option.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.type" />
                                </div>
                            </div>

                            <div v-if="isTransfer" class="grid gap-6 md:grid-cols-2">
                                <div class="grid w-full gap-2">
                                    <Label for="wallet-from">Dari dompet</Label>
                                    <Select v-model="form.walletFrom">
                                        <SelectTrigger id="wallet-from" class="w-full">
                                            <SelectValue placeholder="Pilih dompet asal" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="wallet in walletOptions" :key="wallet.id" :value="String(wallet.id)">
                                                {{ wallet.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p class="text-xs text-muted-foreground">Transfer hanya antar dompet.</p>
                                    <InputError :message="form.errors.wallet_from_id" />
                                </div>
                                <div class="grid w-full gap-2">
                                    <Label for="wallet-to">Ke dompet</Label>
                                    <Select v-model="form.walletTo">
                                        <SelectTrigger id="wallet-to" class="w-full">
                                            <SelectValue placeholder="Pilih dompet tujuan" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="wallet in walletOptions" :key="wallet.id" :value="String(wallet.id)">
                                                {{ wallet.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p class="text-xs text-muted-foreground">Pastikan dompet berbeda dari asal.</p>
                                    <InputError :message="form.errors.wallet_to_id" />
                                </div>
                            </div>
                            <div v-else class="grid gap-6 md:grid-cols-2">
                                <div class="grid w-full gap-2">
                                    <Label for="wallet">Dompet</Label>
                                    <Select v-model="form.wallet">
                                        <SelectTrigger id="wallet" class="w-full">
                                            <SelectValue placeholder="Pilih dompet" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="wallet in walletOptions" :key="wallet.id" :value="String(wallet.id)">
                                                {{ wallet.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.wallet_id" />
                                </div>
                                <div class="grid w-full gap-2">
                                    <Label for="category">Kategori</Label>
                                    <Select v-model="form.categoryId">
                                        <SelectTrigger id="category" class="w-full">
                                            <SelectValue placeholder="Pilih kategori" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="category in categoryOptions"
                                                :key="category.id"
                                                :value="String(category.id)"
                                            >
                                                {{ category.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="categoryOptions.length === 0" class="text-xs text-muted-foreground">
                                        Belum ada jenis transaksi untuk tipe ini.
                                    </p>
                                    <InputError :message="form.errors.category_id" />
                                </div>
                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="amount">Nominal</Label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">Rp</span>
                                    <Input
                                        id="amount"
                                        v-model="form.amount"
                                        class="pl-10"
                                        type="number"
                                        inputmode="numeric"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>
                                <p class="text-xs text-muted-foreground">Masukkan nominal tanpa tanda pemisah.</p>
                                <InputError :message="form.errors.amount" />
                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="notes">Catatan</Label>
                                <Textarea id="notes" v-model="form.notes" placeholder="Tambahkan catatan transaksi (opsional)." />
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing">Simpan transaksi</Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </section>

            <section class="space-y-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Filter transaksi</CardTitle>
                        <CardDescription>Temukan transaksi dengan cepat menggunakan filter berikut.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[1.6fr_repeat(2,1fr)_auto]">
                            <div class="grid gap-2">
                                <Label for="search">Cari</Label>
                                <Input
                                    id="search"
                                    v-model="filters.search"
                                    placeholder="Cari kategori atau dompet"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="type">Jenis</Label>
                                <Select v-model="filters.type">
                                    <SelectTrigger id="type" class="w-full">
                                        <SelectValue placeholder="Semua jenis" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Semua</SelectItem>
                                        <SelectItem value="income">Pendapatan</SelectItem>
                                        <SelectItem value="expense">Pengeluaran</SelectItem>
                                        <SelectItem value="transfer">Transfer antar dompet</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="grid gap-2">
                                <Label for="wallet">Dompet</Label>
                                <Select v-model="filters.wallet">
                                    <SelectTrigger id="wallet" class="w-full">
                                        <SelectValue placeholder="Semua dompet" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Semua</SelectItem>
                                        <SelectItem
                                            v-for="wallet in walletOptions"
                                            :key="wallet.id"
                                            :value="String(wallet.id)"
                                        >
                                            {{ wallet.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="flex items-end">
                                <Button variant="secondary" class="w-full" @click="resetFilters">Reset</Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <section class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-foreground">Daftar transaksi</h3>
                        <p class="text-sm text-muted-foreground">
                            Menampilkan {{ filteredTransactions.length }} dari {{ transactions.length }} transaksi.
                        </p>
                    </div>
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
                                    <TableHead class="w-[120px]">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="filteredTransactions.length">
                                    <TableRow v-for="transaction in filteredTransactions" :key="transaction.id">
                                        <TableCell class="whitespace-nowrap">{{ formatDate(transaction.date) }}</TableCell>
                                        <TableCell>
                                            <p class="font-medium text-foreground">{{ transaction.category }}</p>
                                        </TableCell>
                                        <TableCell>{{ getWalletLabel(transaction) }}</TableCell>
                                        <TableCell>
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                                :class="typeMeta[transaction.type].class"
                                            >
                                                {{ typeMeta[transaction.type].label }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="text-right font-semibold" :class="amountTone(transaction)">
                                            {{ amountPrefix(transaction) }}{{ formatCurrency(transaction.amount) }}
                                        </TableCell>
                                        <TableCell>
                                            <Button variant="secondary" size="sm">Detail</Button>
                                        </TableCell>
                                    </TableRow>
                                </template>
                                <TableEmpty v-else :colspan="6">
                                    {{ emptyTransactionsMessage }}
                                </TableEmpty>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
