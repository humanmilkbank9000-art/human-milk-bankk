@php
    $accountId = session('account_id');
    $accountRole = session('account_role', 'user');
@endphp

<style>
    .notification-item {
        transition: background-color 0.2s ease;
    }

    .notification-item[style*="cursor: pointer"]:hover {
        background-color: #f8f9fa !important;
    }

    .notification-item[style*="cursor: pointer"]:active {
        background-color: #e9ecef !important;
    }

    #notificationDropdown {
        box-sizing: border-box;
    }

    #notificationDropdown * {
        box-sizing: border-box;
    }

    /* Ensure notification content wraps properly on all screen sizes */
    @media (max-width: 576px) {
        #notificationDropdown {
            width: 90vw !important;
            max-width: 90vw !important;
        }
    }

    @media (max-width: 400px) {
        #notificationDropdown {
            width: 95vw !important;
            max-width: 95vw !important;
        }
    }
</style>

<div class="dropdown notification-bell" style="position:relative;" data-notifiable-id="{{ $accountId }}"
    data-notifiable-role="{{ $accountRole }}">
    <button class="btn btn-light position-relative" id="notificationBellBtn" data-bs-toggle="dropdown"
        aria-expanded="false" title="Notifications" style="height: 40px; min-width: 48px; padding: 6px 16px;">
        <i class="bi bi-bell" style="font-size:1.1rem"></i>
        <span id="notification-badge" class="badge bg-danger position-absolute"
            style="top:-4px;right:-8px;display:none;visibility:hidden;min-width:20px;height:20px;border-radius:10px;font-size:0.75rem;line-height:20px;padding:0 5px;"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-2" id="notificationDropdown"
        style="width:320px; max-width:90vw; max-height:420px; overflow-y:auto; overflow-x:hidden;">
        <div class="d-flex justify-content-between align-items-center px-2 mb-2">
            <strong>Notifications</strong>
            <button class="btn btn-sm btn-link" id="markAllReadBtn">Mark all as read</button>
        </div>
        <div id="notificationList">
            <div class="text-center text-muted py-3">Loading...</div>
        </div>
        <div class="border-top mt-2 pt-2 text-center">
            <a href="#" id="viewAllNotifications" class="small">View all</a>
        </div>
    </div>
</div>

<script>
    async function fetchUnreadCount() {
        try {
            const res = await fetch('{{ route('notifications.unread_count') }}');
            const data = await res.json();
            const badge = document.getElementById('notification-badge');
            if (data.count && data.count > 0) {
                badge.style.display = 'inline-block';
                badge.style.visibility = 'visible';
                badge.textContent = data.count;
            } else {
                badge.style.display = 'none';
                badge.style.visibility = 'hidden';
                badge.textContent = '';
            }
        } catch (e) {
            console.error('Failed to fetch unread count', e);
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.style.display = 'none';
                badge.style.visibility = 'hidden';
                badge.textContent = '';
            }
        }
    }

    async function fetchNotifications(perPage = 10) {
        const list = document.getElementById('notificationList');
        list.innerHTML = '<div class="text-center text-muted py-3">Loading...</div>';
        try {
            const res = await fetch(`{{ route('notifications.index') }}?per_page=${perPage}`);
            const data = await res.json();
            if (!data || !data.data || data.data.length === 0) {
                list.innerHTML = '<div class="text-center text-muted py-3">No new notifications</div>';
                return;
            }

            const accountRole = '{{ $accountRole }}';

            const nodes = data.data.map(n => {
                const time = new Date(n.created_at).toLocaleString();
                const readClass = n.is_read ? 'text-muted' : 'fw-bold';
                const payload = n.data || n.data;
                const title = payload.title || payload['title'] || '';
                const message = payload.message || payload['message'] || '';
                const type = payload.type || 'info';
                const icon = type === 'success' ? 'bi-check-circle-fill text-success' : (type === 'warning' ? 'bi-exclamation-triangle-fill text-warning' : 'bi-info-circle-fill text-primary');

                // Determine redirect URL based on notification title and role
                const redirectUrl = getNotificationRedirectUrl(title, accountRole);
                const cursorStyle = redirectUrl ? 'cursor: pointer;' : '';
                const clickHandler = redirectUrl ? `onclick="handleNotificationClick('${n.id}', '${redirectUrl}')"` : '';

                return `
                    <div class="d-flex gap-2 align-items-start p-2 border-bottom notification-item" 
                         style="${cursorStyle}" ${clickHandler}>
                        <i class="bi ${icon} me-2" style="font-size:1.1rem; flex-shrink:0;"></i>
                        <div style="flex:1; min-width:0;">
                            <div class="${readClass}" style="font-size:0.9rem;">${title}</div>
                            <div class="small text-muted" style="word-wrap:break-word; overflow-wrap:break-word; line-height:1.4;">${message}</div>
                            <div class="small text-muted" style="font-size:0.75rem; margin-top:2px;">${time}</div>
                        </div>
                    </div>
                `;
            }).join('');

            list.innerHTML = nodes;

        } catch (e) {
            console.error('Failed to fetch notifications', e);
            list.innerHTML = '<div class="text-center text-danger py-3">Failed to load notifications</div>';
        }
    }

    // Function to determine redirect URL based on notification title and role
    function getNotificationRedirectUrl(title, role) {
        const titleLower = title.toLowerCase();

        if (role === 'admin') {
            // Admin notifications
            if (titleLower.includes('health screening')) {
                return '{{ route('admin.health-screening') }}';
            } else if (titleLower.includes('donation') && (titleLower.includes('walk-in') || titleLower.includes('home collection'))) {
                return '{{ route('admin.donation') }}';
            } else if (titleLower.includes('breastmilk request')) {
                return '{{ route('admin.request') }}';
            }
        } else {
            // User notifications
            if (titleLower.includes('health screening')) {
                return '{{ route('user.health-screening') }}';
            } else if (titleLower.includes('pickup validated') || titleLower.includes('donation validated')) {
                return '{{ route('user.history') }}';
            } else if (titleLower.includes('donation') || titleLower.includes('pickup')) {
                return '{{ route('user.pending') }}';
            } else if (titleLower.includes('request')) {
                return '{{ route('user.my-requests') }}';
            }
        }

        return null; // No redirect for unknown notification types
    }

    // Handle notification click
    async function handleNotificationClick(notificationId, redirectUrl) {
        // Mark as read
        await fetch(`{{ url('/notifications') }}/${notificationId}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        // Redirect to appropriate page
        window.location.href = redirectUrl;
    }

    document.addEventListener('DOMContentLoaded', function () {
        fetchUnreadCount();

        const bell = document.getElementById('notificationBellBtn');
        if (bell) {
            bell.addEventListener('click', function () {
                // when opening dropdown, fetch list
                setTimeout(fetchNotifications, 150);
            });
        }

        const markAll = document.getElementById('markAllReadBtn');
        if (markAll) {
            markAll.addEventListener('click', async function () {
                await fetch('{{ url('/notifications/read-all') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                fetchUnreadCount();
                const dropdown = document.getElementById('notificationDropdown');
                const isExpanded = dropdown.style.maxHeight === '80vh';
                fetchNotifications(isExpanded ? 50 : 10);
            });
        }

        // View all notifications - expand dropdown vertically
        const viewAllBtn = document.getElementById('viewAllNotifications');
        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const dropdown = document.getElementById('notificationDropdown');

                if (dropdown.style.maxHeight === '80vh') {
                    // Collapse back to normal
                    dropdown.style.maxHeight = '420px';
                    this.textContent = 'View all';
                    fetchNotifications(10);
                } else {
                    // Expand to show more
                    dropdown.style.maxHeight = '80vh';
                    this.textContent = 'View less';
                    fetchNotifications(50);
                }
            });
        }

        // Poll unread count every 30 seconds
        setInterval(fetchUnreadCount, 30000);

        // Real-time: subscribe to Echo channel for this notifiable (if Echo is loaded)
        try {
            const notifiableId = document.querySelector('.notification-bell').dataset.notifiableId;
            const notifiableRole = document.querySelector('.notification-bell').dataset.notifiableRole;
            if (window.Echo && notifiableId) {
                const channelName = notifiableRole === 'admin' ? `private-App.Models.Admin.${notifiableId}` : `private-App.Models.User.${notifiableId}`;
                window.Echo.private(channelName)
                    .listen('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (e) => {
                        // e.notification contains the broadcast payload
                        fetchUnreadCount();
                        // optionally prepend to list if dropdown is open
                        const dropdown = document.querySelector('.dropdown.notification-bell .show');
                        if (dropdown) {
                            const dropdownElement = document.getElementById('notificationDropdown');
                            const isExpanded = dropdownElement.style.maxHeight === '80vh';
                            fetchNotifications(isExpanded ? 50 : 10);
                        }
                    });
            }
        } catch (err) {
            console.warn('Echo not configured or failed to subscribe', err);
        }
    });
</script>