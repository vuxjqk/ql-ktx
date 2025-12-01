<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function show(Contract $contract)
    {
        if (Auth::id() !== $contract->booking->user_id) {
            abort(403, __('Unauthorized'));
        }

        $contract->load('booking.room.floor.branch');
        return view('student.contracts.show', compact('contract'));
    }

    public function agree(Contract $contract)
    {
        if (Auth::id() !== $contract->booking->user_id) {
            abort(403, __('Unauthorized'));
        }

        if ($contract->contract_file) {
            return redirect()->route('student.bookings.index')
                ->with('error', __('Hợp đồng đã được ký, không thể ký lại.'));
        }

        $pdf = Pdf::loadView('student.contracts.export', compact('contract'));
        $filename = "contracts/{$contract->contract_code}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        $contract->update([
            'contract_file' => $filename,
        ]);

        return redirect()->route('student.bookings.index')
            ->with('success', __('Ký hợp đồng thành công!'));
    }

    public function export(Contract $contract)
    {
        if (Auth::id() !== $contract->booking->user_id) {
            abort(403, __('Unauthorized'));
        }

        $contract->load([
            'booking.user.student',
            'booking.room.floor.branch'
        ]);

        $pdf = Pdf::loadView('student.contracts.export', compact('contract'));
        return $pdf->stream("contracts-{$contract->contract_code}.pdf");
    }
}
