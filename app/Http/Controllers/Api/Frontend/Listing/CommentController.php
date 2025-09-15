<?php

namespace App\Http\Controllers\Api\Frontend\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Listing\CommentRequest;
use App\Http\Resources\Listing\CommentResource;
use App\Http\Resources\Listing\ReplyResource;
use App\Http\Responses\ApiResponse;
use App\Models\Backend\Comment;
use App\Models\Backend\Listing;
use App\Models\Backend\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CommentController extends Controller
{
    public function store(Listing $listing, CommentRequest $request)
    {
        Log::info($request->all());

        $comment = $listing->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request['content']
        ]);

        return ApiResponse::success('Comment added', new CommentResource($comment));
    }

    public function destroyComment($listing, Comment $comment)
    {
        $comment->delete();
        return ApiResponse::success('Comment deleted');
    }

    public function editComment(Listing $listing, Comment $comment, CommentRequest $request)
    {
        $comment->update([
            'content' => $request->content
        ]);

        return ApiResponse::success('Comment Edited', new CommentResource($comment->refresh()));
    }

    public function getComment(Comment $comment)
    {
        return ApiResponse::success('Comment retrieved', new CommentResource($comment));
    }

    public function storeReply($listing,Comment $comment, CommentRequest $request)
    {
        $reply = $comment->replies()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        return ApiResponse::success('Reply added', new ReplyResource($reply));
    }

    public function destroyReply(Listing $listing, Comment $comment, Reply $reply, Request $request)
    {
        $reply->delete();
        return ApiResponse::success('Reply deleted');
    }

    public function editReply(Listing $listing, Comment $comment, Reply $reply, CommentRequest $request)
    {
        $reply->update([
            'content' => $request->content,
        ]);

        return ApiResponse::success('Reply Edited', new ReplyResource($reply->refresh()));
    }


    public function getReply(Comment $comment, Comment $reply)
    {
        return ApiResponse::success('Reply retrieved', new ReplyResource($reply));
    }

}
