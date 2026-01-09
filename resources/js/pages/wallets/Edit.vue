<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    wallet: {
        type: Object,
        required: true,
    },
});

const walletTypes = [
    { label: 'Tunai', value: 'cash' },
    { label: 'Bank', value: 'bank' },
    { label: 'E-Wallet', value: 'ewallet' },
];

const form = useForm({
    name: props.wallet.name,
    type: props.wallet.type,
    balance: props.wallet.balance,
    description: props.wallet.description ?? '',
});

const handleSubmit = () => {
    form.put(route('wallets.update', props.wallet), {
        preserveScroll: true,
        onSuccess: () => toast.success('Dompet berhasil diperbarui.'),
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
        title: 'Edit dompet',
        href: '/wallets',
    },
];
</script>

<template>
    <Head title="Edit dompet" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Edit dompet</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <form class="space-y-6" @submit.prevent="handleSubmit">
                            <div class="grid w-full gap-2">
                                <Label for="name">Nama dompet</Label>
                                <Input id="name" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="grid w-full gap-2">
                                    <Label for="type">Jenis dompet</Label>
                                    <Select id="type" v-model="form.type">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih jenis dompet" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="type in walletTypes" :key="type.value" :value="type.value">
                                                {{ type.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.type" />
                                </div>
                                <div class="grid w-full gap-2">
                                    <Label for="balance">Saldo awal</Label>
                                <Input id="balance" v-model="form.balance" type="number" inputmode="numeric" min="0" />
                                    <InputError :message="form.errors.balance" />
                                </div>
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="description">Catatan</Label>
                                <Textarea id="description" v-model="form.description" />
                                <InputError :message="form.errors.description" />
                            </div>
                            <div class="flex justify-between items-center">
                                <Button variant="default" :disabled="form.processing">Simpan perubahan</Button>
                                <Link :class="buttonVariants({ variant: 'ghost' })" :href="route('wallets.index')">
                                    Batal
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
