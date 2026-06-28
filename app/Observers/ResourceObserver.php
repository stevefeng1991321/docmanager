<?php

namespace App\Observers;

use App\Jobs\IndexDocumentTfidf;
use App\Models\Resource;
use App\Models\ResourceEmbedding;

class ResourceObserver
{
    public function updated(Resource $resource): void
    {
        // content changes come through updateQuietly() in ExtractDocumentContent (bypasses observers)
        // and that job already dispatches IndexDocumentTfidf itself — no double-dispatch needed.
        // We only need to react to title/description edits made via the admin form.
        if ($resource->wasChanged(['title', 'description']) && $resource->content) {
            IndexDocumentTfidf::dispatch($resource);
        }
    }

    public function deleted(Resource $resource): void
    {
        ResourceEmbedding::where('resource_id', $resource->id)->delete();
    }

    public function restored(Resource $resource): void
    {
        if ($resource->content) {
            IndexDocumentTfidf::dispatch($resource);
        }
    }

    public function forceDeleted(Resource $resource): void
    {
        ResourceEmbedding::where('resource_id', $resource->id)->delete();
    }
}
