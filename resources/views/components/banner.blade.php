@props([
  'style' => session('flash.bannerStyle', 'success'),
  'message' => session('flash.banner'),
])

<div
    x-data="{ show: true, style: @js($style), message: @js($message) }"
    x-cloak
    x-show="show && message"
    x-on:banner-message.window="
    style = event.detail.style;
    message = event.detail.message;
    show = true;
  "
    class="w-100"
>
    <div
        class="alert mb-0 rounded-0"
        role="alert"
        :class="{
      'alert-success': style === 'success',
      'alert-danger':  style === 'danger',
      'alert-warning': style === 'warning',
      'alert-secondary': style !== 'success' && style !== 'danger' && style !== 'warning'
    }"
    >
        <div class="container py-2 d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
        <span class="d-inline-flex align-items-center justify-content-center">
          <wa-icon x-show="style === 'success'" name="check-circle"></wa-icon>
          <wa-icon x-show="style === 'danger'"  name="triangle-exclamation"></wa-icon>
          <wa-icon x-show="style === 'warning'" name="exclamation-circle"></wa-icon>
          <wa-icon x-show="style !== 'success' && style !== 'danger' && style !== 'warning'" name="circle-info"></wa-icon>
        </span>

                <p class="mb-0 text-truncate" x-text="message"></p>
            </div>

            <button type="button" class="btn btn-link p-0 text-decoration-none" aria-label="Dismiss" @click="show = false">
                <wa-icon name="xmark"></wa-icon>
            </button>
        </div>
    </div>
</div>
