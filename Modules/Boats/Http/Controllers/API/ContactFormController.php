<?php

namespace Modules\Boats\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Events\ContactFormProcessed;
use App\Transformers\ContactFormTransformer;
use Illuminate\Contracts\Support\Renderable;
use Modules\Automotive\Entities\ContactForm;
use App\Traits\PushNotifications;
use Modules\Boats\Http\Requests\ContactFormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContactFormController extends Controller
{
    use PushNotifications;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
        $contactForm = ContactForm::with('product', 'user')
            ->whereHas('product', function ($query) {
                $query->whereHas('standardTags', function ($subQuery) {
                    $subQuery->where('slug', 'boats');
                })->when(request()->filled('businessUuid'), function ($query) {
                    $query->whereRelation('business', 'uuid', '=', request()->input('businessUuid'));
                });
            })
            ->whereRelation('user', 'id', '=', auth('sanctum')->user()->id ?? null)
            ->latest()
            ->paginate($limit);
        $paginate = apiPagination($contactForm, $limit);
        $forms = (new ContactFormTransformer)->transformCollection($contactForm);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $forms,
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('boats::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ContactFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $contactForm = ContactForm::create($request->all());
            event(new ContactFormProcessed($contactForm));
            DB::commit();
            $this->contactFormNotification($contactForm);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Contact Form Added Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
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
    public function show(ContactForm $contactForm)
    {
        try {
            $form = (new ContactFormTransformer)->transform($contactForm->load('product', 'user'));
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $form,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('boats::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(ContactFormRequest $request, ContactForm $contactForm)
    {
        try {
            DB::beginTransaction();
            $contactForm->update($request->all());
            event(new ContactFormProcessed($contactForm));
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Contact Form updated Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(ContactForm $contactForm)
    {
        try {
            $contactForm->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Contact Form Deleted Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
