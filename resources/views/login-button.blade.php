@php
    $panelId = filament()->getId();
    $optionsUrl = route("filament.{$panelId}.passkeys.login.options");
    $submitUrl = route("filament.{$panelId}.passkeys.login");
@endphp

<div
    class="fi-passkeys-login-button"
    x-data="{
        supported: true,
        loading: false,
        emailInput: null,

        init() {
            this.supported = window.FilamentPasskeys?.isSupported() ?? false

            if (! this.supported) return

            this.emailInput = document.querySelector('input[type=email]')

            if (this.emailInput) {
                this.emailInput.setAttribute('autocomplete', 'email webauthn')
            }

            this.startAutofill()
        },

        destroy() {
            if (this.supported) {
                window.FilamentPasskeys.cancel()
            }
        },

        routes() {
            return {
                options: @js($optionsUrl),
                submit: @js($submitUrl),
            }
        },

        async startAutofill() {
            try {
                const result = await window.FilamentPasskeys.autofill({ routes: this.routes() })

                if (result?.redirect) {
                    window.location.href = result.redirect
                }
            } catch (e) {
                console.debug('[filament-passkeys] autofill ended', e)
            }
        },

        async verify() {
            if (this.loading || ! this.supported) return

            this.loading = true

            try {
                const result = await window.FilamentPasskeys.verify({ routes: this.routes() })

                if (result?.redirect) {
                    window.location.href = result.redirect
                    return
                }
            } catch (e) {
                const cancelled = e?.name === 'UserCancelledError'
                    || e?.constructor?.name === 'UserCancelledError'
                    || e?.message?.includes('abort')

                if (! cancelled) {
                    const serverMessage = e?.message && ! e.message.startsWith('Request failed with status')
                        ? e.message
                        : null

                    new window.FilamentNotification()
                        .title(serverMessage ?? @js(__('filament-passkeys::passkeys.login.failed')))
                        .danger()
                        .send()
                }
            } finally {
                this.loading = false
            }
        },
    }"
>
    <template x-if="supported">
        <x-filament::button
            type="button"
            color="gray"
            icon="filamentpasskeys-m-user-key"
            x-on:click="verify()"
            x-bind:disabled="loading"
            class="w-full"
        >
            {{ __('filament-passkeys::passkeys.login.button') }}
        </x-filament::button>
    </template>
</div>
