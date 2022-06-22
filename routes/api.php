<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function (): void {
    Route::post('/register', 'Auth\AuthController@register')->name('register');
    Route::post('/login', 'Auth\AuthController@login')->name('login');
    Route::post('/refresh', 'Auth\AuthController@refresh')->name('refresh');
    Route::post('/logout', 'Auth\AuthController@logout')->name('logout');

    Route::group(['prefix' => 'active', 'as' => 'active.'], function(): void {
        Route::post('/email', 'Auth\ActiveController@generateActiveToken')->name('email');
        Route::put('/activate', 'Auth\ActiveController@userActive')->name('activate');
    });

    Route::group(['prefix' => 'check', 'as' => 'check.'], function(): void {
        // Route::get('/email', 'Auth\CheckEmailController@checkEmail')->name('email');
        Route::post('/email_code', 'Auth\CheckEmailController@checkEmailCode')->name('email_code');
    });

    Route::group(['prefix' => 'restore_password', 'as' => 'restore_password.'], function(): void {
        Route::post('/restore', 'Auth\RestorePasswordController@generateResetPassword')->name('restore');
        Route::put('/restore', 'Auth\RestorePasswordController@storeNewPassword')->name('restore');
    });

    Route::group(['prefix' => 'socials'], function (): void {
        Route::post('/apple', 'AppleAuthController@handle');
        Route::post('/google', 'GoogleAuthController@handle');
    });
});

Route::group(['middleware' => ['auth:api']], function (): void {
    Route::get('/header_data', 'HeaderController');
    Route::get('/countries', 'CountryController@index');
    Route::get('/countries/{id}', 'CountryController@show');

    Route::get('/feed/{user_id?}', 'FeedController@index');
    Route::get('/feed/search', 'FeedController@search');

    Route::group(['prefix' => 'tags'], function (): void {
        Route::get('/top', 'TopTagPostController@index');
        Route::get('/top/list', 'TopTagPostController@list');

        Route::get('/hot-today', 'HotTodayTagPostController@index');
        Route::get('/hot-today/list', 'HotTodayTagPostController@list');
    });

    Route::apiResource('/posts', 'PostController');
    Route::get('/posts/{post_id}/share', 'PostController@share');
    Route::post('/posts/{post_id}/share/timeline', 'PostShareController@shareOnTimeline');
    Route::post('/posts/{post_id}/like','PostLikeController@likeToggle');
    Route::get('/posts/{post_id}/liked_users', 'PostLikeController@getLikedUsers');
    Route::post('/posts/{post_id}/pin', 'PostActionController@pinToggle');
    Route::post('/posts/{post_id}/hide', 'PostHideController@hideToggle');

    Route::get('/follow/popular', 'FollowController@popular');
    Route::post('/follow/{user_id}', 'FollowController@followToggle');
    Route::get('/follow/search/{user_id?}', 'FollowController@search');

    Route::get('/chats/{id}/users', 'GroupChatActionController@getUsersFromUserChat');
    Route::post('/chats/{id}/users', 'GroupChatActionController@addUserInGroupChat');
    Route::delete('/chats/{id}/user', 'GroupChatActionController@removeUserFromChatUser');
    Route::apiResource('chats', 'GroupChatController');
    Route::get('/search', 'SearchController@search');

    Route::apiResource('post.comments', 'PostCommentController')->middleware('auth.active');
    Route::post('/posts/{post_id}/report','PostReportController@report');
    Route::post('/posts/{post_id}/report/withdraw','PostReportController@withdraw');
    Route::post('/post/{post_id}/comments/{comment_id}/report','PostCommentReportController@reportComment');
    Route::post('/post/{post_id}/comments/{comment_id}/report/withdraw','PostCommentReportController@withdrawCommentReport');
    Route::post('/post/{post_id}/comment/{comment_id}/like','PostCommentLikeController@toggleLike');
    Route::group(['prefix' => 'post', 'as' => 'post.'], function (): void {
        Route::apiResource('/comment.replies', 'CommentReplyController');
    });
    Route::post('/post/comment/{comment_id}/replies/{reply_id}/like','CommentReplyLikeController@toggleLike');

    Route::apiResource('/messages', 'MessageController');

    // Groups
    Route::post('/group/{group_id}/invite', 'GroupActionController@invite');
    Route::get('/user/groups/{user_id?}', 'GroupActionController@userCreatedGroups');
    Route::get('/group/{group_id}/members', 'GroupActionController@members');
    Route::get('/groups', 'GroupActionController@index');
    Route::apiResource('/groups', 'GroupController', ['except' => 'index']);
    Route::apiResource('/group.admins', 'GroupAdminController');
    Route::apiResource('/group.posts', 'GroupPostController');
    Route::get('/group/{group_id}/requests', 'GroupJoinController@getRequests');
    Route::post('/group/{group_id}/requests/{user_id}', 'GroupJoinController@requestAction');
    Route::post('/group/{group_id}/joins', 'GroupJoinController@joinToggle');
    Route::put('/group/{group_id}/settings', 'GroupAdditionalDataController@update');
    Route::get('/group/{group_id}/settings', 'GroupAdditionalDataController@show');
    Route::get('/group/suggestions', 'GroupActionController@suggestions');
    Route::get('/group/joined/{user_id?}', 'GroupActionController@joined');
    Route::get('/group/search', 'GroupActionController@search');
    Route::put('/group/{group_id}/medias', 'GroupMediaController');

    Route::group(['prefix' => 'group', 'as' => 'group.'], function (): void {
        Route::apiResource('/messages', 'GroupMessageController');
        Route::apiResource('/categories', 'GroupCategoryController');
    });

    Route::get('/location', 'LocationController@searchForLocation');
});

Route::resource('/files/uploads', 'FileController', ['except' => ['index', 'update']]);
Route::post('/files/uploads/{uuid}', 'FileController@update');

Route::group(['prefix' => 'blockdesk'], function (): void {
    Route::get('/trending/block', 'BlockdeskBlockController@trendingBlock');
    Route::get('/popular/block', 'BlockdeskBlockController@popularBlock');
    Route::get('/editors-choice/block', 'BlockdeskBlockController@editorsChoiceBlock');
    Route::get('/latest/block', 'BlockdeskBlockController@latestBlock');
    Route::apiResource('/{blog_id}/comments', 'BlockdeskBlockCommentController');
    Route::apiResource('/{blog_id}/comments/{comment_id}/replies', 'BlockdeskBlockCommentReplyController');
    Route::get('/my-articles', 'BlockdeskArticleController@getMyArticles');
    Route::get('/save', 'BlockdeskBookmarkController@getBookmarks');
    Route::post('/{blog_id}/save', 'BlockdeskBookmarkController@toggleBookmark');

    Route::post('/{blog_id}/comments/{comment_id}/likes', 'BlockdeskCommentLikeController@toggleLike');
    Route::post('/comments/replies/{reply_comment_id}/likes', 'BlockdeskCommentReplyLikeController@toggleLike');

    Route::post('/{article_id}/comments/{comment_id}/reports','BlockdeskCommentReportController@reportComment');
    Route::post('/{article_id}/comments/{comment_id}/reports/withdraw','BlockdeskCommentReportController@withdrawCommentReport');
    Route::get('/article/categories', 'BlockdeskArticleCategoryController@index');
    Route::get('/article/following', 'BlockdeskArticleFollowingController@index');

    Route::get('/search', 'BlockdeskSearchController');
    Route::post('/{blog_id}/likes', 'BlockdeskLikeController@toggleLike');

    Route::get('/{article_id}/share', 'BlockdeskArticleController@share');
});

Route::apiResource('/blockdesk', 'BlockdeskArticleController');

// Form URL Encoded
Route::group(['prefix' => 'users', 'middleware' => ['auth:api']], function (): void {
    Route::get('/me', 'UserController@index');
    Route::get('/me/privacy', 'UserController@getPrivacy');
    Route::put('/me/privacy', 'UserController@updatePrivacy');
    Route::put('/me', 'UserController@update');
    Route::post('/me/devices', 'FCMNotificationController@saveDeviceToken');
    Route::apiResource('/me/sessions', 'SessionController', ['only' =>['index', 'destroy']]);
    Route::get('/me/notifications', 'UserNotificationsSettingsController@getUserNotificationsSettings');
    Route::put('/me/notifications', 'UserNotificationsSettingsController@setUserNotificationsSettings');

    Route::group(['prefix' => 'me/change-password'], function(): void {
        Route::post('/', 'Auth\ChangePasswordController@requestPasswordChange');
        Route::post('/verify', 'Auth\ChangePasswordController@verifyPasswordChange');
    });

    Route::post('/fcm_send/{user_id?}', 'FCMNotificationController@send');

    Route::get('/{user_id}', 'UserController@show');
    Route::get('/followers', 'FollowController@getUserFollowers');
    Route::get('/{user_id}/followers', 'FollowController@getFollowers');
    Route::get('/{user_id}/followers/search', 'FollowController@getFollowersSearch');
    // TODO: choose and use everywhere one name from variations used right now: followed & followings
    Route::get('/followings', 'FollowController@getUserFollowings');
    Route::get('/{user_id}/followings', 'FollowController@getFollowings');
    Route::get('/{user_id}/followings/search', 'FollowController@getFollowingsSearch');
    Route::get('/following-requests', 'FollowController@getFollowingRequests');
    Route::get('/following-requests/{follower_id}/accept', 'FollowController@acceptFollowingRequest');
    Route::get('/following-requests/{follower_id}/decline', 'FollowController@declineFollowingRequest');
    Route::get('/follow/discover', 'FollowController@getFollowRecommendations');
    Route::get('/{user_id}/chats', 'UserController@getChats');
    Route::get('/timeline', 'UserController@timeline');
    Route::get('/timeline/pinned', 'UserController@timelinePinned');
    Route::get('/{user_id}/timeline', 'UserController@timeline');
    Route::get('/timeline/search', 'UserTimelineSearchController@search');
    Route::get('/{user_id}/timeline/search', 'UserTimelineSearchController@search');

    Route::middleware(['auth.permission'])->group(function (): void {
        Route::patch('/{user_id}', 'UserController@update');
        Route::delete('/{user_id}', 'UserController@destroy');
    });
});

Route::get('/ping', function (): \Illuminate\Http\JsonResponse {
    return response()->json(['status' => 'ok'], 200);
});

Route::fallback(function(): \Illuminate\Http\JsonResponse {
    return response()->json(['error' => 'Not found'], 404);
});
