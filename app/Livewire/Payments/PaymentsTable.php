<?php

namespace App\Livewire\Payments;

use App\Support\CurrentCompany;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Payments')]
class PaymentsTable extends Component
{
    use WithPagination;

    public string $search = '';

    protected function company()
    {
        return CurrentCompany::resolve(auth()->user());
    }

    public function delete(int $id): void
    {
        $payment = $this->company()->payments()->with('debt')->findOrFail($id);
        if ($payment->debt) {
            $payment->debt->current_balance = (float) $payment->debt->current_balance + (float) $payment->amount;
            if ($payment->debt->status === 'PAID') {
                $payment->debt->status = 'ACTIVE';
                $payment->debt->paid_at = null;
            }
            $payment->debt->save();
        }
        $payment->delete();
    }

    public function render(): View
    {
        $payments = $this->company()->payments()
            ->with(['debt', 'cashflow'])
            ->when($this->search !== '', function ($q) {
                $q->whereHas('debt', fn ($qq) => $qq->where('creditor', 'like', '%' . $this->search . '%'));
            })
            ->orderByDesc('paid_on')
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.payments.payments-table', [
            'payments' => $payments,
        ]);
    }
}
