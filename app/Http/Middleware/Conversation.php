<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Models\Conversation as ConversationModel;

class Conversation
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $conversation = ConversationModel::where('user_id', $user->id)
            ->orWhere('support_member', $user->id)
            ->where('closed', 'false')->first();
        if($conversation){
            return $next($request);
        }

        return $this->error('Unauthorized', 401);

    }
}   
