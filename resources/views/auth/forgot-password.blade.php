<x-guest-layout>
    <div class="mb-3 text-secondary small">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link to choose a new one.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success rounded-3 mb-4 small" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input 
                id="email" 
                class="form-control @error('email') is-invalid @enderror" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Email Password Reset Link
        </button>

        <div class="text-center small">
            <a href="{{ route('login') }}" class="text-link">Back to Log in</a>
        </div>
    </form>
</x-guest-layout>
