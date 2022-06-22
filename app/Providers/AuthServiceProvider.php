<?php

namespace App\Providers;

use App\Policies\AppSessionPolicy;
use App\Policies\BlogCommentPolicy;
use App\Policies\BlogCommentReplyPolicy;
use App\Policies\CommentReplyPolicy;
use App\Policies\Gates\Contracts\RegisterGateInterface;
use App\Policies\Gates\RegisterGateClasses;
use App\Policies\GroupAdminPolicy;
use App\Policies\GroupChatPolicy;
use App\Policies\GroupPolicy;
use App\Policies\MessagePolicy;
use App\Policies\SessionPolicy;
use App\Models\{AppSession,
    AuthRefreshToken,
    BlogComment,
    BlogCommentReply,
    Group,
    GroupAdmin,
    GroupChat,
    Message,
    User,
    Post,
    Comment};
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{

    /**
     * @var RegisterGateInterface $registerGate
     */
    private RegisterGateInterface $registerGate;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
        Message::class => MessagePolicy::class,
        GroupChat::class => GroupChatPolicy::class,
        Group::class => GroupPolicy::class,
        GroupAdmin::class => GroupAdminPolicy::class,
        BlogComment::class => BlogCommentPolicy::class,
        BlogCommentReply::class => BlogCommentReplyPolicy::class,
        CommentReplyPolicy::class => CommentReplyPolicy::class,
        AuthRefreshToken::class => SessionPolicy::class,
        AppSession::class => AppSessionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(RegisterGateClasses $registerGateClasses): void
    {
        $this->registerGate = $registerGateClasses;

        Gate::before(fn(User $user): ?bool => $user->isAdmin() ?: null);
        $this->registerPolicies();
        $this->registerGate->registerGates();

        //
    }
}
