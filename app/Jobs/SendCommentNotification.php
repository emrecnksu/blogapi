<?php

namespace App\Jobs;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Spatie\Permission\Models\Role;
use App\Mail\NewCommentNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendCommentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function handle()
    {
        // Get email addresses of users with super admin role //
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $superAdmins = $superAdminRole ? $superAdminRole->users : collect();

        foreach ($superAdmins as $admin) {
            Mail::to($admin->email)->send(new NewCommentNotification($this->comment));
        }
    }
}

