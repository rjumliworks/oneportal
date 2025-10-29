<?php

namespace App\Http\Controllers\Portal;

use App\Traits\HandlesTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestTag;
use App\Models\RequestComment;
use App\Models\RequestCommentView;

class CommentController extends Controller
{
    use HandlesTransaction;

    public function store(Request $request){
        $result = $this->handleTransaction(function () use ($request) {
            if($request->option == 'reply'){
                $comment = RequestComment::create([
                    'user_id'          => auth()->id(),
                    'content'          => $request->content,
                    'commentable_id'   => $request->comment_id,
                    'commentable_type' => 'App\Models\RequestComment',
                ]);
            }else{
                $comment = RequestComment::create([
                    'user_id'          => auth()->id(),
                    'content'          => $request->content,
                    'commentable_id'   => $request->request_id,
                    'commentable_type' => 'App\Models\Request',
                ]);
            }
            

            $taggedUsers = RequestTag::where('request_id', $request->request_id)->pluck('user_id');

            foreach ($taggedUsers as $userId) {
                if ($userId != auth()->id()) {
                    RequestCommentView::create([
                        'comment_id' => $comment->id,
                        'user_id'            => $userId,
                        'viewed'             => false,
                    ]);
                }
            }

            return [
                'data' =>  $comment,
                'message' => 'Comment Submitted', 
                'info' => "Your comment has been submitted. Keep an eye for updates and replies."
            ];
        });

        return back()->with([
            'data' => $result['data'],
            'message' => $result['message'],
            'info' => $result['info'],
            'status' => $result['status'],
        ]);
    }
}
