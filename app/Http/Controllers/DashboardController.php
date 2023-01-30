<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveyAnswerResource;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyResourceDashboard;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;
use App\Models\Survey;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        //Total no of surveys
        $totalSurvey = Survey::query()->where('user_id', $user->id)->count();

        //latest survey
        $latestSurvey = Survey::query()->where('user_id', $user->id)->latest('created_at')->first();

        //Total number of answers
        $totalAnswers = SurveyAnswer::query()
            ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
            ->where('surveys.user_id', $user->id)
            ->count();

        //latest 5 answer
        $latestAnswers = SurveyAnswer::query()
            ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
            ->where('surveys.user_id', $user->id)
            ->orderBy('end_date', 'DESC')
            ->limit(5)
            ->getModels('survey_answers.*');
        
        //return an associative array
        return [
            'totalSurvey' => $totalSurvey,
            'latestSurvey' => $latestSurvey ? new SurveyResourceDashboard($latestSurvey) : null,
            'totalAnswers' => $totalAnswers,
            'latestAnswers' => SurveyAnswerResource::collection($latestAnswers)
        ];
    }
}
