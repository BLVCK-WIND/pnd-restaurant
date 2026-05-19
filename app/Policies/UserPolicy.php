<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // ── GUEST actions ──────────────────────────────
    public function viewGuest(User $user, User $guest): bool
    {
        // $user  = người đang đăng nhập (admin)
        // $guest = người được xem
        // Chỉ cho xem nếu target là guest
        return $guest->role === 'guest';
    }

    public function deleteGuest(User $user, User $guest): bool
    {
        // Chỉ xoá được user có role guest
        return $guest->role === 'guest';
    }

    public function toggleGuest(User $user, User $guest): bool
    {
        // Chỉ toggle được user có role guest
        return $guest->role === 'guest';
    }

    // ── STAFF actions ──────────────────────────────
    public function viewStaff(User $user, User $staff): bool
    {
        // Target phải là admin hoặc staff
        return in_array($staff->role, ['admin', 'staff']);
    }

    public function updateStaff(User $user, User $staff): bool
    {
        // Target phải là admin hoặc staff
        return in_array($staff->role, ['admin', 'staff']);
    }

    public function deleteStaff(User $user, User $staff): bool
    {
        // Target phải là admin hoặc staff
        // VÀ không được xoá chính mình!
        return in_array($staff->role, ['admin', 'staff'])
            && $user->id !== $staff->id;
    }

    public function toggleStaff(User $user, User $staff): bool
    {
        // Target phải là admin hoặc staff
        // VÀ không được khoá chính mình!
        return in_array($staff->role, ['admin', 'staff'])
            && $user->id !== $staff->id;
    }
}