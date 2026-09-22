@if(Auth::check() && !Auth::user()->hasAnyRole(['nurse', 'patient']))
    @php
        $globalRisk = app(\App\Services\RiskPredictionService::class)->getGlobalRiskStatus();
    @endphp
    <button type="button"
            @click="$dispatch('open-risk-engine')"
            {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {$globalRisk['badge_class']} text-xs font-semibold shadow-2xs transition cursor-pointer shrink-0"]) }}
            title="{{ $globalRisk['pill_title'] }}">
        <span class="w-1.5 h-1.5 rounded-full {{ $globalRisk['dot_class'] }}"></span>
        <span class="whitespace-nowrap">{{ $globalRisk['label'] }}</span>
    </button>
@endif
