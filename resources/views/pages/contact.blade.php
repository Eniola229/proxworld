@extends('layouts.app')

@section('title', 'Contact — ProxWorld')
@section('description', 'Contact ProxWorld for sales, support or reseller questions.')

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ url('/') }}">Home</a> / Contact</div>
        <h1>Talk to us</h1>
        <p class="lede">Questions about proxies, an order, or reselling? Send a message and we will reply by email, or write to <a href="mailto:info@proxworld.shop" style="color:var(--accent)">info@proxworld.shop</a>.</p>
    </div>
</section>

<section class="section">
    <div class="container split" style="align-items:flex-start">
        <div class="form-card">
            @if (session('contact_sent'))
                <div class="alert-ok" role="status">Thanks, your message is on its way. We will reply by email.</div>
            @endif
            @if (session('contact_failed'))
                <div class="alert-bad" role="alert">Your message could not be sent. Please try again in a moment.</div>
            @endif

            <form method="POST" action="{{ route('contact.send') }}" novalidate>
                @csrf

                <div class="field">
                    <label for="name">Your name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                    @error('name') <div class="err">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                    @error('email') <div class="err">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
                    @error('subject') <div class="err">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                    @error('message') <div class="err">{{ $message }}</div> @enderror
                </div>

                {{-- Honeypot: hidden from people, bots fill it in --}}
                <div class="hp" aria-hidden="true">
                    <label for="website">Leave this empty</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" class="btn btn-solid btn-large btn-block">Send message</button>
            </form>
        </div>

        <div>
            <div class="section-head" style="margin-bottom:1.5rem">
                <h2>Before you write</h2>
                <p>These might answer your question faster.</p>
            </div>
            <div class="tiles c2" style="grid-template-columns:1fr">
                <a class="tile" href="{{ route('faq') }}" style="text-decoration:none;color:inherit">
                    <div class="ico"><i class="fas fa-circle-question" aria-hidden="true"></i></div>
                    <h3>FAQ</h3>
                    <p>Payment, verification, proxy types and more.</p>
                </a>
                <a class="tile" href="{{ route('guides') }}" style="text-decoration:none;color:inherit">
                    <div class="ico"><i class="fas fa-book-open" aria-hidden="true"></i></div>
                    <h3>Setup guides</h3>
                    <p>Connect your proxies in cURL, Python, Node.js or PHP.</p>
                </a>
                <a class="tile" href="{{ route('api.docs') }}" style="text-decoration:none;color:inherit">
                    <div class="ico"><i class="fas fa-code" aria-hidden="true"></i></div>
                    <h3>API docs</h3>
                    <p>Order and manage proxies from your own code.</p>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
