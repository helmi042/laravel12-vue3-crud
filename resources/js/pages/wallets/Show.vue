<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/vue3';
import { Button, buttonVariants } from '@/components/ui/button';
import { deleteWallet } from '@/composables/useWallet';

const props = defineProps({
    wallet: {
        type: Object,
        required: true,
    },
});

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

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Dompet',
        href: '/wallets',
    },
    {
        title: 'Detail dompet',
        href: '/wallets',
    },
];
</script>

<template>
    <Head title="Detail dompet" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Detail dompet</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Nama</div>
                            <div>{{ props.wallet.name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Logo</div>
                            <div>
                                <div
                                    v-if="props.wallet.logo_url"
                                    class="h-20 w-20 overflow-hidden rounded-lg border border-border bg-muted/40"
                                >
                                    <img :src="props.wallet.logo_url" alt="Logo dompet" class="h-full w-full object-cover" />
                                </div>
                                <div v-else class="text-sm text-muted-foreground">Belum ada logo.</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Jenis</div>
                            <div>{{ typeLabels[props.wallet.type] ?? props.wallet.type }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Saldo saat ini</div>
                            <div>{{ formatCurrency(props.wallet.current_balance) }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Saldo awal</div>
                            <div>{{ formatCurrency(props.wallet.balance) }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Aktivitas terakhir</div>
                            <div>{{ formatDate(props.wallet.last_activity) }}</div>
                        </div>
                        <div class="flex space-x-4">
                            <div class="w-32 font-semibold">Catatan</div>
                            <div class="flex-1">{{ props.wallet.description ?? '-' }}</div>
                        </div>
                        <div class="flex justify-between items-center space-x-4 mt-6">
                            <Link :href="route('wallets.index')" :class="buttonVariants({ variant: 'outline' })">Kembali</Link>
                            <div>
                                <Link :href="route('wallets.edit', props.wallet)" :class="buttonVariants({ variant: 'default' })">
                                    Edit
                                </Link>
                                <Button @click="deleteWallet(props.wallet.id)" class="ml-2" variant="destructive">
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
