<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Traits\PushNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Events\ContactFormProcessed;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StandardTag;
use App\Transformers\ContactFormTransformer;
use Modules\Automotive\Entities\ContactForm;
use Modules\Boats\Http\Requests\ContactFormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContactFormController extends Controller
{
    use PushNotifications;

    public function index(Request $request, $module)
    {
        $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
        $limit = \config()->get('settings.pagination_limit');
        $module = StandardTag::where('id', $module)->orWhere('slug', $module)->firstOrFail();
        $forms = ContactForm::where('user_id', '<>', auth()->user()->id)->with('user', 'product.mainImage')->where(function ($query) {
            $keyword = request()->keyword;
            $query->where('first_name', 'like', '%' . $keyword . '%')
                ->orWhere('last_name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->orWhere('subject', 'like', '%' . $keyword . '%')
                ->orWhere('comment', 'like', '%' . $keyword . '%')
                ->orWhereHas('product', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
        })->whereHas('product', function ($query) use ($module) {
            $query->where('user_id', auth()->user()->id);
            $query->whereRelation('standardTags', 'id', $module->id);
        })->latest()->paginate($limit);
        $paginate = apiPagination($forms, $limit);
        $forms = (new ContactFormTransformer)->transformCollection($forms);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $forms,
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    public function store(ContactFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = Product::find($request->product_id);
            $productBusiness = $product->business;
            $productUser = $product->user;
            $user = $productBusiness ? $productBusiness->businessOwner : null;
            if ($productUser && $productUser->id === auth()->id() || $user && $user->id === auth()->id()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'You cannot contact on your own product.',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            $contactForm = ContactForm::create($request->all());
            event(new ContactFormProcessed($contactForm, $request->module));
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

    public function destroy($module, $id)
    {
        try {
            ContactForm::findOrFail($id)->delete();
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
