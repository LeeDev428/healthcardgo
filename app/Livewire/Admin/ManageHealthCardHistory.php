<?php

namespace App\Livewire\Admin;

use App\Models\HistoricalHealthCardData;
use App\Services\HealthCardPredictionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Health Card Historical Data')]
class ManageHealthCardHistory extends Component
{
    use WithPagination;

    public string $recordDate = '';

    public int $issuedCount = 0;

    public string $notes = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $filterStartDate = '';

    public string $filterEndDate = '';

    protected $rules = [
        'recordDate' => 'required|date',
        'issuedCount' => 'required|integer|min:0',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount(): void
    {
        $this->recordDate = now()->startOfMonth()->format('Y-m-d');
        $this->filterStartDate = now()->subYear()->format('Y-m-d');
        $this->filterEndDate = now()->format('Y-m-d');
    }

    #[Computed]
    public function historicalData()
    {
        return HistoricalHealthCardData::query()
            ->when($this->filterStartDate && $this->filterEndDate, function ($q) {
                $q->byDateRange($this->filterStartDate, $this->filterEndDate);
            })
            ->with('creator:id,name')
            ->orderByDesc('record_date')
            ->paginate(15);
    }

    #[Computed]
    public function dataStatus()
    {
        $service = app(HealthCardPredictionService::class);

        return $service->hasSufficientData();
    }

    public function openForm(): void
    {
        $this->showForm = true;
        $this->editingId = null;
        $this->resetForm();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->recordDate = now()->startOfMonth()->format('Y-m-d');
        $this->issuedCount = 0;
        $this->notes = '';
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'record_date' => $this->recordDate,
            'issued_count' => $this->issuedCount,
            'notes' => $this->notes,
            'data_source' => 'manual',
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            $record = HistoricalHealthCardData::findOrFail($this->editingId);
            $record->update($data);
            session()->flash('message', 'Historical data updated successfully.');
        } else {
            HistoricalHealthCardData::create($data);
            session()->flash('message', 'Historical data added successfully.');
        }

        $this->closeForm();
        unset($this->historicalData);
        unset($this->dataStatus);
    }

    public function edit(int $id): void
    {
        $record = HistoricalHealthCardData::findOrFail($id);

        $this->editingId = $id;
        $this->recordDate = \Carbon\Carbon::parse($record->record_date)->format('Y-m-d');
        $this->issuedCount = $record->issued_count;
        $this->notes = $record->notes ?? '';
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        HistoricalHealthCardData::findOrFail($id)->delete();
        session()->flash('message', 'Historical data deleted successfully.');
        unset($this->historicalData);
        unset($this->dataStatus);
    }

    public function render()
    {
        return view('livewire.admin.manage-health-card-history');
    }
}
