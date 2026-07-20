<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success rounded-3 mb-4 small" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input 
                id="email" 
                class="form-control @error('email') is-invalid @enderror" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input 
                id="password" 
                class="form-control @error('password') is-invalid @enderror" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="form-check-input" 
                    name="remember"
                >
                <label for="remember_me" class="form-check-label small text-secondary">Remember me</label>
            </div>
            
            @if (Route::has('password.request'))
                <a class="text-link" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Log in
        </button>

        @if (Route::has('register'))
            <div class="text-center small">
                <span class="text-secondary">Don't have an account?</span>
                <a href="{{ route('register') }}" class="text-link ms-1">Register</a>
            </div>
        @endif
    </form>
</x-guest-layout>
