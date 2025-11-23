<?php

namespace App\Livewire\Admin\Announcements;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app.sidebar')]
#[Title('Announcements')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $announcementId = null;

    public $title = '';
    public $content = '';
    public $is_active = true;
    public $published_at = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'is_active' => 'boolean',
        'published_at' => 'nullable|date',
    ];

    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $this->announcementId = $announcement->id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->is_active = $announcement->is_active;
        $this->published_at = $announcement->published_at?->format('Y-m-d\TH:i');
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'is_active' => $this->is_active,
            'published_at' => $this->published_at ? $this->published_at : now(),
        ];

        if ($this->editMode) {
            $announcement = Announcement::findOrFail($this->announcementId);
            $announcement->update($data);
            session()->flash('success', 'Announcement updated successfully!');
        } else {
            $data['created_by'] = Auth::id();
            Announcement::create($data);
            session()->flash('success', 'Announcement created successfully!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Announcement::findOrFail($id)->delete();
        session()->flash('success', 'Announcement deleted successfully!');
    }

    public function toggleActive($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => !$announcement->is_active]);
        session()->flash('success', 'Announcement status updated!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['title', 'content', 'is_active', 'published_at', 'announcementId', 'editMode']);
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.announcements.index', [
            'announcements' => Announcement::with('creator')
                ->latest('published_at')
                ->paginate(10),
        ]);
    }
}
