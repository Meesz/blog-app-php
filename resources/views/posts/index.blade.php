<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Blog Posts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <a href="{{ route('posts.create') }}"
                    class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-bold py-3 px-6 rounded-lg transform transition duration-200 hover:scale-105 hover:shadow-lg">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Post
                    </span>
                </a>
            </div>

            @if($posts->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <p class="text-gray-600 text-center text-lg">There are no posts yet. Create one!</p>
                </div>
            @else
                <div id="sortable-posts" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <div class="post-item bg-white rounded-xl shadow-lg overflow-hidden" data-id="{{ $post->id }}">
                            <div class="p-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-3 line-clamp-2">
                                    <a href="{{ route('posts.show', $post) }}"
                                        class="hover:text-blue-600 transition duration-200">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                <p class="text-gray-600 mb-6 line-clamp-3 text-base leading-relaxed">
                                    {{ Str::limit($post->content, 150) }}
                                </p>
                                <div class="flex justify-between items-center border-t pt-4 mt-4 border-gray-100">
                                    <span class="text-sm text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                    <div class="space-x-3 flex items-center">
                                        @can('update', $post)
                                            <a href="{{ route('posts.edit', $post) }}"
                                                class="text-blue-500 hover:text-blue-700 transition duration-200 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                        @endcan

                                        @can('delete', $post)
                                            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 transition duration-200 flex items-center"
                                                    onclick="return confirm('Are you sure you want to delete this post?')">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sortable = new Sortable(document.getElementById('sortable-posts'), {
                animation: 150,
                onEnd: async function (evt) {
                    const order = Array.from(evt.to.children).map((item, index) => ({
                        id: item.getAttribute('data-id'),
                        order: index
                    }));

                    await fetch('{{ route("posts.updateOrder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(order)
                    });
                }
            });
        });
    </script>

    <style>
        .dragging {
            opacity: 0.7;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
    </style>
</x-app-layout>