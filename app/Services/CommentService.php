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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            $validData = $validator->validated();
            $validData['user_account_id'] = Auth::user()->id;

            $comment = $this->commentRepository->create($validData);

            if (!$comment) {
                DB::rollBack();
                Log::error('Erro ao salvar comentário', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Erro ao salvar comentário'], 500);
            }

            $teacher = $this->teacherRepository->findById($validData['teacher_id']);

            if (!$teacher) {
                DB::rollBack();
                Log::error('Erro ao buscar professor', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Erro ao buscar professor'], 500);
            }

            $teacher->rating = $this->commentRepository->findAverageByTeacherId($teacher->id);
            $teacher->evaluations = $teacher->evaluations + 1;

            $teacher->save();

            DB::commit();

            return response()->json($comment, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar comentário', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(array $data, int $commentId)
    {
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'rating' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            $validData = $validator->validated();

            $comment = $this->commentRepository->findById($commentId);

            if (!$comment) {
                DB::rollBack();
                Log::error('Erro ao encontrar comentário', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Erro ao buscar comentário'], 500);
            }

            if ($comment->user_account_id !== Auth::user()->id) {
                DB::rollBack();
                Log::error('Usuário não autorizado a atualizar comentário', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Usuário não autorizado a atualizar comentário'], 401);
            }

            $comment->content = $validData['content'];
            $comment->rating = $validData['rating'];
            $comment->updated_at = now();
            $comment->save();

            $teacher = $this->teacherRepository->findById($comment->teacher_id);

            if (!$teacher) {
                DB::rollBack();
                Log::error('Erro ao buscar professor', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Erro ao buscar professor'], 500);
            }

            $teacher->rating = $this->commentRepository->findAverageByTeacherId($teacher->id);

            $teacher->save();

            DB::commit();

            return response()->json($comment, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar comentário', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete(int $commentId)
    {
        try {
            DB::beginTransaction();

            $comment = $this->commentRepository->findById($commentId);

            if (!$comment) {
                DB::rollBack();
                Log::error('Erro ao encontrar comentário', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Erro ao buscar comentário'], 500);
            }

            if ($comment->user_account_id !== Auth::user()->id) {
                DB::rollBack();
                Log::error('Usuário não autorizado a deletar comentário', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Usuário não autorizado a deletar comentário'], 401);
            }

            $this->commentRepository->delete($commentId);

            $teacher = $this->teacherRepository->findById($comment->teacher_id);

            if (!$teacher) {
                DB::rollBack();
                Log::error('Erro ao buscar professor', [
                    'user' => Auth::user(),
                ]);

                return response()->json(['error' => 'Erro ao buscar professor'], 500);
            }

            $teacher->rating = $this->commentRepository->findAverageByTeacherId($teacher->id) ?? 0;
            $teacher->evaluations = $teacher->evaluations - 1;

            $teacher->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comentário deletado com sucesso',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar comentário', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
