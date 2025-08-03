<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    // public function search(Request $request)
    // {
    //     $_term = preg_replace('/[^a-zA-Z0-9\s]/', '', $request->input('q'));

    //     $term = $_term . "*";

    //     $posts = Post::selectRaw("*, MATCH(title, body) AGAINST(? IN BOOLEAN MODE) AS relevance", [$term])
    //         ->whereRaw("MATCH(title, body) AGAINST(? IN BOOLEAN MODE)", [$term])
    //         ->orderByDesc('relevance')
    //         ->get();
    //     foreach ($posts as $post) {
    //         $post->title = preg_replace("/($_term)/i", "<b>$1</b>", $post->title);
    //         $post->body = preg_replace("/($_term)/i", "<b>$1</b>", $post->body);
    //     }
    //     return response()->json($posts);
    // }


    public function search(Request $request)
    {
        // Get the raw search term from the query string: ?q=someword
        $_term = $request->input('q');

        // Clean the input to remove unwanted characters (e.g., symbols) for security and query safety
        $cleanTerm = preg_replace('/[^a-zA-Z0-9\s]/', '', $_term);

        // Add a wildcard * at the end to enable partial word matching in BOOLEAN MODE
        $wildcardTerm = $cleanTerm . '*';

        // Use MySQL full-text search with relevance scoring:
        // Title matches are weighted higher than body matches (* 2)
        $posts = Post::selectRaw("*, 
            (MATCH(title) AGAINST(? IN BOOLEAN MODE) * 2 + 
            MATCH(body) AGAINST(? IN BOOLEAN MODE)) AS relevance",
            [$wildcardTerm, $wildcardTerm]
        )
            // Only return rows where there is a match in either title or body
            ->whereRaw("MATCH(title, body) AGAINST(? IN BOOLEAN MODE)", [$wildcardTerm])
            // Order results by descending relevance
            ->orderByDesc('relevance')
            // Paginate the results (default 15 per page)
            ->paginate();

        // Escape the cleaned term for safe use in a regular expression
        $escapedTerm = preg_quote($cleanTerm, '/');

        // Loop through each result to highlight the matched terms in title and body
        foreach ($posts as $post) {
            // Wrap the matched term with <b>...</b> for emphasis
            $post->title = preg_replace("/($escapedTerm)/i", "<b>$1</b>", $post->title);
            $post->body = preg_replace("/($escapedTerm)/i", "<b>$1</b>", $post->body);
        }

        // Return the search results as JSON (including pagination metadata)
        return response()->json($posts);
    }

}
