<?php

namespace App\Livewire\Admin;

use App\Models\Barangay;
use App\Models\HistoricalDiseaseData;
use App\Services\SarimaPredictionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ManageHistoricalData extends Component
{
    use WithPagination;

    public string $diseaseType = 'dengue';

    public ?int $barangayId = null;

    public string $recordDate = '';

    public int $caseCount = 0;

    public string $notes = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $filterDiseaseType = '';

    public ?int $filterBarangayId = null;

    protected $rules = [
        'diseaseType' => 'required|in:hiv,dengue,malaria,measles,rabies,pregnancy_complications',
        'barangayId' => 'nullable|exists:barangays,id',
        'recordDate' => 'required|date',
        'caseCount' => 'required|integer|min:0',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount(): void
    {
        $this->recordDate = now()->startOfMonth()->format('Y-m-d');
    }

    #[Computed]
    public function historicalData()
    {
        return HistoricalDiseaseData::query()
            ->when($this->filterDiseaseType, fn ($q) => $q->byDiseaseType($this->filterDiseaseType))
            ->when($this->filterBarangayId, fn ($q) => $q->byBarangay($this->filterBarangayId))
            ->with(['barangay:id,name', 'creator:id,name'])
            ->orderByDesc('record_date')
            ->paginate(15);
    }

    #[Computed]
    public function barangays()
    {
        return Barangay::orderBy('name')->get();
    }

    #[Computed]
    public function diseaseTypes(): array
    {
        return [
            'hiv' => 'HIV/AIDS',
            'dengue' => 'Dengue',
            'malaria' => 'Malaria',
            'measles' => 'Measles',
            'rabies' => 'Rabies',
            'pregnancy_complications' => 'Pregnancy Complications',
        ];
    }

    #[Computed]
    public function dataStatus()
    {
        $service = app(SarimaPredictionService::class);
        $status = [];

        foreach (array_keys($this->diseaseTypes) as $type) {
            $status[$type] = $service->hasSufficientData($type);
        }

        return $status;
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
        $this->diseaseType = 'dengue';
        $this->barangayId = null;
        $this->recordDate = now()->startOfMonth()->format('Y-m-d');
        $this->caseCount = 0;
        $this->notes = '';
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'disease_type' => $this->diseaseType,
            'barangay_id' => $this->barangayId,
            'record_date' => $this->recordDate,
            'case_count' => $this->caseCount,
            'notes' => $this->notes,
            'data_source' => 'manual',
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            $record = HistoricalDiseaseData::findOrFail($this->editingId);
            $record->update($data);
            session()->flash('message', 'Historical data updated successfully.');
        } else {
            HistoricalDiseaseData::create($data);
            session()->flash('message', 'Historical data added successfully.');
        }

        $this->closeForm();
        unset($this->historicalData);
        unset($this->dataStatus);
    }

    public function edit(int $id): void
    {
        $record = HistoricalDiseaseData::findOrFail($id);

        $this->editingId = $id;
        $this->diseaseType = $record->disease_type;
        $this->barangayId = $record->barangay_id;
        $this->recordDate = \Carbon\Carbon::parse($record->record_date)->format('Y-m-d');
        $this->caseCount = $record->case_count;
        $this->notes = $record->notes ?? '';
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        HistoricalDiseaseData::findOrFail($id)->delete();
        session()->flash('message', 'Historical data deleted successfully.');
        unset($this->historicalData);
        unset($this->dataStatus);
    }

    // Predictions are generated automatically via a scheduled command.

    public function render()
    {
        return view('livewire.admin.manage-historical-data');
    }
}
