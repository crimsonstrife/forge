<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold">{{ __('Profile Information') }}</div>
            <div class="text-body-secondary small">{{ __('Update your account\'s profile information and email address.') }}</div>
        </div>

        <form wire:submit.prevent="updateProfileInformation" class="row g-3">
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <div class="col-12">
                    <label class="form-label">{{ __('Photo') }}</label>

                    <div x-data="{photoName: null, photoPreview: null}">
                        <input type="file" id="photo" class="d-none"
                               wire:model.live="photo"
                               x-ref="photo"
                               x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result };
                                    reader.readAsDataURL($refs.photo.files[0]);
                               ">

                        <div class="d-flex align-items-center gap-3">
                            {{-- Current --}}
                            <div x-show="!photoPreview">
                                <x-avatar :src="$this->user->profile_photo_url" :name="$this->user->name" preset="xl" />
                            </div>

                            {{-- Preview --}}
                            <div x-show="photoPreview" style="display:none">
                                <span class="d-inline-block rounded-circle"
                                      x-bind:style="'background-image:url('+photoPreview+');width:80px;height:80px;background-size:cover;background-position:center'"></span>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        x-on:click.prevent="$refs.photo.click()">
                                    {{ __('Select A New Photo') }}
                                </button>

                                @if ($this->user->profile_photo_path)
                                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="deleteProfilePhoto">
                                        {{ __('Remove Photo') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif

            {{-- Name --}}
            <div class="col-12 col-sm-6">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" type="text" class="form-control @error('state.name') is-invalid @enderror"
                       wire:model="state.name" required autocomplete="name">
                @error('state.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="col-12 col-sm-6">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" type="email" class="form-control @error('state.email') is-invalid @enderror"
                       wire:model="state.email" required autocomplete="username">
                @error('state.email') <div class="invalid-feedback">{{ $message }}</div> @enderror

                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                    <p class="small mt-2 mb-0">
                        {{ __('Your email address is unverified.') }}
                        <button type="button" class="btn btn-link btn-sm p-0 align-baseline"
                                wire:click.prevent="sendEmailVerification">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if ($this->verificationLinkSent)
                        <p class="small text-success mt-1 mb-0">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                @endif
            </div>

            <div class="col-12 d-flex align-items-center gap-2">
                <x-action-message class="text-success small" on="saved">
                    {{ __('Saved.') }}
                </x-action-message>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="photo">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>
