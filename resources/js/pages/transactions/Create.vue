<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Transaksi',
        href: '/transactions',
    },
    {
        title: 'Tambah transaksi',
        href: '/transactions/create',
    },
];

type WalletOption = {
    id: number;
    name: string;
};

const props = defineProps<{
    wallets: WalletOption[];
}>();

const typeOptions = [
    { label: 'Pendapatan', value: 'income' },
    { label: 'Pengeluaran', value: 'expense' },
    { label: 'Transfer antar dompet', value: 'transfer' },
];

const walletOptions = computed(() => props.wallets ?? []);
const categoryOptions = ['Penjualan', 'Operasional', 'Marketing', 'Biaya Lainnya'];

const today = new Date().toISOString().split('T')[0];

const initialWalletId = props.wallets?.[0]?.id ? String(props.wallets[0].id) : '';
const secondaryWalletId = props.wallets?.[1]?.id ? String(props.wallets[1].id) : initialWalletId;

const form = useForm({
    date: today,
    type: 'income',
    wallet: initialWalletId,
    walletFrom: initialWalletId,
    walletTo: secondaryWalletId,
    category: 'Penjualan',
    amount: '',
    notes: '',
});

const isTransfer = computed(() => form.type === 'transfer');

const formatCurrency = (value: string | number) => {
    const rawValue = typeof value === 'number' ? value : Number.parseInt(value.replace(/[^\d]/g, ''), 10);
    const normalized = Number.isNaN(rawValue) ? 0 : rawValue;

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(normalized);
};

const previewType = computed(() => typeOptions.find((option) => option.value === form.type)?.label ?? 'Transaksi');

const previewWallet = computed(() => {
    const walletNameById = new Map(walletOptions.value.map((wallet) => [String(wallet.id), wallet.name]));

    const walletLabel = (id: string) => walletNameById.get(id) ?? '-';

    if (isTransfer.value) {
        return `${walletLabel(form.walletFrom)} ke ${walletLabel(form.walletTo)}`;
    }

    return walletLabel(form.wallet);
});

const previewCategory = computed(() => {
    if (isTransfer.value) {
        return 'Transfer antar dompet';
    }

    return form.category || '-';
});

const handleSubmit = () => {
    form.transform((data) => {
        const transfer = data.type === 'transfer';

        return {
            date: data.date,
            type: data.type,
            category: transfer ? 'Transfer antar dompet' : data.category,
            amount: data.amount,
            notes: data.notes,
            wallet_id: transfer ? null : data.wallet,
            wallet_from_id: transfer ? data.walletFrom : null,
            wallet_to_id: transfer ? data.walletTo : null,
        };
    }).post(route('transactions.store'));
};
</script>

<template>
    <Head title="Tambah transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
                <Card>
                    <CardHeader>
                        <CardTitle>Form transaksi</CardTitle>
                        <CardDescription>Lengkapi detail transaksi dengan jelas dan ringkas.</CardDescription>
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
                                    <Select v-model="form.category">
                                        <SelectTrigger id="category" class="w-full">
                                            <SelectValue placeholder="Pilih kategori" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="category in categoryOptions" :key="category" :value="category">
                                                {{ category }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.category" />
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
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    placeholder="Tambahkan catatan transaksi (opsional)."
                                />
                            </div>

                            <div class="flex items-center justify-between">
                                <Button type="submit">Simpan</Button>
                                <Link :class="buttonVariants({ variant: 'ghost' })" :href="route('transactions.index')">
                                    Batal
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Ringkasan transaksi</CardTitle>
                        <CardDescription>Pratinjau sebelum disimpan.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">Jenis</span>
                            <span class="font-medium text-foreground">{{ previewType }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">Dompet</span>
                            <span class="font-medium text-foreground">{{ previewWallet }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">Kategori</span>
                            <span class="font-medium text-foreground">{{ previewCategory }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">Nominal</span>
                            <span class="font-semibold text-foreground">{{ formatCurrency(form.amount) }}</span>
                        </div>
                        <div class="rounded-lg border border-border/60 bg-muted/30 p-3 text-xs text-muted-foreground">
                            Pastikan dompet dan nominal sudah sesuai sebelum disimpan.
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
