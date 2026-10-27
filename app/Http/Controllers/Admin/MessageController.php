<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The contact inbox.
 *
 * This screen is not one of the twelve the design covers; without it the
 * messages the contact form stores would be unreachable, so the form
 * would collect personal data nobody could read or delete.
 */
class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('statut');

        $messages = ContactMessage::query()
            ->with('handler')
            ->when($status === 'nouveaux', fn ($q) => $q->unhandled())
            ->when($status === 'traites', fn ($q) => $q->where('status', ContactMessage::STATUS_HANDLED))
            ->when($request->query('sujet'), fn ($q, $subject) => $q->where('subject', $subject))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('message', 'like', "%{$term}%");
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.messages', [
            'messages' => $messages,
            'subjects' => ContactRequest::SUBJECTS,
            'counts' => [
                'new' => ContactMessage::unhandled()->count(),
                'handled' => ContactMessage::where('status', ContactMessage::STATUS_HANDLED)->count(),
                'week' => ContactMessage::where('created_at', '>=', now()->subWeek())->count(),
                'all' => ContactMessage::count(),
            ],
            'filters' => $request->only(['statut', 'sujet', 'q']),
        ]);
    }

    public function handle(Request $request, ContactMessage $message): RedirectResponse
    {
        $message->markHandled($request->user());

        return back()->with('status', "Le message de {$message->name} est marqué comme résolu.");
    }

    /**
     * Permanent, and the only way one of these rows leaves: they hold
     * personal data a visitor typed in, so there is no corbeille to leave
     * them sitting in.
     */
    public function destroy(ContactMessage $message): RedirectResponse
    {
        $name = $message->name;
        $message->delete();

        return back()->with('status', "Le message de {$name} a été supprimé définitivement.");
    }
}
