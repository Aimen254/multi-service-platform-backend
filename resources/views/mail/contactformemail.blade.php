@component('mail::message')
<div style="padding:4%; border-radius:4px">
    <h1 style="text-align: center; text-transform: capitalize;">{{ str_replace('_', ' ', $contact->subject). ' For '. $product->name}}</h1>
    <div style="text-align: center; font-weight:600">({{$contact->first_name. ' ' . $contact->last_name}}) Add a query.</div>
</div>
<div style="padding: 3%; margin:2%; border: 1px solid #eee">
    <h1 style="margin-bottom:2%">Customer Details</h1>
    <div style="display: flex; justify-content:space-between">
        <div>Name:</div>
        <div>{{$contact->first_name. ' ' . $contact->last_name}}</div>
    </div>
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>Email:</div>
        <div>{{$contact->email}}</div>
    </div>
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>Phone:</div>
        <div>{{$contact->phone}}</div>
    </div>
</div>
<div style="padding: 3%; margin:2%; border: 1px solid #eee">
    <h1 style="margin-bottom:2%">Query Detail</h1>
    <div style="display: flex; justify-content:space-between">
        <div>Subject:</div>
        <div style="text-transform: capitalize;">{{str_replace('_', ' ', $contact->subject). ' For '. $product->name}}</div>
    </div>
    @if ($module == "services" || $module == 'taskers')
    <div style="display: flex; justify-content:space-between">
        <div>Urgent:</div>
        <div style="text-transform: capitalize;">{{ $contact->is_urgent ? 'Yes' : 'No' }}</div>
    </div>
    @endif
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>Comment:</div>
        <div>{{$contact->comment}}</div>
    </div>
    @if ($module != "taskers" && $module != "services")
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>Trade In:</div>
        <div>{{$contact->trade_in}}</div>
    </div>
    @endif
</div>
@endcomponent