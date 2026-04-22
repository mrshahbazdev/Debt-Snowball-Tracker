<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use App\Support\CurrentCompany;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Companies')]
class CompaniesManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public ?string $industry = null;
    public ?string $notes = null;

    public ?string $flash = null;

    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:120'],
            'industry' => ['nullable', 'string', 'max:120'],
            'notes'    => ['nullable', 'string', 'max:500'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $c = auth()->user()->companies()->findOrFail($id);
        $this->editingId = $c->id;
        $this->name = $c->name;
        $this->industry = $c->industry;
        $this->notes = $c->notes;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $user = auth()->user();

        if ($this->editingId) {
            $c = $user->companies()->findOrFail($this->editingId);
            $c->update($data);
            $this->flash = __('messages.companies.updated');
        } else {
            $c = $user->companies()->create($data);
            CurrentCompany::set($c);
            $this->flash = __('messages.companies.created');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $user = auth()->user();
        if ($user->companies()->count() <= 1) {
            $this->flash = __('messages.companies.need_one');
            return;
        }

        $c = $user->companies()->findOrFail($id);
        $wasCurrent = (session(CurrentCompany::SESSION_KEY) === $c->id);
        $c->delete();

        if ($wasCurrent) {
            CurrentCompany::set($user->companies()->first());
        }

        $this->flash = __('messages.companies.deleted');
    }

    public function switch(int $id): void
    {
        $c = auth()->user()->companies()->findOrFail($id);
        CurrentCompany::set($c);
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->industry = null;
        $this->notes = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $companies = auth()->user()->companies()->withCount(['debts', 'cashflows', 'payments'])->get();
        $currentId = session(CurrentCompany::SESSION_KEY);

        return view('livewire.companies.companies-manager', [
            'companies' => $companies,
            'currentId' => $currentId,
        ]);
    }
}
