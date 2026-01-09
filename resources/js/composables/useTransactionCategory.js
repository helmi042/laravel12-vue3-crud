import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export function deleteTransactionCategory(id) {
    if (confirm('Yakin ingin menghapus jenis transaksi ini?')) {
        router.delete(route('transaction-categories.destroy', id), {
            preserveScroll: true,
            onSuccess: () => toast.success('Jenis transaksi berhasil dihapus.'),
        });
    }
}
