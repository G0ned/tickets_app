<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\InvitationList;
use App\Models\VerificationCode;

class EditionInvitationCodesController extends Controller
{
    /**
     * Tabla de seguimiento de una edición: para cada cliente invitado, los
     * códigos de verificación generados y enviados (y si ya se han usado).
     *
     * El administrador ve las listas de todos los comerciales; un comercial
     * solo ve las de sus propias carteras y debe gestionar la edición.
     */
    public function index(Edition $edition)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        abort_unless(
            $isAdmin || $edition->managers()->where('manager_id', $user->id)->exists(),
            403
        );

        $edition->load(['event' => fn ($query) => $query->withTrashed()]);

        $lists = InvitationList::where('edition_id', $edition->id)
            ->whereNotNull('sent_at')
            ->when(! $isAdmin, fn ($query) => $query->whereIn(
                'client_portfolio_id',
                $user->portfolios()->select('id')
            ))
            ->with([
                'clientPorfolio.user',
                'persons',
            ])
            ->orderBy('sent_at')
            ->get();

        $codesByListAndPerson = VerificationCode::whereIn('invitation_list_id', $lists->pluck('id'))
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($code) => $code->invitation_list_id . '-' . $code->person_id);

        $rows = $lists->flatMap(function (InvitationList $list) use ($codesByListAndPerson) {
            return $list->persons->map(fn ($person) => [
                'list'     => $list,
                'person'   => $person,
                'salesrep' => $list->clientPorfolio?->user,
                'codes'    => $codesByListAndPerson->get($list->id . '-' . $person->id, collect()),
            ]);
        })->values();

        return view('editions.invitation-codes', compact('edition', 'rows', 'isAdmin'));
    }
}
