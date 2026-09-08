<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;

/** Every sent newsletter with show_on_blog=true also serves as a public blog post. */
class BlogController extends Controller
{
    public function index()
    {
        return view('blog.index', [
            'posts' => Newsletter::publishedOnBlog()->latest('sent_at')->paginate(9),
        ]);
    }

    public function show(Newsletter $newsletter)
    {
        abort_unless($newsletter->show_on_blog && $newsletter->sent_at, 404);

        return view('blog.show', [
            'post' => $newsletter,
            'related' => Newsletter::publishedOnBlog()->where('id', '!=', $newsletter->id)->latest('sent_at')->limit(3)->get(),
        ]);
    }
}
