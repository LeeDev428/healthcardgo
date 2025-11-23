<div class="max-w-7xl space-y-6">
  <!-- Header Section -->
  <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
      <div class="space-y-2">
        <flux:heading size="xl" class="text-zinc-900 dark:text-white">
          {{ __('Manage Announcements') }}
        </flux:heading>
        <flux:subheading class="text-zinc-600 dark:text-zinc-400">
          Create and manage system announcements
        </flux:subheading>
      </div>

      <flux:button wire:click="openCreateModal" variant="primary">
        <flux:icon name="plus" class="h-4 w-4 mr-2" />
        {{ __('New Announcement') }}
      </flux:button>
    </div>
  </div>

  <!-- Flash Message -->
  @if (session()->has('message'))
    <div
      class="rounded-lg border border-green-200 bg-green-50 p-4 shadow-sm dark:border-green-800 dark:bg-green-900/20">
      <div class="flex items-center">
        <flux:icon name="check-circle" class="h-5 w-5 text-green-600 dark:text-green-400 mr-3" />
        <flux:text class="text-green-800 dark:text-green-200">
          {{ session('message') }}
        </flux:text>
      </div>
    </div>
  @endif

  <!-- Announcements List -->
  <div class="rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
              {{ __('Title') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
              {{ __('Created By') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
              {{ __('Status') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
              {{ __('Created At') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
              {{ __('Actions') }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
          @forelse($announcements as $announcement)
            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <flux:text class="font-medium text-zinc-900 dark:text-white">
                    {{ $announcement->title }}
                  </flux:text>
                  <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    {{ Str::limit($announcement->content, 80) }}
                  </flux:text>
                </div>
              </td>
              <td class="px-6 py-4">
                <flux:text class="text-zinc-900 dark:text-white">
                  {{ $announcement->creator->name }}
                </flux:text>
              </td>
              <td class="px-6 py-4">
                @if ($announcement->is_active)
                  <flux:badge variant="success" size="sm">
                    {{ __('Active') }}
                  </flux:badge>
                @else
                  <flux:badge variant="ghost" size="sm">
                    {{ __('Inactive') }}
                  </flux:badge>
                @endif
              </td>
              <td class="px-6 py-4">
                <flux:text class="text-zinc-900 dark:text-white">
                  {{ $announcement->created_at->format('M j, Y') }}
                </flux:text>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <flux:button size="sm" variant="ghost" wire:click="toggleActive({{ $announcement->id }})">
                    <flux:icon name="{{ $announcement->is_active ? 'eye-slash' : 'eye' }}" class="h-4 w-4" />
                  </flux:button>
                  <flux:button size="sm" variant="ghost" wire:click="openEditModal({{ $announcement->id }})">
                    <flux:icon name="pencil" class="h-4 w-4" />
                  </flux:button>
                  <flux:button size="sm" variant="danger" wire:click="delete({{ $announcement->id }})"
                    wire:confirm="Are you sure you want to delete this announcement?">
                    <flux:icon name="trash" class="h-4 w-4" />
                  </flux:button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                  <flux:icon name="megaphone" class="h-12 w-12 text-zinc-400 dark:text-zinc-600 mb-3" />
                  <flux:heading size="lg" class="text-zinc-700 dark:text-zinc-300 mb-2">
                    {{ __('No announcements yet') }}
                  </flux:heading>
                  <flux:text class="text-zinc-500 dark:text-zinc-400">
                    {{ __('Create your first announcement to get started') }}
                  </flux:text>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if ($announcements->hasPages())
      <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
        {{ $announcements->links() }}
      </div>
    @endif
  </div>

  <!-- Create/Edit Modal -->
  @if ($isModalOpen)
    <flux:modal name="announcement-modal" class="min-w-[600px]">
      <form wire:submit="save">
        <div class="space-y-6">
          <div>
            <flux:heading size="lg">
              {{ $editingId ? __('Edit Announcement') : __('Create Announcement') }}
            </flux:heading>
          </div>

          <div class="space-y-4">
            <div>
              <flux:label for="title">{{ __('Title') }}</flux:label>
              <flux:input wire:model="title" id="title" placeholder="Enter announcement title" />
              @error('title')
                <flux:text class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</flux:text>
              @enderror
            </div>

            <div>
              <flux:label for="content">{{ __('Content') }}</flux:label>
              <flux:textarea wire:model="content" id="content" rows="6"
                placeholder="Enter announcement content"></flux:textarea>
              @error('content')
                <flux:text class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</flux:text>
              @enderror
            </div>

            <div>
              <flux:checkbox wire:model="is_active" label="{{ __('Active') }}" />
              <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Only active announcements will be displayed on the homepage') }}
              </flux:text>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <flux:button type="button" variant="ghost" wire:click="closeModal">
              {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary">
              {{ $editingId ? __('Update') : __('Create') }}
            </flux:button>
          </div>
        </div>
      </form>
    </flux:modal>
  @endif
</div>
