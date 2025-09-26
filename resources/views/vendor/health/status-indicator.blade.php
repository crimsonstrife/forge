@php
    /** @var \Spatie\Health\Enums\Status|string $status */
    $status = is_string($result->status) ? $result->status : $result->status->value;

    $map = [
        'ok'       => ['bg' => 'bg-success-subtle border-success-subtle',  'icon' => ['family' => 'regular', 'name' => 'circle-check'],     'iconClass' => 'text-success'],
        'warning'  => ['bg' => 'bg-warning-subtle border-warning-subtle',  'icon' => ['family' => 'regular', 'name' => 'circle-exclamation'],'iconClass' => 'text-warning'],
        'failed'   => ['bg' => 'bg-danger-subtle border-danger-subtle',    'icon' => ['family' => 'regular', 'name' => 'circle-xmark'],      'iconClass' => 'text-danger'],
        'crashed'  => ['bg' => 'bg-danger-subtle border-danger-subtle',    'icon' => ['family' => 'regular', 'name' => 'triangle-exclamation'], 'iconClass' => 'text-danger'],
        'skipped'  => ['bg' => 'bg-secondary-subtle border-secondary-subtle', 'icon' => ['family' => 'regular', 'name' => 'circle-right'],  'iconClass' => 'text-secondary'],
        'unknown'  => ['bg' => 'bg-body-tertiary border-body-tertiary',    'icon' => ['family' => 'regular', 'name' => 'circle-question'],   'iconClass' => 'text-body-secondary'],
    ];

    $s = $map[$status] ?? $map['unknown'];
@endphp

<div class="d-inline-flex align-items-center justify-content-center rounded-circle border {{ $s['bg'] }} health-indicator"
     style="width: 40px; height: 40px;">
    <wa-icon
        family="{{ $s['icon']['family'] }}"
        name="{{ $s['icon']['name'] }}"
        class="{{ $s['iconClass'] }}"
        style="font-size: 20px;"
        aria-hidden="true">
    </wa-icon>
</div>
