<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\CreditCard;
use Stripe\StripeClient;
use Carbon\Carbon;
use App\Events\CardExpiryStatus;
use App\Events\OrderStatusEmail;

class CheckCardExpiry extends Command
{

    protected StripeClient $stripeClient;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:cardExpiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is made to check customer credit card expiry date.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        try {
            $cards = CreditCard::all();
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            // Updating and checking all cards.
            foreach ($cards as $key => $card) {
                $customer = $card->user;
                $last_four_digits = $card->last_four;
                $paymentMethod = $this->stripeClient->paymentMethods->retrieve(
                    $card->payment_method_id,
                    []
                );
                //card expiry month and year
                $cardExpiryMonth = $paymentMethod['card']['exp_month'];
                $cardExpiryYear = $paymentMethod['card']['exp_year'];
                //Update card
                $card->update([
                    'expiry_month' => $cardExpiryMonth,
                    'expiry_year' => $cardExpiryYear
                ]);
    
                //send email according to expiry date 
                if ($cardExpiryYear < $currentYear) {
                    event(new CardExpiryStatus($last_four_digits, 'card_expired', $customer));
                    $this->deleteCard($card);
                } elseif ( $cardExpiryYear == $currentYear) {
                    if ($cardExpiryMonth < $currentMonth) {
                        event(new CardExpiryStatus($last_four_digits, 'card_expired', $customer));
                        $this->deleteCard($card);
                    } elseif ($cardExpiryMonth == $currentMonth) {
                        event(new CardExpiryStatus($last_four_digits, 'card_expiring_this_month', $customer));
                    } else {
                        event(new CardExpiryStatus($last_four_digits, 'card_updated', $customer));
                    }
                } else {
                    event(new CardExpiryStatus($last_four_digits, 'card_updated', $customer));
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
        return 0;
    }

    private function deleteCard($card) 
    {
        //detaching the payment method from customer
        $paymentDetach = $this->stripeClient->paymentMethods->detach(
            $card->payment_method_id,
            []
        );
        //deleting card
        $card->delete();
        return;
    }
}
