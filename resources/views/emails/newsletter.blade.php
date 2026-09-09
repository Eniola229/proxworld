@extends('emails.layout')

@section('title', $newsletter->subject)
@section('preheader', $newsletter->excerpt ?? '')

@section('content')
    @if($newsletter->featured_image_url)
        <img src="{{ $newsletter->featured_image_url }}" alt="" style="width:100%; height:auto; border-radius:8px; margin:0 0 20px; display:block;">
    @endif

    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#111827; line-height:1.3;">{{ $newsletter->subject }}</h1>

    <div style="font-size:15px; color:#374151; line-height:1.7;">
        {!! $newsletter->body !!}
    </div>

    <p style="margin:28px 0 0; padding-top:16px; border-top:1px solid #eef1f6; font-size:12px; color:#9ca3af;">
        You're receiving this because you have a {{ config('app.name') }} account.
    </p>
@endsection
