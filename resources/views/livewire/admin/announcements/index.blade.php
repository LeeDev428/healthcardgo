<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Announcements</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400 mt-1">
                Manage system-wide announcements for the homepage
            </flux:text>
        </div>
        <flux:button wire:click="create" icon="plus" variant="primary">
            New Announcement
        </flux:button>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <flux:callout variant="success" class="mb-6">{{ session('success') }}</flux:callout>
    @endif

    <!-- Announcements List -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
        @if($announcements->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Content Preview
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Published
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Created By
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($announcements as $announcement)
                            <tr wire:key="announcement-{{ $announcement->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text weight="semibold">{{ $announcement->title }}</flux:text>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 line-clamp-2">
                                        {{ Str::limit($announcement->content, 100) }}
                                    </flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text size="sm">
                                        {{ $announcement->published_at?->format('M d, Y g:i A') ?? 'Not published' }}
                                    </flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        wire:click="toggleActive({{ $announcement->id }})"
                                        class="inline-flex items-center">
                                        @if($announcement->is_active)
                                            <flux:badge variant="success" size="sm">Active</flux:badge>
                                        @else
                                            <flux:badge variant="outline" size="sm">Inactive</flux:badge>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text size="sm">{{ $announcement->creator->name }}</flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <flux:button wire:click="edit({{ $announcement->id }})" size="sm" variant="ghost" icon="pencil">
                                        Edit
                                    </flux:button>
                                    <flux:button
                                        wire:click="delete({{ $announcement->id }})"
                                        wire:confirm="Are you sure you want to delete this announcement?"
                                        size="sm"
                                        variant="danger"
                                        icon="trash">
                                        Delete
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $announcements->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <flux:icon name="megaphone" class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                <flux:heading size="lg" class="mb-2">No announcements yet</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400 mb-6">
                    Get started by creating your first announcement.
                </flux:text>
                <flux:button wire:click="create" icon="plus" variant="primary">
                    Create Announcement
                </flux:button>
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
            @click.self="$wire.closeModal()"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;">
            
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                @click.stop>
                
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 px-6 py-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ $editMode ? 'Edit' : 'Create' }} Announcement</flux:heading>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <flux:icon name="x-mark" size="lg" />
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit="save" class="px-6 py-6 space-y-6">
                    <!-- Title -->
                    <flux:field>
                        <flux:label>Title <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="title" placeholder="Enter announcement title" />
                        <flux:error name="title" />
                    </flux:field>

                    <!-- Content -->
                    <flux:field>
                        <flux:label>Content <span class="text-red-500">*</span></flux:label>
                        <flux:textarea wire:model="content" rows="8" placeholder="Enter announcement content" />
                        <flux:error name="content" />
                    </flux:field>

                    <!-- Published At -->
                    <flux:field>
                        <flux:label>Publish Date & Time</flux:label>
                        <flux:input type="datetime-local" wire:model="published_at" />
                        <flux:description>Leave empty to publish immediately</flux:description>
                        <flux:error name="published_at" />
                    </flux:field>

                    <!-- Active Status -->
                    <flux:field>
                        <flux:checkbox wire:model="is_active">
                            Active
                        </flux:checkbox>
                        <flux:description>Only active announcements will be shown on the homepage</flux:description>
                    </flux:field>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button type="button" wire:click="closeModal" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editMode ? 'Update' : 'Create' }}</span>
                            <span wire:loading>Saving...</span>
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
