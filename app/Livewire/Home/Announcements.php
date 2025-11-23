<?php

namespace App\Livewire\Home;

use App\Models\Announcement;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.home')]
class Announcements extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.home.announcements', [
            'announcements' => Announcement::where('is_active', true)
                ->with('creator')
                ->latest()
                ->paginate(6),
        ]);
    }
}
