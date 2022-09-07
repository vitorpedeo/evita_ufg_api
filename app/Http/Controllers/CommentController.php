<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    private CommentService $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function save(Request $request)
    {
        return $this->service->save([
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'teacher_id' => $request->input('teacher_id'),
        ]);
    }

    public function update(Request $request, int $commentId)
    {
        return $this->service->update([
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
        ], $commentId);
    }

    public function delete(int $commentId)
    {
        return $this->service->delete($commentId);
    }
}
