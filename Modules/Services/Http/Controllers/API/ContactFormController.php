<?php

namespace Modules\Services\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Events\ContactFormProcessed;
use App\Transformers\ContactFormTransformer;
use Illuminate\Contracts\Support\Renderable;
use Modules\Automotive\Entities\ContactForm;
use App\Traits\PushNotifications;
use Modules\Services\Http\Requests\ContactFormRequest;
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
        return view('services::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('services::create');
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
    public function show($id)
    {
        return view('services::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('services::edit');
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
        //
    }
}
