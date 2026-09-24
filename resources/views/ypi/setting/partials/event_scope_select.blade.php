@php
    $scopeEvent = current_event();
@endphp

<div class="row">
    <div class="col mb-3">
        <label for="event_scope" class="form-label"><?= get_label('event_scope', 'Applies to') ?></label>
        <select class="form-select" id="event_scope" name="event_scope" @disabled(!$scopeEvent)>
            @if ($scopeEvent)
                <option value="current" selected>{{ $scopeEvent->name }} <?= get_label('only', 'only') ?></option>
            @endif
            <option value="global" @selected(!$scopeEvent)><?= get_label('all_events', 'All events') ?></option>
        </select>
        @unless ($scopeEvent)
            <input type="hidden" name="event_scope" value="global">
            <div class="form-text">
                <?= get_label('select_event_in_header_hint', 'Select an event in the header to make this entry event-specific.') ?>
            </div>
        @endunless
    </div>
</div>
