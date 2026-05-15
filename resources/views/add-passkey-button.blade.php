@php
    $panelId = filament()->getId();
    $optionsUrl = route("filament.{$panelId}.passkeys.register.options");
    $submitUrl = route("filament.{$panelId}.passkeys.register");
@endphp

<div
    x-data="{
        loading: false,
        supported: true,

        init() {
            this.supported = window.FilamentPasskeys?.isSupported() ?? false
        },

        async register() {
            if (this.loading) return

            this.loading = true

            try {
                await window.FilamentPasskeys.register({
                    name: @js(__('filament-passkeys::passkeys.management.default_name')),
                    routes: {
                        options: @js($optionsUrl),
                        submit: @js($submitUrl),
                    },
                })

                $wire.$refresh()

                new window.FilamentNotification()
                    .title(@js(__('filament-passkeys::passkeys.management.registered')))
                    .success()
                    .send()
            } catch (e) {
                if (e.name === 'UserCancelledError' || e.constructor?.name === 'UserCancelledError') {
                    return
                }

                new window.FilamentNotification()
                    .title(e.message || @js(__('filament-passkeys::passkeys.challenge.failed')))
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
        <x-filament::link
            tag="button"
            type="button"
            icon="heroicon-m-plus"
            x-on:click="register()"
            x-bind:disabled="loading"
        >
            <span x-show="!loading">{{ __('filament-passkeys::passkeys.management.add') }}</span>
            <span x-show="loading" x-cloak>{{ __('filament-passkeys::passkeys.management.waiting') }}</span>
        </x-filament::link>
    </template>
</div>
