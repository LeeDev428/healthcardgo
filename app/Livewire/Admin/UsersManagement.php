<?php

namespace App\Livewire\Admin;

use App\Enums\AdminCategoryEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithPagination;

class UsersManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $roleFilter = 'all';

    public $statusFilter = 'all';

    public $selectedUserId = null;

    public $showDetailsModal = false;

    public $showEditModal = false;

    public $showCreateModal = false;

    public $form = [
        'name' => '',
        'email' => '',
        'password' => '',
        'contact_number' => '',
        'role_id' => '',
        'status' => '',
        'admin_category' => '',
        'doctor' => [
            'license_number' => '',
            'work_schedule' => null,
            'is_available' => true,
        ],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewDetails($userId)
    {
        $this->selectedUserId = $userId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedUserId = null;
    }

    public function createUser()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function storeUser()
    {
        $doctorRoleId = Role::where('name', 'doctor')->value('id');

        $rules = [
            'form.name' => 'required|string|max:255',
            'form.email' => 'required|email|unique:users,email',
            'form.password' => 'required|string|min:8',
            'form.contact_number' => 'nullable|string|max:20',
            'form.role_id' => 'required|exists:roles,id',
            'form.status' => 'required|in:active,inactive,pending',
            'form.admin_category' => ['nullable', new Enum(AdminCategoryEnum::class)],
            // Doctor-specific fields (conditionally required when role is doctor)
            'form.doctor.license_number' => 'required_if:form.role_id,'.$doctorRoleId.'|string|max:255|unique:doctors,license_number',
            'form.doctor.work_schedule' => 'nullable|array',
            'form.doctor.is_available' => 'nullable|boolean',
        ];

        $validatedData = $this->validate($rules);

        DB::transaction(function () use ($validatedData) {
            $userData = $validatedData['form'];
            $userData['password'] = Hash::make($userData['password']);

            // Normalize optional enum field to null if empty string
            if (array_key_exists('admin_category', $userData) && ($userData['admin_category'] === '' || $userData['admin_category'] === null)) {
                $userData['admin_category'] = null;
            }

            if ($userData['status'] === 'active') {
                $userData['approved_at'] = now();
                $userData['approved_by'] = Auth::id();
            }

            /** @var \App\Models\User $user */
            $user = User::create(collect($userData)
                ->except(['doctor'])
                ->toArray());

            // If role is doctor, create a corresponding doctor profile
            if ($user->role && $user->role->name === 'doctor') {
                $doctorData = $validatedData['form']['doctor'] ?? [];

                $user->doctor()->create([
                    'license_number' => $doctorData['license_number'] ?? null,
                    'work_schedule' => $doctorData['work_schedule'] ?? null,
                    'is_available' => $doctorData['is_available'] ?? true,
                ]);
            }
        });

        $this->dispatch('notify', message: 'User created successfully!', type: 'success');
        $this->closeCreateModal();
        $this->resetPage();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);

        $this->selectedUserId = $userId;
        $form = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => '',
            'contact_number' => $user->contact_number,
            'role_id' => $user->role_id,
            // Normalize legacy 'approved' to 'active' for consistency in UI
            'status' => $user->status === 'approved' ? 'active' : $user->status,
            'admin_category' => $user->admin_category,
        ];

        // Include doctor nested form data if role is doctor
        if ($user->role && $user->role->name === 'doctor') {
            $form['doctor'] = [
                'license_number' => $user->doctor?->license_number ?? '',
                'work_schedule' => $user->doctor?->work_schedule ?? null,
                'is_available' => $user->doctor?->is_available ?? true,
            ];
        } else {
            $form['doctor'] = [
                'license_number' => '',
                'work_schedule' => null,
                'is_available' => true,
            ];
        }

        $this->form = $form;

        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $doctorRoleId = Role::where('name', 'doctor')->value('id');

        $rules = [
            'form.name' => 'required|string|max:255',
            'form.email' => 'required|email|unique:users,email,'.$this->selectedUserId,
            'form.password' => 'nullable|string|min:8',
            'form.contact_number' => 'nullable|string|max:20',
            'form.role_id' => 'required|exists:roles,id',
            'form.status' => 'required|in:active,inactive,pending,rejected,suspended',
            'form.admin_category' => ['nullable', new Enum(AdminCategoryEnum::class)],
            'form.doctor.license_number' => 'required_if:form.role_id,'.$doctorRoleId.'|string|max:255|unique:doctors,license_number,'.($this->selectedUserId ? User::find($this->selectedUserId)?->doctor?->id : 'NULL'),
            'form.doctor.work_schedule' => 'nullable|array',
            'form.doctor.is_available' => 'nullable|boolean',
        ];

        $validatedData = $this->validate($rules);

        DB::transaction(function () use ($validatedData) {
            $user = User::findOrFail($this->selectedUserId);

            $userData = $validatedData['form'];

            // Only update password if provided
            if (! empty($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            } else {
                unset($userData['password']);
            }

            // Normalize optional enum field to null if empty string
            if (array_key_exists('admin_category', $userData) && ($userData['admin_category'] === '' || $userData['admin_category'] === null)) {
                $userData['admin_category'] = null;
            }

            // Handle status change to approved
            if ($userData['status'] === 'active' && $user->status !== 'active') {
                $userData['approved_at'] = now();
                $userData['approved_by'] = Auth::id();
            }

            $previousRoleName = $user->role?->name;

            $user->update(collect($userData)->except(['doctor'])->toArray());

            $currentRoleName = $user->fresh('role')->role?->name;

            // Handle doctor profile creation/update/removal
            $doctorPayload = $validatedData['form']['doctor'] ?? [];
            if ($currentRoleName === 'doctor') {
                if ($user->doctor) {
                    // Update existing profile (avoid overwriting license if unchanged)
                    $user->doctor->update(array_filter([
                        'license_number' => $doctorPayload['license_number'] ?? $user->doctor->license_number,
                        'work_schedule' => $doctorPayload['work_schedule'] ?? $user->doctor->work_schedule,
                        'is_available' => $doctorPayload['is_available'] ?? $user->doctor->is_available,
                    ], fn ($v) => $v !== null));
                } else {
                    $user->doctor()->create([
                        'license_number' => $doctorPayload['license_number'] ?? null,
                        'work_schedule' => $doctorPayload['work_schedule'] ?? null,
                        'is_available' => $doctorPayload['is_available'] ?? true,
                    ]);
                }
            } elseif ($previousRoleName === 'doctor' && $user->doctor) {
                // If role changed away from doctor, optionally remove profile (soft decision: keep for history?)
                $user->doctor()->delete();
            }
        });

        $this->dispatch('notify', message: 'User updated successfully!', type: 'success');
        $this->closeEditModal();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedUserId = null;
        $this->resetForm();
    }

    public function activateUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        $this->dispatch('notify', message: 'User activated successfully!', type: 'success');
    }

    public function deactivateUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'inactive']);

        $this->dispatch('notify', message: 'User deactivated successfully!', type: 'success');
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent deleting current user
        if ($user->id === Auth::id()) {
            $this->dispatch('notify', message: 'Cannot delete your own account.', type: 'error');

            return;
        }

        // Check if user has appointments - these should be handled carefully
        $appointmentsCount = $user->appointments()->count();
        if ($appointmentsCount > 0) {
            $this->dispatch('notify', message: "Cannot delete user with {$appointmentsCount} appointment(s). Please reassign or cancel them first, or deactivate the user instead.", type: 'error');

            return;
        }

        DB::transaction(function () use ($user) {
            // Delete related profiles (these have cascade delete in DB, but explicit is safer)
            if ($user->patient) {
                $user->patient->delete();
            }
            if ($user->doctor) {
                $user->doctor->delete();
            }
            // Delete the user (cascade will handle remaining FK relationships)
            $user->delete();
        });

        $this->dispatch('notify', message: 'User and related records deleted successfully!', type: 'success');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
            'email' => '',
            'password' => '',
            'contact_number' => '',
            'role_id' => '',
            'status' => '',
            'admin_category' => '',
            'doctor' => [
                'license_number' => '',
                'work_schedule' => null,
                'is_available' => true,
            ],
        ];
        $this->resetValidation();
    }

    public function render()
    {
        $query = User::query()->with('role');

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('contact_number', 'like', '%'.$this->search.'%');
            });
        }

        // Apply role filter
        if ($this->roleFilter !== 'all') {
            $query->where('role_id', $this->roleFilter);
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $statistics = [
            'total' => User::count(),
            // Count both legacy 'approved' and new 'active' as active
            'active' => User::whereIn('status', ['active', 'approved'])->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'pending' => User::where('status', 'pending')->count(),
            'admins' => User::query()
                ->whereHas('role', fn ($q) => $q->whereIn('name', ['super_admin', 'healthcare_admin']))
                ->count(),
            'doctors' => User::whereHas('role', fn ($q) => $q->where('name', 'doctor'))->count(),
            'patients' => User::whereHas('role', fn ($q) => $q->where('name', 'patient'))->count(),
        ];

        // Get selected user if viewing details
        $selectedUser = $this->selectedUserId
            ? User::with(['role', 'approver', 'doctor', 'patient'])->findOrFail($this->selectedUserId)
            : null;

        $roles = Role::query()
            // ->where('name', '!=', 'patient')
            ->where('is_active', true)
            ->get();

        return view('livewire.admin.users-management', [
            'users' => $users,
            'statistics' => $statistics,
            'selectedUser' => $selectedUser,
            'roles' => $roles,
            'categories' => AdminCategoryEnum::cases(),
        ]);
    }
}
