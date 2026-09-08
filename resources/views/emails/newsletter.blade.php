@extends('emails.layout')
@section('content')
    @if($newsletter->featured_image_url)
        <img src="{{ $newsletter->featured_image_url }}" alt="" style="width:100%;border-radius:8px;margin-bottom:16px;">
    @endif
    <h2>{{ $newsletter->subject }}</h2>
    <div>{!! $newsletter->body !!}</div>
    <p style="font-size:12px;color:#9ca3af;margin-top:24px;">
        You're receiving this because you have a {{ config('app.name') }} account.
    </p>
@endsection
