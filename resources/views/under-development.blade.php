@extends('layouts.dwello')

@section('title', 'Under Development - Dwello')

@section('content')
<div class="container" style="min-height: calc(100vh - 160px); display:flex; align-items:center; justify-content:center; padding:40px 24px;">
    <div style="max-width:480px; background:white; border-radius:20px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:24px; text-align:center;">
        <h2 style="font-family:'Poppins',sans-serif; font-size:24px; font-weight:600; margin-bottom:8px; color:var(--gray-900);">
            This feature is under development
        </h2>
        <p style="color:var(--gray-600); font-size:14px; margin-bottom:20px;">
            We’re still building this part of Dwello. You’ll see it in a future sprint.
            In the meantime, you can browse rooms and flatmates that are already live.
        </p>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
            <a href="{{ route('properties.index') }}" class="btn btn-primary" style="border-radius:16px;">
                Browse rooms
            </a>
            <a href="{{ route('roommates.index') }}" class="btn btn-outline" style="border-radius:16px;">
                See flatmates
            </a>
        </div>
    </div>
</div>
@endsection
