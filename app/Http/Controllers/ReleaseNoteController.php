<?php

namespace App\Http\Controllers;

use App\Models\ReleaseNote;
use Illuminate\Support\Facades\Gate;

class ReleaseNoteController extends Controller
{
    private $gateHandler = 'release-notes';
    private $viewDirectory = 'pages.release_notes.';

    public function index()
    {
        Gate::authorize('view', $this->gateHandler);

        $listingData = ReleaseNote::where(function ($q) {
            if (request()->search) {
                $q->where(function ($q) {
                    $q->where('title', 'like', '%' . request()->search . '%')
                        ->orWhere('description', 'like', '%' . request()->search . '%');
                });
            }

            if (request()->version) {
                $q->where('version', 'like', '%' . request()->version . '%');
            }

            if (request()->release_date) {
                $q->whereDate('release_date', request()->release_date);
            }
        })
        ->orderByDesc('release_date')
        ->orderByDesc('created_at')
        ->paginate(50)
        ->withQueryString();

        return view($this->viewDirectory . 'release_notes_view', [
            'listingData' => $listingData,
            'module' => $this->gateHandler,
        ]);
    }

    public function showForm($placeholder = null)
    {
        if ($placeholder) {
            Gate::authorize('update', $this->gateHandler);
            $formData = ReleaseNote::findOrFail(decrypt($placeholder));
        } else {
            Gate::authorize('create', $this->gateHandler);
            $formData = null;
        }

        return view($this->viewDirectory . 'manage_release_note', [
            'formData' => $formData,
            'module' => $this->gateHandler,
        ]);
    }

    public function manage($placeholder = null)
    {
        $id = $placeholder ? decrypt($placeholder) : null;

        request()->validate([
            'title' => 'required|string',
            'version' => 'required|string|max:100',
            'type' => 'required|in:1,2,3',
            'description' => 'required|string',
            'status' => 'required|in:0,1',
            'release_date' => 'required|date',
        ]);

        if ($id) {
            Gate::authorize('update', $this->gateHandler);
            $releaseNote = ReleaseNote::findOrFail($id);
            $wasPublished = $releaseNote->status == 1;
        } else {
            Gate::authorize('create', $this->gateHandler);
            $releaseNote = new ReleaseNote();
            $wasPublished = false;
        }

        $releaseNote->title = request()->title;
        $releaseNote->version = request()->version;
        $releaseNote->type = request()->type;
        $releaseNote->description = request()->description;
        $releaseNote->status = request()->status;
        $releaseNote->release_date = request()->release_date;

        if (request()->status == 1 && !$wasPublished) {
            $releaseNote->published_at = now();
        }

        if (request()->status == 0) {
            $releaseNote->published_at = null;
        }

        $res = $releaseNote->save();

        $this->setFlashSession($res, $id ? 'Release note updated successfully.' : 'Release note created successfully.');

        return redirect()->route('index-release-notes');
    }

    public function toggleStatus($placeholder)
    {
        Gate::authorize('update', $this->gateHandler);

        $releaseNote = ReleaseNote::findOrFail(decrypt($placeholder));
        $releaseNote->status = $releaseNote->status ? 0 : 1;
        $releaseNote->published_at = $releaseNote->status ? now() : null;

        $this->setFlashSession($releaseNote->save(), 'Release note status updated successfully.');

        return redirect()->route('index-release-notes');
    }

    public function delete($placeholder)
    {
        Gate::authorize('delete', $this->gateHandler);

        $releaseNote = ReleaseNote::findOrFail(decrypt($placeholder));

        return $this->generateJsonResponse($releaseNote->delete(), 'Release note deleted successfully.');
    }
}
