<div>
    @if($state = $getState())
        <a href="/admin/orders?tableFilters[user][value]={{ $state[0]->user_id }}" target="_blank">{{ count($state) ?? '-' }}</a>
    @endif
</div>
