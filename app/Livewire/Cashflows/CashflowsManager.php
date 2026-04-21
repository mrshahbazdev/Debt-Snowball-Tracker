<?php

namespace App\Livewire\Cashflows;

use App\Models\Cashflow;
use App\Services\SnowballService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Cashflow')]
class CashflowsManager extends Component
{
    use WithPagination;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $period = '';
    public float $revenue = 0;
    public ?string $notes = null;

    public ?string $flash = null;

    public function mount(): void
    {
        $this->period = Carbon::now()->startOfMonth()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'period' => ['required', 'date'],
            'revenue' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->period = Carbon::now()->startOfMonth()->toDateString();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $cf = auth()->user()->cashflows()->findOrFail($id);
        $this->editingId = $cf->id;
        $this->period = $cf->period->toDateString();
        $this->revenue = (float) $cf->revenue;
        $this->notes = $cf->notes;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $user = auth()->user();
        $setting = $user->getOrCreateSetting();

        $period = Carbon::parse($data['period'])->startOfMonth();
        $revenue = (float) $data['revenue'];
        $alloc = round($revenue * $setting->allocationFraction(), 2);
        $available = round($revenue - $alloc, 2);

        if ($this->editingId) {
            $cf = $user->cashflows()->findOrFail($this->editingId);
            $cf->update([
                'period' => $period,
                'revenue' => $revenue,
                'debt_allocation' => $alloc,
                'available_cash' => $available,
                'notes' => $data['notes'] ?? null,
            ]);
        } else {
            $user->cashflows()->updateOrCreate(
                ['period' => $period],
                [
                    'revenue' => $revenue,
                    'debt_allocation' => $alloc,
                    'available_cash' => $available,
                    'notes' => $data['notes'] ?? null,
                ]
            );
        }

        $this->showForm = false;
        $this->resetForm();
        $this->flash = 'Cashflow saved.';
    }

    public function delete(int $id): void
    {
        auth()->user()->cashflows()->findOrFail($id)->delete();
    }

    public function applySnowball(int $id, SnowballService $snowball): void
    {
        $cf = auth()->user()->cashflows()->findOrFail($id);
        $payments = $snowball->distributeCashflow($cf);
        $this->flash = count($payments) . ' payment(s) applied.';
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->revenue = 0;
        $this->notes = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $cashflows = auth()->user()->cashflows()
            ->withCount('payments')
            ->orderByDesc('period')
            ->paginate(12);

        return view('livewire.cashflows.cashflows-manager', [
            'cashflows' => $cashflows,
        ]);
    }
}
