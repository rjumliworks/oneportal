<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EnsureSurveyIsCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $activeSurvey = Survey::where('is_active',1)->where('is_completed',0)->first();

        if ($activeSurvey) {
            $hasAnswered = SurveyAnswer::where('survey_id', $activeSurvey->id)
                ->where('user_id', $user->id)
                ->exists();

            Inertia::share(['surveyRequired' => !$hasAnswered]);
        } else {
            Inertia::share(['surveyRequired' => false]);
        }
        return $next($request);
    }
}
