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
                            <div class="w-32 font-semibold">Jenis</div>
                            <div>{{ typeLabels[props.wallet.type] ?? props.wallet.type }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Saldo</div>
                            <div>{{ formatCurrency(props.wallet.balance) }}</div>
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
