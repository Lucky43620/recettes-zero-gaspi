<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $activeEvents = Event::active()->latest()->get();
        $upcomingEvents = Event::upcoming()->latest()->get();
        $endedEvents = Event::ended()->latest()->limit(5)->get();

        return Inertia::render('Event/Index', [
            'activeEvents' => $activeEvents,
            'upcomingEvents' => $upcomingEvents,
            'endedEvents' => $endedEvents,
        ]);
    }

    public function show(Event $event)
    {
        $leaderboard = DB::table('event_participants')
            ->join('users', 'event_participants.user_id', '=', 'users.id')
            ->leftJoin('recipes', 'event_participants.recipe_id', '=', 'recipes.id')
            ->where('event_participants.event_id', $event->id)
            ->select('users.id', 'users.name', 'users.profile_photo_path', 'event_participants.score', 'recipes.title as recipe_title', 'recipes.slug as recipe_slug')
            ->orderByDesc('event_participants.score')
            ->limit(10)
            ->get();

        $userParticipation = null;
        if (Auth::check()) {
            $userParticipation = DB::table('event_participants')
                ->where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return Inertia::render('Event/Show', [
            'event' => $event,
            'leaderboard' => $leaderboard,
            'userParticipation' => $userParticipation,
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Événement créé avec succès');
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $event->update($request->validated());

        return back()->with('success', 'Événement mis à jour');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Événement supprimé');
    }

    public function join(JoinEventRequest $request, Event $event)
    {
        $event->participants()->syncWithoutDetaching([
            Auth::id() => [
                'recipe_id' => $request->validated('recipe_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        return back()->with('success', 'Inscription confirmée');
    }

    public function leave(Event $event)
    {
        $event->participants()->detach(Auth::id());

        return back()->with('success', 'Désinscription confirmée');
    }
}
