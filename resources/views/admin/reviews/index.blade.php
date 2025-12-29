@extends('layouts.dwello')

@section('title', 'Admin - Pending Reviews')

@section('content')
<div class="container" style="padding: 48px 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
        <h1 style="font-size: 32px; font-weight: 700; color: var(--gray-900);">Admin Dashboard</h1>
    </div>

    {{-- Stats Grid --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 48px;">
        <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid var(--gray-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size: 14px; font-weight: 500; color: var(--gray-500); margin-bottom: 8px;">Pending Reviews</div>
            <div style="font-size: 32px; font-weight: 700; color: #f59e0b;">{{ $stats['pending'] }}</div>
        </div>
        <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid var(--gray-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size: 14px; font-weight: 500; color: var(--gray-500); margin-bottom: 8px;">Approved Reviews</div>
            <div style="font-size: 32px; font-weight: 700; color: #10b981;">{{ $stats['approved'] }}</div>
        </div>
        <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid var(--gray-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size: 14px; font-weight: 500; color: var(--gray-500); margin-bottom: 8px;">Total Reviews</div>
            <div style="font-size: 32px; font-weight: 700; color: var(--gray-900);">{{ $stats['total'] }}</div>
        </div>
    </div>

    <h2 style="font-size: 24px; font-weight: 600; color: var(--gray-900); margin-bottom: 24px;">Pending Approvals</h2>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background: #ecfdf5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    @if($reviews->count() > 0)
        <div style="background: white; border-radius: 16px; border: 1px solid var(--gray-200); overflow: hidden;">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                    <tr>
                        <th style="padding: 16px; font-weight: 600; font-size: 14px; color: var(--gray-600);">Date</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 14px; color: var(--gray-600);">Property</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 14px; color: var(--gray-600);">Reviewer</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 14px; color: var(--gray-600);">Rating</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 14px; color: var(--gray-600); width: 40%;">Comment</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 14px; color: var(--gray-600); text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid var(--gray-100);">
                    @foreach($reviews as $review)
                        <tr style="border-bottom: 1px solid var(--gray-100);">
                            <td style="padding: 16px; color: var(--gray-500); font-size: 14px;">{{ $review->created_at->format('M d, Y') }}</td>
                            <td style="padding: 16px; font-weight: 500;">
                                <a href="{{ route('properties.show', $review->property) }}" target="_blank" style="color: var(--dwello-primary); text-decoration: none;">
                                    {{ Str::limit($review->property->title, 30) }}
                                </a>
                            </td>
                            <td style="padding: 16px;">{{ $review->user->name }}</td>
                            <td style="padding: 16px; color: #f59e0b; font-weight: 600;">{{ $review->rating }} ★</td>
                            <td style="padding: 16px; color: var(--gray-700);">{{ $review->comment }}</td>
                            <td style="padding: 16px; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 64px 0; background: var(--gray-50); border-radius: 16px;">
            <p style="color: var(--gray-500); font-size: 18px;">No pending reviews.</p>
        </div>
    @endif
</div>
@endsection
