@php
    $panelId = filament()->getId();
    $optionsUrl = route("filament.{$panelId}.passkeys.verify.options");
    $submitUrl = route("filament.{$panelId}.passkeys.verify");
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            encryptedUser: '',
            supported: true,
            verified: false,
            loading: false,

            init() {
                this.supported = window.FilamentPasskeys?.isSupported() ?? false
                this.encryptedUser = $wire.userUndertakingMultiFactorAuthentication ?? ''

                const form = this.$el.closest('form')

                if (form) {
                    this.submitHandler = (event) => this.handleSubmit(event)
                    form.addEventListener('submit', this.submitHandler, { capture: true })
                }

                if (this.supported) {
                    this.autofill()
                }
            },

            destroy() {
                if (this.submitHandler) {
                    this.$el.closest('form')?.removeEventListener('submit', this.submitHandler, { capture: true })
                }

                if (this.supported) {
                    window.FilamentPasskeys.cancel()
                }
            },

            routes() {
                return {
                    options: @js($optionsUrl) + '?user=' + encodeURIComponent(this.encryptedUser),
                    submit: @js($submitUrl),
                }
            },

            async markVerifiedAndSubmit(form) {
                this.verified = true
                await $wire.set(@js($statePath), 'verified')
                this.$nextTick(() => form.requestSubmit())
            },

            async autofill() {
                try {
                    const result = await window.FilamentPasskeys.autofill({
                        routes: this.routes(),
                    })

                    if (result) {
                        this.markVerifiedAndSubmit(this.$el.closest('form'))
                    }
                } catch {
                    // No matching passkey, or autofill unsupported.
                }
            },

            async handleSubmit(event) {
                if (this.verified) return

                event.preventDefault()
                event.stopImmediatePropagation()

                this.loading = true

                try {
                    await window.FilamentPasskeys.verify({ routes: this.routes() })
                    this.markVerifiedAndSubmit(event.target)
                } catch (e) {
                    if (e.name === 'UserCancelledError' || e.constructor?.name === 'UserCancelledError') {
                        return
                    }

                    new window.FilamentNotification()
                        .title(@js(__('filament-passkeys::passkeys.challenge.failed')))
                        .danger()
                        .send()
                } finally {
                    this.loading = false
                }
            },
        }"
    >
        <template x-if="!supported">
            <x-filament::callout
                color="danger"
                icon="heroicon-m-exclamation-triangle"
                :heading="__('filament-passkeys::passkeys.challenge.unsupported')"
            />
        </template>

        <template x-if="supported">
            <div>
                <input
                    autocomplete="webauthn"
                    tabindex="-1"
                    aria-hidden="true"
                    style="position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none;"
                />

                <x-filament::callout
                    color="info"
                    icon="heroicon-m-finger-print"
                    :heading="__('filament-passkeys::passkeys.challenge.callout.heading')"
                >
                    <x-slot:description>
                        <span x-show="!loading">{{ __('filament-passkeys::passkeys.challenge.callout.description') }}</span>
                        <span x-show="loading" x-cloak>{{ __('filament-passkeys::passkeys.challenge.callout.waiting') }}</span>
                    </x-slot:description>
                </x-filament::callout>
            </div>
        </template>
    </div>
</x-dynamic-component>
