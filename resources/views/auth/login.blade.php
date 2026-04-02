<x-guest-layout>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                class="form-control @error('email') is-invalid @enderror"
                required 
                autofocus
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                class="form-control @error('password') is-invalid @enderror"
                required
            >

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input 
                type="checkbox" 
                name="remember" 
                id="remember_me" 
                class="form-check-input"
            >
            <label class="form-check-label" for="remember_me">
                Remember me
            </label>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center">

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small">
                    Forgot your password?
                </a>
            @endif

            <button type="submit" class="btn btn-primary">
                Log in
            </button>

        </div>
    </form>

</x-guest-layout>