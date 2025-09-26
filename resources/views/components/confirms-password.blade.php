@props(['title' => __('Confirm Password')])

<span
    {{ $attributes->wire('then') }}
    x-data="{
        show: false,
        id: (window.crypto?.randomUUID?.() || Math.random().toString(36).slice(2)),
        pw: '',
    }"
    x-on:click.prevent="$wire.startConfirmingPassword(id); show = true"
    x-on:password-confirmed.window="
        if ($event.detail.id === id) {
            show = false; pw = '';
            $el.dispatchEvent(new CustomEvent('then', { bubbles: false }));
        }
    "
>
    {{ $slot }}

    {{-- Teleport modal + backdrop to <body> so stacking is always correct --}}
    <template x-teleport="body">
        <!-- Modal -->
        <div class="modal fade"
             x-cloak
             x-bind:class="{ 'show d-block': show }"
             role="dialog"
             aria-modal="true"
             :aria-hidden="(!show).toString()"
             x-on:keydown.escape.window="show = false; pw=''; $wire.stopConfirmingPassword()"
             x-on:confirming-password.window="setTimeout(() => $refs.cpwd?.focus(), 250)">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $title }}</h5>
                        <button type="button" class="btn-close"
                                @click="show=false; pw=''; $wire.stopConfirmingPassword()"
                                aria-label="{{ __('Close') }}"></button>
                    </div>

                    <div class="modal-body">
                        {{ __('For your security, please confirm your password to continue.') }}

                        <div class="mt-3">
                            <wa-input type="password"
                                      placeholder="{{ __('Password') }}"
                                      autocomplete="current-password"
                                      x-ref="cpwd"
                                      :value="pw"
                                      x-on:input="pw = $event.target.value; $wire.set('confirmablePassword', pw)"
                                      x-on:keydown.enter="$wire.confirmPassword()">
                            </wa-input>

                            @error('confirmablePassword')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <wa-button type="button" variant="neutral" appearance="outlined"
                                   @click="show=false; pw=''; $wire.stopConfirmingPassword()"
                                   wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </wa-button>
                        <wa-button type="submit" variant="brand" appearance="accent" class="ms-2"
                                   dusk="confirm-password-button"
                                   wire:click="confirmPassword" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </wa-button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div class="modal-backdrop fade"
             x-cloak
             x-bind:class="{ 'show d-block': show }"></div>
    </template>
</span>
