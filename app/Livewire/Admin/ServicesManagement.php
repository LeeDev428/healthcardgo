<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServicesManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $categoryFilter = 'all';

    public $statusFilter = 'all';

    public $selectedServiceId = null;

    public $showDetailsModal = false;

    public $showEditModal = false;

    public $showCreateModal = false;

    public $form = [
        'name' => '',
        'description' => '',
        'duration_minutes' => '',
        'fee' => '',
        'category' => '',
        'requirements' => '',
        'preparation_instructions' => '',
        'requires_appointment' => true,
        'is_active' => true,
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewDetails($serviceId)
    {
        $this->selectedServiceId = $serviceId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedServiceId = null;
    }

    public function createService()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function storeService()
    {
        $validatedData = $this->validate([
            'form.name' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.duration_minutes' => 'nullable|integer|min:1',
            'form.fee' => 'nullable|numeric|min:0',
            'form.category' => 'required|string',
            'form.requirements' => 'nullable|string',
            'form.preparation_instructions' => 'nullable|string',
            'form.requires_appointment' => 'boolean',
            'form.is_active' => 'boolean',
        ]);

        $serviceData = $validatedData['form'];

        // Convert text fields to arrays for JSON storage
        if (! empty($serviceData['requirements'])) {
            $serviceData['requirements'] = array_map('trim', explode("\n", $serviceData['requirements']));
        } else {
            $serviceData['requirements'] = null;
        }

        if (! empty($serviceData['preparation_instructions'])) {
            $serviceData['preparation_instructions'] = array_map('trim', explode("\n", $serviceData['preparation_instructions']));
        } else {
            $serviceData['preparation_instructions'] = null;
        }

        Service::create($serviceData);

        $this->dispatch('notify', message: 'Service created successfully!', type: 'success');
        $this->closeCreateModal();
        $this->resetPage();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function editService($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $this->selectedServiceId = $serviceId;
        $this->form = [
            'name' => $service->name,
            'description' => $service->description,
            'duration_minutes' => $service->duration_minutes,
            'fee' => $service->fee,
            'category' => $service->category,
            'requirements' => is_array($service->requirements) ? implode("\n", $service->requirements) : '',
            'preparation_instructions' => is_array($service->preparation_instructions) ? implode("\n", $service->preparation_instructions) : '',
            'requires_appointment' => $service->requires_appointment,
            'is_active' => $service->is_active,
        ];

        $this->showEditModal = true;
    }

    public function updateService()
    {
        $validatedData = $this->validate([
            'form.name' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.duration_minutes' => 'nullable|integer|min:1',
            'form.fee' => 'nullable|numeric|min:0',
            'form.category' => 'required|string',
            'form.requirements' => 'nullable|string',
            'form.preparation_instructions' => 'nullable|string',
            'form.requires_appointment' => 'boolean',
            'form.is_active' => 'boolean',
        ]);

        $service = Service::findOrFail($this->selectedServiceId);

        $serviceData = $validatedData['form'];

        // Convert text fields to arrays for JSON storage
        if (! empty($serviceData['requirements'])) {
            $serviceData['requirements'] = array_map('trim', explode("\n", $serviceData['requirements']));
        } else {
            $serviceData['requirements'] = null;
        }

        if (! empty($serviceData['preparation_instructions'])) {
            $serviceData['preparation_instructions'] = array_map('trim', explode("\n", $serviceData['preparation_instructions']));
        } else {
            $serviceData['preparation_instructions'] = null;
        }

        $service->update($serviceData);

        $this->dispatch('notify', message: 'Service updated successfully!', type: 'success');
        $this->closeEditModal();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedServiceId = null;
        $this->resetForm();
    }

    public function deactivateService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->update(['is_active' => false]);

        $this->dispatch('notify', message: 'Service deactivated successfully!', type: 'success');
    }

    public function activateService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->update(['is_active' => true]);

        $this->dispatch('notify', message: 'Service activated successfully!', type: 'success');
    }

    public function deleteService($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        // Check if service has appointments
        if ($service->appointments()->count() > 0) {
            $this->dispatch('notify', message: 'Cannot delete service with existing appointments. Deactivate instead.', type: 'error');

            return;
        }

        $service->delete();
        $this->dispatch('notify', message: 'Service deleted successfully!', type: 'success');
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
            'description' => '',
            'duration_minutes' => '',
            'fee' => '',
            'category' => '',
            'requirements' => '',
            'preparation_instructions' => '',
            'requires_appointment' => true,
            'is_active' => true,
        ];
        $this->resetValidation();
    }

    public function render()
    {
        $query = Service::query()->withCount('appointments');

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhere('category', 'like', '%'.$this->search.'%');
            });
        }

        // Apply category filter
        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $services = $query->orderBy('name')->paginate(15);

        // Calculate statistics
        $statistics = [
            'total' => Service::count(),
            'active' => Service::where('is_active', true)->count(),
            'inactive' => Service::where('is_active', false)->count(),
            'with_appointments' => Service::has('appointments')->count(),
            'free_services' => Service::where('fee', 0)->orWhereNull('fee')->count(),
            'walk_in' => Service::where('requires_appointment', false)->count(),
        ];

        // Get selected service if viewing details
        $selectedService = $this->selectedServiceId
            ? Service::with('appointments')->withCount('appointments')->findOrFail($this->selectedServiceId)
            : null;

        $categories = Service::getCategories();

        return view('livewire.admin.services-management', [
            'services' => $services,
            'statistics' => $statistics,
            'selectedService' => $selectedService,
            'categories' => $categories,
        ]);
    }
}
