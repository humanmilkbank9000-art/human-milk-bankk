@php
    $modalId = $id ?? null;
    $labelId = $modalId ? ($modalId . 'Label') : null;
@endphp
<div class="modal fade shared-modal" @if($modalId) id="{{ $modalId }}" @endif tabindex="-1" role="dialog"
    aria-hidden="true" @if($labelId) aria-labelledby="{{ $labelId }}" @endif>
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" @if($labelId) id="{{ $labelId }}" @endif>{!! $title ?? '' !!}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Body content injected by caller --}}
                @if(!empty($view))
                    @include($view, $viewData ?? [])
                @else
                    {!! $slot ?? '' !!}
                @endif
            </div>
            @unless(!empty($hideFooterButtons) && $hideFooterButtons)
                <div class="modal-footer">
                    @if(isset($secondary))
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ $secondary }}</button>
                    @endif
                    @if(isset($primary))
                        <button type="button" class="btn btn-primary">{{ $primary }}</button>
                    @endif
                </div>
            @endunless
        </div>
    </div>
</div>