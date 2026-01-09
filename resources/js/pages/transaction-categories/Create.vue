<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const typeOptions = [
    { label: 'Pendapatan', value: 'income' },
    { label: 'Pengeluaran', value: 'expense' },
];

const form = useForm({
    name: '',
    type: '',
    description: '',
});

const handleSubmit = () => {
    form.post(route('transaction-categories.store'), {
        preserveScroll: true,
        onSuccess: () => toast.success('Jenis transaksi berhasil ditambahkan.'),
    });
};

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Jenis Transaksi',
        href: '/transaction-categories',
    },
    {
        title: 'Tambah jenis',
        href: '/transaction-categories/create',
    },
];
</script>

<template>
    <Head title="Tambah jenis transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Tambah jenis transaksi</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <form class="space-y-6" @submit.prevent="handleSubmit">
                            <div class="grid w-full gap-2">
                                <Label for="name">Nama jenis</Label>
                                <Input id="name" v-model="form.name" placeholder="Contoh: Gaji" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="type">Tipe</Label>
                                <Select v-model="form.type">
                                    <SelectTrigger id="type" class="w-full">
                                        <SelectValue placeholder="Pilih tipe" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="option in typeOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.type" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="description">Catatan</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    placeholder="Tambahkan catatan jika diperlukan"
                                />
                                <InputError :message="form.errors.description" />
                            </div>
                            <div class="flex justify-between items-center">
                                <Button variant="default" :disabled="form.processing">Simpan</Button>
                                <Link :class="buttonVariants({ variant: 'ghost' })" :href="route('transaction-categories.index')">
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
