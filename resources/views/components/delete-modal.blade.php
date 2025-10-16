<x-modal name="confirm-deletion" focusable>
    <form id="delete-form" method="post" action="#" class="p-6">
        @csrf
        @method('delete')

        <div class="flex items-center gap-6">
            <div class="bg-red-100 shadow-sm sm:rounded-lg p-3">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Bạn có chắc chắn muốn xóa mục này không?') }}
            </h2>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Huỷ') }}
            </x-secondary-button>

            <x-danger-button class="ms-3">
                {{ __('Xoá') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('delete-form');
            document.querySelectorAll('[data-delete-url]').forEach(btn =>
                btn.addEventListener('click', () => form.action = btn.dataset.deleteUrl)
            );
        });
    </script>
@endPushOnce
