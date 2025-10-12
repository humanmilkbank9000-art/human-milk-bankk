<?php

namespace App\Services;

use App\Models\HealthScreening;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HealthScreeningService
{
    public function create(array $data, int $userId)
    {
        if (HealthScreening::where('user_id', $userId)->exists()) {
            throw new \RuntimeException('You have already submitted your health screening.');
        }

        $payload = array_merge($data, [
            'user_id' => $userId,
            'status' => 'pending',
            'date_accepted' => null,
            'date_declined' => null,
        ]);

        $screening = HealthScreening::create($payload);

        // Notify admins
        $this->notifyAdmins('New Health Screening', 'A user submitted a health screening for review.');

        return $screening;
    }

    public function accept(HealthScreening $screening, ?string $comments = null)
    {
        $screening->status = 'accepted';
        $screening->date_accepted = now();
        if (!empty($comments)) {
            $screening->admin_notes = $comments;
        }
        $screening->save();

        $user = $screening->user;
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Health Screening Accepted', 'Your health screening has been accepted. You may now donate.'));
        }

        return $screening;
    }

    public function reject(HealthScreening $screening, ?string $comments = null)
    {
        $screening->status = 'declined';
        $screening->date_declined = now();
        if (!empty($comments)) {
            $screening->admin_notes = $comments;
        }
        $screening->save();

        $user = $screening->user;
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Health Screening Declined', 'Your health screening has been declined.'));
        }

        return $screening;
    }

    public function undoDecline(HealthScreening $screening, ?string $comments = null)
    {
        if ($screening->status !== 'declined') {
            throw new \RuntimeException('Only declined screenings can be undone.');
        }

        $screening->status = 'accepted';
        $screening->date_accepted = now();
        $screening->date_declined = null;
        if (!empty($comments)) {
            $screening->admin_notes = $comments;
        }
        $screening->save();

        $user = $screening->user;
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Health Screening Accepted', 'Your previously declined health screening has been reviewed and accepted. You may now donate.'));
        }

        return $screening;
    }

    protected function notifyAdmins(string $title, string $message)
    {
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemAlert($title, $message));
        }
    }
}
