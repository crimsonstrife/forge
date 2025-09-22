<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Response;

final class IssueActionItemController extends Controller
{
    public function toggle(Request $request, Issue $issue): Response
    {
        $this->authorize('update', $issue);

        $data = $request->validate([
            'listId'  => 'required|string',
            'itemId'  => 'required|string',
            'checked' => 'required|boolean',
        ]);

        $html = '<div id="root">'.($issue->description ?? '').'</div>';
        $dom  = new DOMDocument('1.0','UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xp = new DOMXPath($dom);
        foreach ($xp->query('//li[@data-ai-id="'.$data['itemId'].'"]') as $li) {
            $li->setAttribute('data-ai-checked', $data['checked'] ? 'true' : 'false');
        }

        $root = $dom->getElementById('root'); $new = '';
        foreach ($root->childNodes as $n) {
            $new .= $dom->saveHTML($n);
        }
        $issue->description = $new;
        $issue->save();

        return response()->noContent();
    }
}
