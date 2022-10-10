<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommentService
{
    private CommentRepository $commentRepository;
    private TeacherRepository $teacherRepository;

    public function __construct(CommentRepository $commentRepository, TeacherRepository $teacherRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->teacherRepository = $teacherRepository;
    }

    public function save(array $data)
    {
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'rating' => 'required|numeric',
            'teacher_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            $validData = $validator->validated();
            $validData['user_account_id'] = Auth::user()->id;

            $comment = $this->commentRepository->create($validData);

            if (!$comment) {
                DB::rollBack();
                Log::error('Failed to save comment', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'Failed to save comment'], 500);
            }

            $teacher = $this->teacherRepository->findById($validData['teacher_id']);

            if (!$teacher) {
                DB::rollBack();
                Log::error('Failed to find teacher', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'Failed to find teacher'], 500);
            }

            $teacher->rating = $this->commentRepository->findAverageByTeacherId($teacher->id);
            $teacher->evaluations = $teacher->evaluations + 1;

            $teacher->save();

            DB::commit();

            return response()->json(['success' => true, 'data' => $comment], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save comment', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(array $data, int $commentId)
    {
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'rating' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            $validData = $validator->validated();

            $comment = $this->commentRepository->findById($commentId);

            if (!$comment) {
                DB::rollBack();
                Log::error('Failed to find comment', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'Failed to find comment'], 500);
            }

            if ($comment->user_account_id !== Auth::user()->id) {
                DB::rollBack();
                Log::error('User cannot update this comment', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'User cannot update this comment'], 401);
            }

            $comment->content = $validData['content'];
            $comment->rating = $validData['rating'];
            $comment->updated_at = now();
            $comment->save();

            $teacher = $this->teacherRepository->findById($comment->teacher_id);

            if (!$teacher) {
                DB::rollBack();
                Log::error('Failed to find teacher', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'Failed to find teacher'], 500);
            }

            $teacher->rating = $this->commentRepository->findAverageByTeacherId($teacher->id);

            $teacher->save();

            DB::commit();

            return response()->json(['success' => true, 'data' => $comment], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update comment', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(int $commentId)
    {
        try {
            DB::beginTransaction();

            $comment = $this->commentRepository->findById($commentId);

            if (!$comment) {
                DB::rollBack();
                Log::error('Failed to find comment', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'Failed to find comment'], 500);
            }

            if ($comment->user_account_id !== Auth::user()->id) {
                DB::rollBack();
                Log::error('User cannot delete this comment', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'User cannot delete this comment'], 401);
            }

            $this->commentRepository->delete($commentId);

            $teacher = $this->teacherRepository->findById($comment->teacher_id);

            if (!$teacher) {
                DB::rollBack();
                Log::error('Failed to find teacher', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['success' => false, 'message' => 'Failed to find teacher'], 500);
            }

            $teacher->rating = $this->commentRepository->findAverageByTeacherId($teacher->id) ?? 0;
            $teacher->evaluations = $teacher->evaluations - 1;

            $teacher->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete comment', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
