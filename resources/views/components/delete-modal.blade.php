<x-modal name="confirm-deletion" focusable>
    <form id="delete-form" method="post" action="#" class="p-6">
        @csrf
        @method('delete')

        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-triangle font-semibold text-xl text-red-500"></i>
            <h2 class="text-lg font-medium text-gray-900">
                Bạn có chắc chắn muốn xóa
                <span class="font-semibold text-red-500">mục</span>
                này không?
            </h2>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                Huỷ
            </x-secondary-button>

            <x-danger-button class="ms-3">
                Xoá
            </x-danger-button>
        </div>
    </form>
</x-modal>

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('delete-form');

            document.querySelectorAll('[data-delete-url]').forEach(button => {
                button.addEventListener('click', () => {
                    const deleteUrl = button.getAttribute('data-delete-url');
                    form.action = deleteUrl;
                });
            });
        });
    </script>
@endPushOnce
