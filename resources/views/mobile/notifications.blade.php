@extends('mobile.layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h5 style="font-size: 1rem; font-weight: 700; margin: 0; color: #333;">
      <i class="bx bx-bell"></i> Notifikasi
    </h5>
    @if($unreadCount > 0)
      <button type="button" 
              onclick="markAllAsRead()"
              style="background: #147440; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; cursor: pointer;">
        Tandai Semua Dibaca
      </button>
    @endif
  </div>
</div>

@if($notifications->count() > 0)
  <div style="background: white; padding: 0.5rem 0;">
    @foreach($notifications as $notification)
      <div class="notification-item" 
           data-id="{{ $notification->id }}"
           onclick="openNotification('{{ $notification->action_url ?? '#' }}', {{ $notification->id }})"
           style="display: flex; gap: 1rem; padding: 1rem; border-bottom: 1px solid #f0f0f0; cursor: pointer; background: {{ $notification->is_read ? 'white' : '#f8f9fa' }};">
        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <i class="bx {{ $notification->icon ?? 'bx-bell' }}" style="color: white; font-size: 1.25rem;"></i>
        </div>
        <div style="flex: 1;">
          <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.25rem;">
            <h6 style="font-size: 0.875rem; font-weight: 600; margin: 0; color: #333;">
              {{ $notification->title }}
            </h6>
            @if(!$notification->is_read)
              <span style="width: 8px; height: 8px; background: #147440; border-radius: 50%; flex-shrink: 0; margin-top: 0.25rem;"></span>
            @endif
          </div>
          <p style="font-size: 0.75rem; color: #666; margin: 0.25rem 0 0 0; line-height: 1.5;">
            {{ $notification->message }}
          </p>
          <div style="font-size: 0.7rem; color: #999; margin-top: 0.5rem;">
            {{ $notification->created_at->diffForHumans() }}
          </div>
        </div>
      </div>
    @endforeach
  </div>
  
  <!-- Pagination -->
  <div style="padding: 1rem; text-align: center;">
    {{ $notifications->links() }}
  </div>
@else
  <div class="empty-state">
    <i class="bx bx-bell-off" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
    <p>Tidak ada notifikasi</p>
  </div>
@endif
@endsection

@push('scripts')
<script>
  function openNotification(url, notificationId) {
    // Mark as read
    if (notificationId) {
      fetch(`{{ route('mobile.notifications.read', '') }}/${notificationId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update badge
          updateNotificationBadge(data.unread_count);
          // Update item style
          const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
          if (item) {
            item.style.background = 'white';
            const dot = item.querySelector('span[style*="background: #147440"]');
            if (dot) dot.remove();
          }
        }
      });
    }
    
    // Navigate to URL
    if (url && url !== '#') {
      window.location.href = url;
    }
  }
  
  function markAllAsRead() {
    fetch('{{ route("mobile.notifications.read-all") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        MobileNotification.success('Semua notifikasi ditandai sebagai sudah dibaca');
        // Reload page
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      }
    });
  }
  
  function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
      if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'flex';
      } else {
        badge.style.display = 'none';
      }
    }
  }
  
  // Load unread count on page load
  @auth
    fetch('{{ route("api.notifications.unread-count") }}')
      .then(response => response.json())
      .then(data => {
        updateNotificationBadge(data.count);
      });
  @endauth
</script>
@endpush
