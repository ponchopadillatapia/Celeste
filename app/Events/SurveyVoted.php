<?php

namespace App\Events;

use App\Models\Survey;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SurveyVoted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $results;

    public function __construct(public Survey $survey)
    {
        // Cargar resultados en vivo
        $this->results = $survey->options()->withCount('votes')->get()->map(fn($o) => [
            'id' => $o->id,
            'option_text' => $o->option_text,
            'votes_count' => $o->votes_count,
        ])->toArray();
    }

    public function broadcastOn(): array
    {
        return [new Channel('survey.' . $this->survey->id)];
    }

    public function broadcastWith(): array
    {
        return [
            'survey_id' => $this->survey->id,
            'results' => $this->results,
        ];
    }
}
