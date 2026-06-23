@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'bx-bar-chart',
    'iconVariant' => 'primary',
    'progress' => null,
    'progressVariant' => 'primary',
])

<div {{ $attributes->merge(['class' => 'card stat-card']) }}>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <span class="stat-card-label">{{ $label }}</span>
            <span class="stat-card-icon stat-card-icon--{{ $iconVariant }}">
                <i class="bx {{ $icon }}"></i>
            </span>
        </div>
        <h3 class="stat-card-value mb-1">{{ $value }}</h3>
        @if ($hint)
            <p class="stat-card-hint mb-0">{!! $hint !!}</p>
        @endif
        @if ($progress !== null)
            <div class="stat-card-progress mt-3">
                <div class="progress stat-card-progress-bar">
                    <div class="progress-bar bg-{{ $progressVariant }}" role="progressbar"
                         style="width: {{ min(100, max(0, (float) $progress)) }}%"
                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        @endif
    </div>
</div>
