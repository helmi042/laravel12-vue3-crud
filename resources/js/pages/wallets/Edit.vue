<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
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
    logo: null,
    _method: 'put',
});

const fileInputClass =
    'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive';

const logoPreview = ref(null);

const handleLogoChange = (event) => {
    const [file] = event.target.files ?? [];

    form.logo = file ?? null;

    if (logoPreview.value) {
        URL.revokeObjectURL(logoPreview.value);
    }

    logoPreview.value = file ? URL.createObjectURL(file) : null;
};

onBeforeUnmount(() => {
    if (logoPreview.value) {
        URL.revokeObjectURL(logoPreview.value);
    }
});

const handleSubmit = () => {
    form.post(route('wallets.update', props.wallet), {
        preserveScroll: true,
        forceFormData: true,
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
                            <div class="grid w-full gap-2">
                                <Label for="logo">Logo dompet</Label>
                                <input
                                    id="logo"
                                    type="file"
                                    accept="image/*"
                                    :class="fileInputClass"
                                    @change="handleLogoChange"
                                />
                                <InputError :message="form.errors.logo" />
                                <div v-if="logoPreview || props.wallet.logo_url" class="flex items-center gap-3">
                                    <div class="h-16 w-16 overflow-hidden rounded-lg border border-border">
                                        <img
                                            :src="logoPreview ?? props.wallet.logo_url"
                                            alt="Logo dompet"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>
                                    <span class="text-xs text-muted-foreground">
                                        {{ logoPreview ? 'Pratinjau logo baru' : 'Logo saat ini' }}
                                    </span>
                                </div>
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
