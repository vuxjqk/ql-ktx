<div class="px-6 py-3">
    <h3 class="font-semibold text-xl text-gray-800 leading-tight">
        <i class="fas fa-table text-green-600 me-1"></i>
        {{ __($title ?? 'Danh sách') }}
    </h3>
</div>
<div class="overflow-x-auto">
    <table class="w-full table-auto">
        {{ $slot }}
    </table>
</div>
