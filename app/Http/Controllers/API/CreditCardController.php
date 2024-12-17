<?php

namespace App\Http\Controllers\API;

use Stripe\StripeClient;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreditCardRequest;
use App\Transformers\CreditCardTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreditCardController extends Controller
{
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $limit=request()->input('limit') ?? 20;
            $card = CreditCard::where('user_id', $request->user()->id)->get();
            $options = ['withProducts' => true, 'withMinimumData' => true];

            $products= $card->paginate($limit);
            $paginate = apiPagination($products, $limit);
            $cards = (new CreditCardTransformer)->transformCollection($products,$options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'All cards of logged in user.',
                'data' => $cards,
                'meta' => $paginate,
                
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreditCardRequest $request)
    {
        try{
            // attach payment method to customer
            $paymentAttach = $this->stripeClient->paymentMethods->attach(
                $request->payment_method_id,
                ['customer' => $request->user()->stripe_customer_id]
            );

            DB::beginTransaction();
            $request->merge([
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'customer_id' => $request->user()->stripe_customer_id,
            ]);
            $card = CreditCard::create($request->all());
            $card = (new CreditCardTransformer)->transform($card);
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Card Added successfully.',
                'data' => $card,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) { 
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $card = CreditCard::findOrFail($id);
            $cards = (new CreditCardTransformer)->transform($card);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Single Card',
                'data' => $cards,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $previousDefaultCard = CreditCard::where('user_id', $request->user()->id )->where('default', 1)->first();
            if ($previousDefaultCard) {
                $previousDefaultCard->update(['default' => 0]);
            }
            $card = CreditCard::findOrFail($id);
            $card->update(['default' => 1]);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Card marked as default successfully.',
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $card = CreditCard::findOrFail($id);
            //detaching the payment method from customer
            $paymentDetach = $this->stripeClient->paymentMethods->detach(
                $card->payment_method_id,
                []
            );
            //deleting card
            $card->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Card Deleted Successfully.',
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

    public function retrievePaymentMethod(Request $request) 
    {
        try {
            $paymentMethod = $this->stripeClient->paymentMethods->retrieve(
                $request->input('paymentMethodId'),
                []
            );
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'successful',
                'data' => $paymentMethod
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
