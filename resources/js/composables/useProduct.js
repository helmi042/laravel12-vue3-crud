import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner'

export function deleteProduct(id) {
    if (confirm("Are you sure?")) {
        router.delete(route('products.destroy', id), {
            preserveScroll: true,
            onSuccess: () => toast.success('Product deleted successfully.')
        })
    }
}