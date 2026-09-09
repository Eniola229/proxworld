<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Jobs\SendNewsletterCampaign;
use App\Models\Newsletter;
use App\Types\NewsletterAudience;
use App\Types\NewsletterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.newsletters.index', ['newsletters' => Newsletter::latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.newsletters.create', ['audiences' => NewsletterAudience::labels()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'featured_image_url' => ['nullable', 'url'],
            'featured_video_url' => ['nullable', 'url'],
            'audience' => ['required', 'string', 'in:'.implode(',', NewsletterAudience::all())],
            'show_on_blog' => ['sometimes', 'boolean'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'action' => ['required', 'in:draft,send_now,schedule'],
        ]);

        $newsletter = Newsletter::create([
            'subject' => $data['subject'],
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'],
            'featured_image_url' => $data['featured_image_url'] ?? null,
            'featured_video_url' => $data['featured_video_url'] ?? null,
            'audience' => $data['audience'],
            'show_on_blog' => $request->boolean('show_on_blog', true),
            'status' => match ($data['action']) {
                'send_now' => NewsletterStatus::SENDING,
                'schedule' => NewsletterStatus::SCHEDULED,
                default => NewsletterStatus::DRAFT,
            },
            'scheduled_at' => $data['action'] === 'schedule' ? $data['scheduled_at'] : null,
            'created_by' => $request->user('admin')->id,
        ]);

        if ($data['action'] === 'send_now') {
            SendNewsletterCampaign::dispatch($newsletter->id);
        }

        return redirect()->route('admin.newsletters.index')->with('success', 'Newsletter saved.');
    }

    public function show(Newsletter $newsletter)
    {
        return view('admin.newsletters.show', compact('newsletter'));
    }

    public function edit(Newsletter $newsletter)
    {
        return view('admin.newsletters.edit', [
            'newsletter' => $newsletter,
            'audiences' => NewsletterAudience::labels(),
        ]);
    }

    public function update(Request $request, Newsletter $newsletter)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'featured_image_url' => ['nullable', 'url'],
            'featured_video_url' => ['nullable', 'url'],
            'audience' => ['required', 'string', 'in:'.implode(',', NewsletterAudience::all())],
            'show_on_blog' => ['sometimes', 'boolean'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'action' => ['required', 'in:draft,send_now,schedule'],
        ]);

        $status = match ($data['action']) {
            'send_now' => NewsletterStatus::SENDING,
            'schedule' => NewsletterStatus::SCHEDULED,
            default => NewsletterStatus::DRAFT,
        };

        $newsletter->update([
            'subject' => $data['subject'],
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'],
            'featured_image_url' => $data['featured_image_url'] ?? null,
            'featured_video_url' => $data['featured_video_url'] ?? null,
            'audience' => $data['audience'],
            'show_on_blog' => $request->boolean('show_on_blog', true),
            'status' => $status,
            'scheduled_at' => $data['action'] === 'schedule' ? $data['scheduled_at'] : null,
        ]);

        if ($data['action'] === 'send_now') {
            SendNewsletterCampaign::dispatch($newsletter->id);
        }

        return redirect()->route('admin.newsletters.index')->with('success', 'Newsletter updated successfully.');
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return redirect()->route('admin.newsletters.index')->with('success', 'Newsletter deleted.');
    }

    public function send(Newsletter $newsletter)
    {
        $newsletter->update(['status' => NewsletterStatus::SENDING]);
        SendNewsletterCampaign::dispatch($newsletter->id);

        return back()->with('success', 'Sending started — recipients will receive it shortly (queued).');
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200|mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm',
        ]);

        $file    = $request->file('file');
        $isVideo = str_starts_with($file->getMimeType(), 'video/');

        /** @var \App\Services\CloudinaryService $cloudinary */
        $cloudinary = app(\App\Services\CloudinaryService::class);

        $result = $isVideo
            ? $cloudinary->uploadVideo($file, 'proxworld/newsletters')
            : $cloudinary->uploadImage($file, 'proxworld/newsletters');

        return response()->json([
            'url'      => $result['url'],
            'location' => $result['url'],
        ]);
    }
}