import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export function deleteWallet(id) {
    if (confirm('Yakin ingin menghapus dompet ini?')) {
        router.delete(route('wallets.destroy', id), {
            preserveScroll: true,
            onSuccess: () => toast.success('Dompet berhasil dihapus.'),
        });
    }
}
