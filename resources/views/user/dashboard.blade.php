@extends('layouts.user-layout')

@section('title', 'User Dashboard')

@section('pageTitle', 'Welcome, ' . ($user->first_name ?? 'User'))

@section('content')

<!-- Floating Facebook support link (text left, icon right, no container background) -->
<a id="fb-support" href="https://web.facebook.com/CDOHMBLSC" target="_blank" rel="noopener noreferrer"
    style="position:fixed;right:16px;bottom:16px;z-index:1050;display:flex;align-items:center;color:#1877F2;text-decoration:none;padding:6px 8px;border-radius:6px;font-size:14px;background:transparent;">
    <span style="color:#222;margin-right:8px;">Please follow and support us on Facebook</span>
    <!-- Inline Facebook SVG icon -->
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path
        d="M22 12.07C22 6.48 17.52 2 12 2S2 6.48 2 12.07C2 17.09 5.66 21.26 10.44 22v-7.03H7.9v-2.9h2.54V9.41c0-2.5 1.49-3.88 3.77-3.88 1.09 0 2.23.2 2.23.2v2.45h-1.25c-1.23 0-1.61.77-1.61 1.56v1.88h2.74l-.44 2.9h-2.3V22C18.34 21.26 22 17.09 22 12.07z"
        fill="#1877F2" />
    </svg>
</a>
@endsection
