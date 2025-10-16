<x-modal name="confirm-status-updation" focusable>
    <form id="status-update-form" method="post" action="#" class="p-6">
        @csrf
        @method('put')

        <div class="flex items-center gap-6">
            <div class="bg-yellow-100 shadow-sm sm:rounded-lg p-3">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Bạn có chắc chắn muốn cập nhật trạng thái mục này không?') }}
            </h2>
        </div>

        <div class="mt-6">
            <div class="relative">
                <div class="flex items-center justify-between relative z-10">
                    @php
                        $index = 0;
                    @endphp
                    @foreach ($statuses as $status => $label)
                        <div
                            class="flex flex-col
                                @if ($loop->first) items-start
                                @elseif ($loop->last)
                                    items-end
                                @else
                                    items-center @endif">
                            <div id="{{ $status }}"
                                class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold">
                                {{ $index + 1 }}
                            </div>
                            <span class="text-xs text-gray-600 mt-1">{{ $label }}</span>
                        </div>
                        @php
                            $index++;
                        @endphp
                    @endforeach
                </div>
                <div class="absolute top-4 left-[18px] right-[18px] bg-gray-300">
                    <div id="progress" class="h-1 bg-green-600"></div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Huỷ') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                {{ __('Cập nhật') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('status-update-form');
            const progress = document.getElementById('progress');
            const updateBtn = document.querySelector('#status-update-form button[type="submit"]');
            const statuses = Object.keys(@json($statuses));

            document.querySelectorAll('[data-status-update-url]').forEach(btn =>
                btn.addEventListener('click', () => {
                    form.action = btn.dataset.statusUpdateUrl;

                    const currentStatus = btn.dataset.statusValue;
                    const currentStatusIndex = statuses.indexOf(currentStatus);

                    let width = '0%';
                    if (currentStatusIndex !== -1) {
                        width = (currentStatusIndex * (100 / (statuses.length - 1))) + '%';

                        if (currentStatusIndex === statuses.length - 1) {
                            updateBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            updateBtn.disabled = true;
                        } else {
                            updateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            updateBtn.disabled = false;
                        }
                    } else {
                        updateBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        updateBtn.disabled = true;
                    }

                    progress.style.width = width;
                    renderStatuses(statuses, currentStatus);
                })
            );

            const renderStatuses = (statuses, currentStatus) => {
                statuses.forEach((status, index) => {
                    const element = document.getElementById(status);
                    element.classList.remove('bg-yellow-600', 'bg-green-600', 'bg-gray-300',
                        'text-white', 'text-gray-600');

                    if (status === currentStatus) {
                        element.classList.add('bg-yellow-600', 'text-white');
                    } else if (index < statuses.indexOf(currentStatus)) {
                        element.classList.add('bg-green-600', 'text-white');
                    } else {
                        element.classList.add('bg-gray-300', 'text-gray-600');
                    }
                });
            }
        });
    </script>
@endPushOnce
