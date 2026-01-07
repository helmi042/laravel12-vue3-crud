<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
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
    {
        title: 'Tambah transaksi',
        href: '/transactions/create',
    },
];
</script>

<template>
    <Head title="Tambah transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Form transaksi</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <form class="space-y-6" @submit.prevent>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="grid w-full gap-2">
                                    <Label for="date">Tanggal</Label>
                                    <Input id="date" type="date" />
                                </div>
                                <div class="grid w-full gap-2">
                                    <Label for="type">Jenis transaksi</Label>
                                    <Select>
                                        <SelectTrigger id="type" class="w-full">
                                            <SelectValue placeholder="Pilih jenis" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="masuk">Kas masuk</SelectItem>
                                            <SelectItem value="keluar">Kas keluar</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="description">Deskripsi</Label>
                                <Input id="description" placeholder="Contoh: Penjualan tunai" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="amount">Nominal</Label>
                                <Input id="amount" placeholder="Rp 0" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="notes">Catatan</Label>
                                <Textarea id="notes" placeholder="Tambahkan catatan transaksi (opsional)." />
                            </div>
                            <div class="flex items-center justify-between">
                                <Button type="submit">Simpan</Button>
                                <Link :class="buttonVariants({ variant: 'ghost' })" :href="route('transactions.index')">Batal</Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
