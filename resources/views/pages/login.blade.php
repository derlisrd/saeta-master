<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="flex min-h-screen">
    <div class="flex-1 flex justify-center items-center">
        <div class="w-80 max-w-80 space-y-6">
            <div class="flex justify-center opacity-50">
                <a href="/" class="group flex items-center gap-3">
                    <div>
                        <svg class="h-4 text-zinc-800 dark:text-white" viewBox="0 0 18 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <line x1="1" y1="5" x2="1" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="5" y1="1" x2="5" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="9" y1="5" x2="9" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="13" y1="1" x2="13" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="17" y1="5" x2="17" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                            </g>
                        </svg>
                    </div>

                    <span class="text-xl font-semibold text-zinc-800 dark:text-white">flux</span>
                </a>
            </div>

            <flux:heading class="text-center" size="xl">Welcome back</flux:heading>


            <div class="flex flex-col gap-6">
                <flux:input label="Email" type="email" placeholder="email@example.com" />

                <flux:field>
                    <div class="mb-3 flex justify-between">
                        <flux:label>Password</flux:label>

                        <flux:link href="#" variant="subtle" class="text-sm">Forgot password?</flux:link>
                    </div>

                    <flux:input type="password" placeholder="Your password" />
                </flux:field>

                <flux:checkbox label="Remember me for 30 days" />

                <flux:button variant="primary" class="w-full">Log in</flux:button>
            </div>

            <flux:subheading class="text-center">
                First time around here? <flux:link href="#">Sign up for free</flux:link>
            </flux:subheading>
        </div>
    </div>


</div>