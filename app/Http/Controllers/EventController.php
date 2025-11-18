<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
use App\Services\SettingsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private EventService $eventService,
        private SettingsService $settings
    ) {}

    public function index()
    {
        if (!$this->settings->get('enable_events', true)) {
            return redirect()->route('dashboard')->with('error', 'Les événements sont actuellement désactivés par l\'administrateur.');
        }

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
        $leaderboard = $this->eventService->getLeaderboard($event, 10);

        $userParticipation = null;
        if (Auth::check()) {
            $userParticipation = $this->eventService->getUserParticipation($event, Auth::id());
        }

        return Inertia::render('Event/Show', [
            'event' => $event,
            'leaderboard' => $leaderboard,
            'userParticipation' => $userParticipation,
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        if (!$this->settings->get('enable_events', true)) {
            return back()->with('error', 'Les événements sont actuellement désactivés par l\'administrateur.');
        }

        $this->authorize('create', Event::class);

        $event = Event::create($request->validated());

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Événement créé avec succès');
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        return back()->with('success', 'Événement mis à jour');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Événement supprimé');
    }

    public function join(JoinEventRequest $request, Event $event)
    {
        if (!$this->settings->get('enable_events', true)) {
            return back()->with('error', 'Les événements sont actuellement désactivés par l\'administrateur.');
        }

        $this->authorize('join', $event);

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
        $this->authorize('leave', $event);

        $event->participants()->detach(Auth::id());

        return back()->with('success', 'Désinscription confirmée');
    }
}
