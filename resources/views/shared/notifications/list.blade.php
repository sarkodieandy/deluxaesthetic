@php
    $markReadRoute = $markReadRoute ?? null;
    $markAllRoute = $markAllRoute ?? null;
@endphp

@if(($unreadCount ?? 0) > 0 && $markAllRoute)
    <form method="POST" action="{{ route($markAllRoute) }}" class="notif-list__toolbar">
        @csrf
        <button type="submit" class="{{ $actionClass ?? 'student-action' }}">Mark all as read ({{ $unreadCount }})</button>
    </form>
@endif

<div class="{{ $panelClass ?? 'student-panel' }} notif-list">
    @forelse($notifications as $notification)
        @php
            $title = $notification->data['title'] ?? class_basename($notification->type);
            $message = $notification->data['message'] ?? '';
            $isUnread = is_null($notification->read_at);
        @endphp
        <article @class(['notif-item', 'notif-item--unread' => $isUnread])>
            <div class="notif-item__icon" aria-hidden="true">
                @include('shared.notifications.bell-icon')
                @if($isUnread)
                    <span class="notif-item__dot"></span>
                @endif
            </div>
            <div class="notif-item__body">
                <p class="notif-item__title">
                    @if($isUnread)
                        <span class="notif-item__badge">New</span>
                    @endif
                    {{ $title }}
                </p>
                @if($message)
                    <p class="notif-item__message">{{ $message }}</p>
                @endif
                <p class="notif-item__time">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @if($markReadRoute)
                <form method="POST" action="{{ route($markReadRoute, $notification->id) }}" class="notif-item__action">
                    @csrf
                    <button type="submit" class="{{ $actionClass ?? 'student-action' }}">
                        {{ $isUnread ? 'Mark read' : 'Viewed' }}
                    </button>
                </form>
            @endif
        </article>
    @empty
        <p class="notif-list__empty">No notifications yet.</p>
    @endforelse

    <div class="notif-list__pagination">{{ $notifications->links() }}</div>
</div>
