<?php

namespace Modules\RealEstate\Http\Controllers\Dashboard\Property;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
        return Inertia::render('RealEstate::ContactForm/Index', [
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
        return view('realestate::create');
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
    public function show($moduleId, $businessUuid, $id)
    {
        try {
            $contactForm = ContactForm::findOrFail($id);
            return Inertia::render('RealEstate::ContactForm/Show', [
                'form' => $contactForm->load('user', 'product.mainImage'),
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this communication portal', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $businessUuid, $id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            if (auth()->user()->hasRole(['admin', 'newspaper', 'agent'])) {
                $contactForm = ContactForm::when(auth()->user()->hasRole('agent'), function ($query) {
                    $query->whereHas('product.business', function ($subQuery) {
                        $subQuery->where('owner_id', auth()->user()->id);
                    });
                })->findOrFail($id)->delete();
            }
            flash('Communication portal deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('real-estate.dashboard.broker.communication-portal.index', [$moduleId, $businessUuid, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this communication portal', 'danger');
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }
}
