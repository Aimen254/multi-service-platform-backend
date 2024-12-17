<?php

namespace Modules\Taskers\Http\Controllers\Dashboard\Taskers;

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
    public function index($moduleId)
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
        })->whereRelation('product.standardTags', 'slug', 'taskers')
            ->when(!auth()?->user()?->hasRole('admin') && !auth()?->user()?->hasRole('newspaper'), function ($query) use ($moduleId) {
                $query->whereHas('product', function ($subQuery) {
                    $subQuery->where('user_id', auth()->id());
                });
            })->latest()->paginate($limit);
        return Inertia::render('Taskers::ContactForm/Index', [
            'forms' => $forms,
            'searchedKeyword' => request()->keyword
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($moduleId, $contactId)
    {
        try {
            $contactForm = ContactForm::findOrFail($contactId);
            return Inertia::render('Taskers::ContactForm/Show', [
                'form' => $contactForm->load('user', 'product.mainImage'),
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this communication', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $id, Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $contactForm = ContactForm::findOrFail($id);
            $contactForm->delete();
            flash('Communication deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('taskers.dashboard.communication-portal.index', [$moduleId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this communication', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }
}
