<?php

namespace Modules\Automotive\Http\Controllers\Dashboard\Vehicle;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;
use Modules\Automotive\Entities\ContactForm;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class ContactFormController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $limit = \config()->get('settings.pagination_limit');
        $forms = ContactForm::with('user', 'product.mainImage')->where(function ($query) {
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
        })
            ->whereHas('product', function ($query) use ($moduleId, $businessUuid) {
                $query->whereRelation('business', 'uuid', '=', $businessUuid);
            })->latest()->paginate($limit);
        return Inertia::render('Automotive::ContactForm/Index', [
            'forms' => $forms,
            'searchedKeyword' => request()->keyword
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('automotive::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($moduleId, $businessUuid, $contactId)
    {
        $contactForm = ContactForm::findOrFail($contactId);
        return Inertia::render('Automotive::ContactForm/Show', [
            'form' => $contactForm->load('product.vehicle.maker', 'product.vehicle.model', 'product.vehicle.bodyType', 'user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('automotive::edit');
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
    public function destroy($moduleId, $businessUuid, ContactForm $contactForm,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $contactForm->delete();
            flash('Contact form deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('automotive.dashboard.dealership.contact-form.index', [$moduleId,$businessUuid,$contactForm->id, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this contact form', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
