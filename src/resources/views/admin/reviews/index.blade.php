@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">Product</th><th class="p-3">Customer</th><th class="p-3">Rating</th><th class="p-3">Review</th><th class="p-3">Status</th><th class="p-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($reviews as $review)
                <tr>
                    <td class="p-3">{{ $review->product->name }}</td>
                    <td class="p-3">{{ $review->customer_name }}</td>
                    <td class="p-3 text-wine-700">@for($i = 0; $i < $review->rating; $i++)★@endfor</td>
                    <td class="p-3 text-neutral-600 max-w-xs truncate">{{ $review->body }}</td>
                    <td class="p-3">{{ $review->is_approved ? '✓ Visible' : 'Hidden' }}</td>
                    <td class="p-3 text-right whitespace-nowrap">
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                            @csrf @method('PATCH')<button class="underline mr-3">{{ $review->is_approved ? 'Hide' : 'Approve' }}</button>
                        </form>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Delete this review?')">
                            @csrf @method('DELETE')<button class="underline text-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-8 text-center text-neutral-500">No reviews yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
