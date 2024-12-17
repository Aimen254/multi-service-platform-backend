<?php

namespace App\Http\Controllers\Admin\Subscription;

use Inertia\Inertia;
use App\Models\Setting;
use Stripe\StripeClient;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreditCardRequest;
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
        $limit = \config()->get('settings.pagination_limit');
        $cards = CreditCard::where(function ($query) {
            $query->where('user_id', request()->user()->id);
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('user_name', 'like', '%' . $keyword . '%')
                ->orWhere('brand', 'like', '%' . $keyword . '%')
                ->orWhere('last_four', 'like', '%' . $keyword . '%');
            }
        })->orderBy('id','desc')->paginate($limit);

        //stripe client id for initiallizing stripe on frontend
        $stripeKey = Setting::where('key', 'sandbox')->first()->value == 'no' ?  Setting::where('key', 'client_id_production')->first() : Setting::where('key', 'client_id_sandbox')->first();

        return Inertia::render('Subscription/Cards/Index', [
            'cards' => $cards,
            'stripeKey' => $stripeKey,
            'searchedKeyword' => request()->input('keyword') ? : null
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreditCardRequest $request)
    {
        try {
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
            CreditCard::create($request->all());
            DB::commit();
            flash('Card added successfully', 'success');
            return \redirect()->back();
        }catch (\Exception $e){
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        try{
            DB::beginTransaction();
            $previousDefaultCard = CreditCard::where('user_id', $request->user()->id )->where('default', 1)->first();
            if ($previousDefaultCard) {
                $previousDefaultCard->update(['default' => 0]);
            }
            $card = CreditCard::findOrFail($id);
            $card->update(['default' => 1]);
            DB::commit();
            flash('Card marked as default','success');
            return \redirect()->back();          
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this card', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
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
            flash('Card deleted succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Card', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
