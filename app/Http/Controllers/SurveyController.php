<?php

namespace App\Http\Controllers;

use App\Events\SurveyVoted;
use App\Models\Survey;
use App\Models\SurveyOption;
use App\Models\SurveyVote;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * Listar encuestas activas.
     */
    public function index()
    {
        $surveys = Survey::where('is_active', true)
            ->with(['options' => function ($q) {
                $q->withCount('votes');
            }, 'creator:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($surveys);
    }

    /**
     * Ver detalle de encuesta con resultados en vivo.
     */
    public function show(Survey $survey)
    {
        $survey->load(['options' => function ($q) {
            $q->withCount('votes');
        }, 'creator:id,name']);

        $totalVotes = $survey->votes()->count();

        return response()->json([
            'survey' => $survey,
            'total_votes' => $totalVotes,
        ]);
    }

    /**
     * Crear encuesta (solo admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $survey = Survey::create([
            'created_by' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        foreach ($request->options as $optionText) {
            $survey->options()->create(['option_text' => $optionText]);
        }

        $survey->load('options');

        return response()->json($survey, 201);
    }

    /**
     * Votar en una encuesta (WebSocket en vivo).
     */
    public function vote(Request $request, Survey $survey)
    {
        $request->validate([
            'option_id' => 'required|exists:survey_options,id',
        ]);

        if (!$survey->is_active) {
            return response()->json(['message' => 'La encuesta ya no está activa.'], 422);
        }

        // Verificar que no haya votado ya
        $existing = SurveyVote::where('survey_id', $survey->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Ya votaste en esta encuesta.'], 422);
        }

        // Verificar que la opción pertenece a la encuesta
        $option = SurveyOption::where('id', $request->option_id)
            ->where('survey_id', $survey->id)
            ->firstOrFail();

        SurveyVote::create([
            'survey_id' => $survey->id,
            'survey_option_id' => $option->id,
            'user_id' => $request->user()->id,
        ]);

        // Emitir resultados actualizados por WebSocket
        try {
            broadcast(new SurveyVoted($survey));
        } catch (\Exception $e) {
            // WebSocket no disponible
        }

        return response()->json(['message' => 'Voto registrado.']);
    }

    /**
     * Cerrar encuesta (solo admin).
     */
    public function close(Survey $survey)
    {
        $survey->update(['is_active' => false]);

        return response()->json(['message' => 'Encuesta cerrada.']);
    }
}
