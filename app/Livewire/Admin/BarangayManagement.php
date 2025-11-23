<?php

namespace App\Livewire\Admin;

use App\Models\Barangay;
use Livewire\Component;
use Livewire\WithPagination;

class BarangayManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedBarangayId = null;

    public $showDetailsModal = false;

    public $showEditModal = false;

    public $showCreateModal = false;

    public $form = [
        'name' => '',
        'city' => 'Panabo City',
        'latitude' => '',
        'longitude' => '',
        'population' => '',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDetails($barangayId)
    {
        $this->selectedBarangayId = $barangayId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedBarangayId = null;
    }

    public function createBarangay()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function storeBarangay()
    {
        $validatedData = $this->validate([
            'form.name' => 'required|string|max:255|unique:barangays,name',
            'form.city' => 'required|string|max:255',
            'form.latitude' => 'nullable|numeric|between:-90,90',
            'form.longitude' => 'nullable|numeric|between:-180,180',
            'form.population' => 'nullable|integer|min:0',
        ]);

        Barangay::create($validatedData['form']);

        $this->dispatch('notify', message: 'Barangay created successfully!', type: 'success');
        $this->closeCreateModal();
        $this->resetPage();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function editBarangay($barangayId)
    {
        $barangay = Barangay::findOrFail($barangayId);

        $this->selectedBarangayId = $barangayId;
        $this->form = [
            'name' => $barangay->name,
            'city' => $barangay->city,
            'latitude' => $barangay->latitude,
            'longitude' => $barangay->longitude,
            'population' => $barangay->population,
        ];

        $this->showEditModal = true;
    }

    public function updateBarangay()
    {
        $validatedData = $this->validate([
            'form.name' => 'required|string|max:255|unique:barangays,name,'.$this->selectedBarangayId,
            'form.city' => 'required|string|max:255',
            'form.latitude' => 'nullable|numeric|between:-90,90',
            'form.longitude' => 'nullable|numeric|between:-180,180',
            'form.population' => 'nullable|integer|min:0',
        ]);

        $barangay = Barangay::findOrFail($this->selectedBarangayId);
        $barangay->update($validatedData['form']);

        $this->dispatch('notify', message: 'Barangay updated successfully!', type: 'success');
        $this->closeEditModal();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedBarangayId = null;
        $this->resetForm();
    }

    public function deleteBarangay($barangayId)
    {
        $barangay = Barangay::findOrFail($barangayId);

        // Check if barangay has patients
        if ($barangay->patients()->count() > 0) {
            $this->dispatch('notify', message: 'Cannot delete barangay with existing patients.', type: 'error');

            return;
        }

        $barangay->delete();
        $this->dispatch('notify', message: 'Barangay deleted successfully!', type: 'success');
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
            'city' => 'Panabo City',
            'latitude' => '',
            'longitude' => '',
            'population' => '',
        ];
        $this->resetValidation();
    }

    public function render()
    {
        $query = Barangay::query()->withCount('patients');

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('city', 'like', '%'.$this->search.'%');
            });
        }

        $barangays = $query->orderBy('name')->paginate(15);

        // Calculate statistics
        $statistics = [
            'total' => Barangay::count(),
            'with_patients' => Barangay::has('patients')->count(),
            'total_population' => Barangay::sum('population'),
            'total_patients' => Barangay::withCount('patients')->get()->sum('patients_count'),
            'avg_patients_per_barangay' => Barangay::count() > 0 ? round(Barangay::withCount('patients')->get()->avg('patients_count'), 1) : 0,
            'with_coordinates' => Barangay::whereNotNull('latitude')->whereNotNull('longitude')->count(),
        ];

        // Get selected barangay if viewing details
        $selectedBarangay = $this->selectedBarangayId
            ? Barangay::withCount('patients')->findOrFail($this->selectedBarangayId)
            : null;

        return view('livewire.admin.barangay-management', [
            'barangays' => $barangays,
            'statistics' => $statistics,
            'selectedBarangay' => $selectedBarangay,
        ]);
    }
}
