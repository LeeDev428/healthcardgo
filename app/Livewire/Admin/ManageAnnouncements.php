<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ManageAnnouncements extends Component
{
    use WithPagination;

    public $title = '';
    public $content = '';
    public $is_active = true;
    public $editingId = null;
    public $isModalOpen = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function openEditModal($id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->editingId = $id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->is_active = $announcement->is_active;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->title = '';
        $this->content = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $announcement = Announcement::findOrFail($this->editingId);
            $announcement->update([
                'title' => $this->title,
                'content' => $this->content,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Announcement updated successfully.');
        } else {
            Announcement::create([
                'title' => $this->title,
                'content' => $this->content,
                'is_active' => $this->is_active,
                'created_by' => Auth::id(),
            ]);
            session()->flash('message', 'Announcement created successfully.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Announcement::findOrFail($id)->delete();
        session()->flash('message', 'Announcement deleted successfully.');
    }

    public function toggleActive($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => !$announcement->is_active]);
        session()->flash('message', 'Announcement status updated.');
    }

    public function render()
    {
        return view('livewire.admin.manage-announcements', [
            'announcements' => Announcement::with('creator')
                ->latest()
                ->paginate(10),
        ]);
    }
}
