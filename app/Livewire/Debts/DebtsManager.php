<?php

namespace App\Livewire\Debts;

use App\Models\Debt;
use App\Services\SnowballService;
use App\Support\CurrentCompany;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Debts')]
class DebtsManager extends Component
{
    use WithPagination;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $creditor = '';
    public float $original_balance = 0;
    public float $current_balance = 0;
    public float $minimum_payment = 0;
    public string $status = Debt::STATUS_ACTIVE;
    public ?string $notes = null;

    public string $filter = 'all'; // all | active | paid

    protected function company()
    {
        return CurrentCompany::resolve(auth()->user());
    }

    protected function rules(): array
    {
        return [
            'creditor' => ['required', 'string', 'max:255'],
            'original_balance' => ['required', 'numeric', 'min:0'],
            'current_balance' => ['required', 'numeric', 'min:0'],
            'minimum_payment' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:ACTIVE,PAID'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $debt = $this->company()->debts()->findOrFail($id);
        $this->editingId = $debt->id;
        $this->creditor = $debt->creditor;
        $this->original_balance = (float) $debt->original_balance;
        $this->current_balance = (float) $debt->current_balance;
        $this->minimum_payment = (float) $debt->minimum_payment;
        $this->status = $debt->status;
        $this->notes = $debt->notes;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $company = $this->company();
        if ($this->editingId) {
            $debt = $company->debts()->findOrFail($this->editingId);
            $debt->fill($data);
            if ($debt->status === Debt::STATUS_PAID && !$debt->paid_at) {
                $debt->paid_at = now();
            }
            if ($debt->status === Debt::STATUS_ACTIVE) {
                $debt->paid_at = null;
            }
            $debt->save();
        } else {
            $debt = $company->debts()->create(array_merge($data, ['user_id' => $company->user_id]));
            if ($debt->status === Debt::STATUS_PAID) {
                $debt->update(['paid_at' => now()]);
            }
        }
        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $this->company()->debts()->findOrFail($id)->delete();
    }

    public function togglePaid(int $id): void
    {
        $debt = $this->company()->debts()->findOrFail($id);
        if ($debt->status === Debt::STATUS_ACTIVE) {
            $debt->update(['status' => Debt::STATUS_PAID, 'paid_at' => now(), 'current_balance' => 0]);
        } else {
            $debt->update(['status' => Debt::STATUS_ACTIVE, 'paid_at' => null]);
        }
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->creditor = '';
        $this->original_balance = 0;
        $this->current_balance = 0;
        $this->minimum_payment = 0;
        $this->status = Debt::STATUS_ACTIVE;
        $this->notes = null;
        $this->resetValidation();
    }

    public function render(SnowballService $snowball): View
    {
        $company = $this->company();

        $query = $company->debts();
        if ($this->filter === 'active') {
            $query->where('status', Debt::STATUS_ACTIVE);
        } elseif ($this->filter === 'paid') {
            $query->where('status', Debt::STATUS_PAID);
        }

        $debts = $query->orderBy('status')->orderBy('current_balance')->paginate(15);
        $target = $snowball->currentTarget($company);

        return view('livewire.debts.debts-manager', [
            'debts' => $debts,
            'target' => $target,
        ]);
    }
}
