<?php

namespace Modules\Employment\Http\Controllers\API;

use App\Models\Media;
use Exception;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Transformers\ResumeTransformer;
use Illuminate\Contracts\Support\Renderable;
use Modules\Employment\Http\Requests\ResumeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        try {
            $resumes = Resume::with('resumes')->where('user_id', auth('sanctum')->user()->id)->firstOrFail();
            $resumes = (new ResumeTransformer)->transform($resumes);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $resumes,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('employment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ResumeRequest $request)
    {
        try {
            $resume = Resume::updateOrCreate(
                [
                    'user_id' => auth('sanctum')->user()->id
                ],
                $request->validated()
            );

           if($request->file('resume')) {
                $userResume = $resume->resumes()->first();
                if ($userResume) {
                    deleteFile($userResume->path);
                }
                $extension = request()->resume->extension();
                $resumePath = uploadPdfFile(request()->resume, "resumes", $extension);
                $resume = $resume->resumes()->updateOrCreate(
                    [
                        'model_id' => $resume->id,
                        'model_type' => 'App\Models\Resume'
                    ],
                    [
                        'path' => $resumePath,
                        'size' => request()->file('resume')->getSize(),
                        'mime_type' => $extension,
                        'type' => 'resume'
                    ]
                );
           }
            $resume = (new ResumeTransformer)->transform($resume);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Resume Created successfully!',
                'data' => $resume,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('employment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('employment::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            Media::findOrFail($id)->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Resume deleted successfully!'
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
