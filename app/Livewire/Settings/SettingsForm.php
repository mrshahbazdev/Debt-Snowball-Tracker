<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Settings')]
class SettingsForm extends Component
{
    public float $monthly_revenue = 0;
    public float $debt_allocation_percent = 1;
    public float $minimum_cash_buffer = 0;
    public bool $new_debt_allowed = false;
    public string $currency = 'PKR';

    public ?string $savedMessage = null;

    public function mount(): void
    {
        $setting = auth()->user()->getOrCreateSetting();
        $this->monthly_revenue = (float) $setting->monthly_revenue;
        $this->debt_allocation_percent = (float) $setting->debt_allocation_percent;
        $this->minimum_cash_buffer = (float) $setting->minimum_cash_buffer;
        $this->new_debt_allowed = (bool) $setting->new_debt_allowed;
        $this->currency = (string) $setting->currency;
    }

    protected function rules(): array
    {
        return [
            'monthly_revenue' => ['required', 'numeric', 'min:0'],
            'debt_allocation_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'minimum_cash_buffer' => ['required', 'numeric', 'min:0'],
            'new_debt_allowed' => ['boolean'],
            'currency' => ['required', 'string', 'max:8'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $setting = auth()->user()->getOrCreateSetting();
        $setting->fill($data)->save();
        $this->savedMessage = 'Settings saved.';
    }

    public function render(): View
    {
        return view('livewire.settings.settings-form');
    }
}
