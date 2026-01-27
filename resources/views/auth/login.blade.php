@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <form submit="{{ route('admin.login.submit') }}" method="post">
        <div class="flex min-h-screen">
            @csrf
            <div class="flex-1 flex justify-center items-center">
                <div class="w-80 max-w-80 space-y-6">
                    <flux:heading class="text-center" size="xl">Welcome back</flux:heading>
                    <div class="flex flex-col gap-6">
                        <flux:input label="Email" type="email" name="email"  placeholder="email@example.com" />
                        <flux:field>
                            <div class="mb-3 flex justify-between">
                                <flux:label>Password</flux:label>
                                <flux:link href="#" variant="subtle" class="text-sm">Forgot password?</flux:link>
                            </div>
                            <flux:input type="password" placeholder="Your password" name="password" />
                        </flux:field>

                        <flux:checkbox label="Remember me for 30 days" />

                        <flux:button variant="primary" type="submit" class="w-full">Log in</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
