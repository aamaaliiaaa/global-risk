<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input 
                id="name" 
                class="form-control @error('name') is-invalid @enderror" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                autocomplete="name"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

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
                autocomplete="new-password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input 
                id="password_confirmation" 
                class="form-control @error('password_confirmation') is-invalid @enderror" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Register
        </button>

        <div class="text-center small">
            <span class="text-secondary">Already registered?</span>
            <a href="{{ route('login') }}" class="text-link ms-1">Log in</a>
        </div>
    </form>
</x-guest-layout>
