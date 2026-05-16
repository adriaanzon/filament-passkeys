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
                this.form = this.$el.closest('form')

                if (this.form) {
                    this.submitHandler = (event) => this.handleSubmit(event)
                    this.form.addEventListener('submit', this.submitHandler, { capture: true })
                }

                if (this.supported && this.form) {
                    this.tryVerify(this.form)
                }
            },

            destroy() {
                if (this.submitHandler && this.form) {
                    this.form.removeEventListener('submit', this.submitHandler, { capture: true })
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

            markVerifiedAndSubmit(form) {
                this.verified = true
                this.$refs.credential.value = 'verified'
                this.$refs.credential.dispatchEvent(new Event('input'))
                this.$nextTick(() => form.requestSubmit())
            },

            async tryVerify(form) {
                if (this.loading || this.verified) return

                this.loading = true

                try {
                    await window.FilamentPasskeys.verify({ routes: this.routes() })
                    this.markVerifiedAndSubmit(form)
                } catch (e) {
                    const cancelled = e.name === 'UserCancelledError'
                        || e.constructor?.name === 'UserCancelledError'
                        || e.message?.includes('abort')

                    if (cancelled) return

                    console.error('[filament-passkeys] verify failed', e)

                    new window.FilamentNotification()
                        .title(@js(__('filament-passkeys::passkeys.challenge.failed')))
                        .danger()
                        .send()
                } finally {
                    this.loading = false
                }
            },

            handleSubmit(event) {
                if (this.verified) return

                event.preventDefault()
                event.stopImmediatePropagation()

                this.tryVerify(event.target)
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

        <input type="hidden" x-ref="credential" wire:model="{{ $statePath }}" />

        <template x-if="supported">
            <div>
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
